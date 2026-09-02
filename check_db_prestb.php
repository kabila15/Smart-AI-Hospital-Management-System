<?php
$c = mysqli_connect('localhost', 'root', '', 'myhmsdb');
if (!$c) die('Connect Error');
$r = mysqli_query($c, 'DESCRIBE prestb');
while($row = mysqli_fetch_assoc($r)) echo $row['Field'] . PHP_EOL;
?>