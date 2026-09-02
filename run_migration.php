<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = file_get_contents('token_system_migration.sql');

if (mysqli_multi_query($con, $sql)) {
    do {
        if ($result = mysqli_store_result($con)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($con));
    echo "Migration successful!";
} else {
    echo "Error: " . mysqli_error($con);
}
mysqli_close($con);
?>