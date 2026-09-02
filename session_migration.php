<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// 1. Add session_type to appointmenttb
$q1 = "ALTER TABLE appointmenttb ADD COLUMN session_type VARCHAR(20) DEFAULT 'Morning'";
mysqli_query($con, $q1);

// 2. Add session_type to doctor_schedule
$q2 = "ALTER TABLE doctor_schedule ADD COLUMN session_type VARCHAR(20) DEFAULT 'Morning'";
mysqli_query($con, $q2);

// 3. Generate Evening rows for existing doctors
$fetch_docs = mysqli_query($con, "SELECT DISTINCT doctor_name FROM doctor_schedule");
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

while ($row = mysqli_fetch_assoc($fetch_docs)) {
    $doc = $row['doctor_name'];
    foreach ($days as $day) {
        $check = mysqli_query($con, "SELECT id FROM doctor_schedule WHERE doctor_name='$doc' AND day_of_week='$day' AND session_type='Evening'");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($con, "INSERT INTO doctor_schedule(doctor_name, day_of_week, start_time, end_time, avg_consult_time, session_capacity, session_type) 
                                VALUES('$doc', '$day', '17:00:00', '20:00:00', 15, 12, 'Evening')");
        }
    }
}

echo "Migration complete: session_type columns added and Evening schedules generated.";
?>