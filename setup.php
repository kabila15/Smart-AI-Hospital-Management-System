<?php
/**
 * Automated Database and Schema Setup Script
 * For Hospital Management System (MABS)
 */

header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'myhmsdb';

echo "=== Hospital Management System Database Setup ===\n\n";

// 1. Establish Connection to MySQL
echo "Connecting to MySQL server at {$host}...\n";
$con = mysqli_connect($host, $user, $pass);
if (!$con) {
    die("ERROR: Connection failed: " . mysqli_connect_error() . "\n" .
        "Please ensure that the XAMPP MySQL database server is running.\n");
}
echo "Connected successfully!\n\n";

// 2. Create database if it does not exist
echo "Checking/creating database '{$dbName}'...\n";
$db_check = mysqli_query($con, "CREATE DATABASE IF NOT EXISTS `{$dbName}`");
if (!$db_check) {
    die("ERROR: Failed to create database: " . mysqli_error($con) . "\n");
}
echo "Database '{$dbName}' ready!\n\n";

// Select the database
if (!mysqli_select_db($con, $dbName)) {
    die("ERROR: Could not select database '{$dbName}': " . mysqli_error($con) . "\n");
}

// 3. Import myhmsdb.sql if tables are empty
$table_check = mysqli_query($con, "SHOW TABLES");
$table_count = mysqli_num_rows($table_check);

if ($table_count === 0) {
    echo "No tables found. Importing base schema from myhmsdb.sql...\n";
    if (file_exists('myhmsdb.sql')) {
        $sql = file_get_contents('myhmsdb.sql');
        
        // Execute multi-query
        if (mysqli_multi_query($con, $sql)) {
            do {
                if ($result = mysqli_store_result($con)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($con));
            echo "Base schema imported successfully!\n\n";
        } else {
            echo "WARNING: Error importing base schema: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "WARNING: myhmsdb.sql file not found in current directory. Skipping base import.\n\n";
    }
} else {
    echo "Database already has tables. Skipping base schema import.\n\n";
}

// Reconnect to clear any pending multi-query state
mysqli_close($con);
$con = mysqli_connect($host, $user, $pass, $dbName);

// 4. Run Migration: create_tables.php / database_update.sql
echo "Running migration: Creating schedule and holiday tables...\n";
$sql_sched = "
CREATE TABLE IF NOT EXISTS `doctor_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_name` varchar(50) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
if (mysqli_query($con, $sql_sched)) {
    echo "  - Table doctor_schedule ready!\n";
} else {
    echo "  - Error creating doctor_schedule: " . mysqli_error($con) . "\n";
}

$sql_hol = "
CREATE TABLE IF NOT EXISTS `doctor_holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_name` varchar(50) NOT NULL,
  `holiday_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
if (mysqli_query($con, $sql_hol)) {
    echo "  - Table doctor_holidays ready!\n";
} else {
    echo "  - Error creating doctor_holidays: " . mysqli_error($con) . "\n";
}

// 5. Run Migration: token_system_migration.sql
echo "\nRunning migration: Token System Schema Updates...\n";
$alterations = [
    "ALTER TABLE `appointmenttb` ADD COLUMN `token_no` INT DEFAULT NULL",
    "ALTER TABLE `appointmenttb` ADD COLUMN `expected_time` TIME DEFAULT NULL",
    "ALTER TABLE `appointmenttb` ADD COLUMN `arrival_status` INT DEFAULT 0 COMMENT '0=Pending, 1=Arrived, 2=Late'",
    "ALTER TABLE `appointmenttb` ADD COLUMN `serving_status` INT DEFAULT 0 COMMENT '0=Waiting, 1=Serving, 2=Completed, 3=Skipped'",
    "ALTER TABLE `doctor_schedule` ADD COLUMN `avg_consult_time` INT DEFAULT 15",
    "ALTER TABLE `doctor_schedule` ADD COLUMN `session_capacity` INT DEFAULT 12"
];

foreach ($alterations as $query) {
    if (mysqli_query($con, $query)) {
        echo "  - Success: " . substr($query, 0, 50) . "...\n";
    } else {
        $err = mysqli_error($con);
        if (strpos($err, 'Duplicate column name') !== false) {
            echo "  - Already applied: " . substr($query, 0, 50) . "...\n";
        } else {
            echo "  - Error: " . $err . "\n";
        }
    }
}

// 6. Run Migration: leave_migration.php
echo "\nRunning migration: Creating doctor_leaves table...\n";
$sql_leaves = "
CREATE TABLE IF NOT EXISTS `doctor_leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor` varchar(50) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type` varchar(20) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
if (mysqli_query($con, $sql_leaves)) {
    echo "  - Table doctor_leaves ready!\n";
} else {
    echo "  - Error: " . mysqli_error($con) . "\n";
}

// 7. Run Migration: session_migration.php
echo "\nRunning migration: Adding Session columns & generating evening sessions...\n";
$session_alters = [
    "ALTER TABLE appointmenttb ADD COLUMN session_type VARCHAR(20) DEFAULT 'Morning'",
    "ALTER TABLE doctor_schedule ADD COLUMN session_type VARCHAR(20) DEFAULT 'Morning'"
];

foreach ($session_alters as $query) {
    if (mysqli_query($con, $query)) {
        echo "  - Success: " . substr($query, 0, 50) . "...\n";
    } else {
        $err = mysqli_error($con);
        if (strpos($err, 'Duplicate column name') !== false) {
            echo "  - Already applied: " . substr($query, 0, 50) . "...\n";
        } else {
            echo "  - Error: " . $err . "\n";
        }
    }
}

// Generate evening shifts for existing doctors
$fetch_docs = mysqli_query($con, "SELECT DISTINCT doctor_name FROM doctor_schedule");
if ($fetch_docs) {
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $added_shifts = 0;
    while ($row = mysqli_fetch_assoc($fetch_docs)) {
        $doc = $row['doctor_name'];
        foreach ($days as $day) {
            $check = mysqli_query($con, "SELECT id FROM doctor_schedule WHERE doctor_name='$doc' AND day_of_week='$day' AND session_type='Evening'");
            if (mysqli_num_rows($check) == 0) {
                $ins = mysqli_query($con, "INSERT INTO doctor_schedule(doctor_name, day_of_week, start_time, end_time, avg_consult_time, session_capacity, session_type) 
                                    VALUES('$doc', '$day', '17:00:00', '20:00:00', 15, 12, 'Evening')");
                if ($ins) {
                    $added_shifts++;
                }
            }
        }
    }
    echo "  - Generated {$added_shifts} new Evening sessions for scheduled doctors.\n";
}

// 8. Run Migration: add_column.php (checked_in)
echo "\nRunning migration: Adding checked_in column...\n";
$sql_checkin = "ALTER TABLE appointmenttb ADD COLUMN checked_in TINYINT DEFAULT 0";
if (mysqli_query($con, $sql_checkin)) {
    echo "  - Success: Added checked_in column.\n";
} else {
    $err = mysqli_error($con);
    if (strpos($err, 'Duplicate column name') !== false) {
        echo "  - Already applied: checked_in column.\n";
    } else {
        echo "  - Error: " . $err . "\n";
    }
}

// 9. Run Migration: update_db.php (qr_token, delayed_mins)
echo "\nRunning migration: Adding qr_token and delayed_mins columns...\n";
$update_alters = [
    "ALTER TABLE appointmenttb ADD COLUMN qr_token VARCHAR(100) NULL AFTER expected_time",
    "ALTER TABLE appointmenttb ADD COLUMN delayed_mins INT DEFAULT 0 AFTER expected_time"
];

foreach ($update_alters as $query) {
    if (mysqli_query($con, $query)) {
        echo "  - Success: " . substr($query, 0, 50) . "...\n";
    } else {
        $err = mysqli_error($con);
        if (strpos($err, 'Duplicate column name') !== false) {
            echo "  - Already applied: " . substr($query, 0, 50) . "...\n";
        } else {
            echo "  - Error: " . $err . "\n";
        }
    }
}

// 10. Close Connection and Report Completion
mysqli_close($con);
echo "\n=== Setup Completed Successfully! ===\n";
echo "Your MABS database is now fully configured and up to date.\n";
?>
