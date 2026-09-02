<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) { die("Connection failed: " . mysqli_connect_error()); }
$result = mysqli_query($con, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    echo $row[0] . "\n";
}
?>