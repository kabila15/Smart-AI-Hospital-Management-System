<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
$doctor_name = "Nishanth";
$day = "Tuesday";
$start = "09:00:00";
$end = "12:00:00";
mysqli_query($con, "INSERT INTO doctor_schedule(doctor_name, day_of_week, start_time, end_time) VALUES('$doctor_name', '$day', '$start', '$end')");
echo "Sample data inserted.\n";
?>