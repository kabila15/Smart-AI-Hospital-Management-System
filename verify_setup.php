<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$tables = ['doctor_schedule', 'doctor_holidays', 'doctb', 'appointmenttb'];
foreach ($tables as $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "Table $table exists.\n";
        $count_res = mysqli_query($con, "SELECT COUNT(*) as cnt FROM $table");
        $count = mysqli_fetch_assoc($count_res)['cnt'];
        echo "Rows in $table: $count\n\n";
    } else {
        echo "Table $table DOES NOT exist.\n";
    }
}

echo "Sample Doctor Schedule data:\n";
$res = mysqli_query($con, "SELECT * FROM doctor_schedule LIMIT 5");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>