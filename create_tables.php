<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "
CREATE TABLE IF NOT EXISTS `doctor_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_name` varchar(50) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `doctor_holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_name` varchar(50) NOT NULL,
  `holiday_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";

if (mysqli_multi_query($con, $sql)) {
    do {
        if ($result = mysqli_store_result($con)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($con));
    echo "Tables created successfully or already exist.\n";
} else {
    echo "Error creating tables: " . mysqli_error($con) . "\n";
}
?>