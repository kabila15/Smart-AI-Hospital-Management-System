<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

$query = "ALTER TABLE appointmenttb ADD COLUMN checked_in TINYINT DEFAULT 0";
$result = mysqli_query($con, $query);

if ($result) {
    echo "Column 'checked_in' added successfully.";
} else {
    echo "Error adding column: " . mysqli_error($con);
}
?>