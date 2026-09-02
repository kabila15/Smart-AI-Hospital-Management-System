<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) { die("Connection failed"); }
$result = mysqli_query($con, "DESCRIBE doctb");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
