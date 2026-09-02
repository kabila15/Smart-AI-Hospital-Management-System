<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "ALTER TABLE appointmenttb ADD COLUMN qr_token VARCHAR(100) NULL AFTER expected_time";
if (mysqli_query($con, $sql)) {
    echo "Column qr_token added successfully\n";
} else {
    echo "Error adding column: " . mysqli_error($con) . "\n";
}

$sql2 = "ALTER TABLE appointmenttb ADD COLUMN delayed_mins INT DEFAULT 0 AFTER expected_time";
if (mysqli_query($con, $sql2)) {
    echo "Column delayed_mins added successfully\n";
} else {
    echo "Error adding column: " . mysqli_error($con) . "\n";
}
mysqli_close($con);
?>