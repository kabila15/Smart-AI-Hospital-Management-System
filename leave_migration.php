<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

$query = "CREATE TABLE IF NOT EXISTS `doctor_leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor` varchar(50) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type` varchar(20) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if (mysqli_query($con, $query)) {
    echo "Table doctor_leaves created successfully";
} else {
    echo "Error creating table: " . mysqli_error($con);
}
?>