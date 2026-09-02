<?php
$file = 'admin-panel.php';
$content = file_get_contents($file);

// Replace headers
$old_headers = '<th scope="col">Doctor Name</th>
                    <th scope="col">Consultancy Fees</th>
                    <th scope="col">Appointment Date</th>
                    <th scope="col">Appointment Time</th>
                    <th scope="col">Current Status</th>
                    <th scope="col">Action</th>';

$new_headers = '<th scope="col">Doctor Name</th>
                    <th scope="col">Fees</th>
                    <th scope="col">Date</th>
                    <th scope="col">Token #</th>
                    <th scope="col">Approx Time</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>';

$content = str_replace($old_headers, $new_headers, $content);

// Replace row data (more pattern based since it might have changed)
$pattern = '/<td><\?php echo \$row\[\'docFees\'\]; \?><\/td>\s+<td><\?php echo \$row\[\'appdate\'\]; \?><\/td>\s+<td><\?php echo \$row\[\'apptime\'\]; \?><\/td>/';
$replacement = '<td><?php echo $row[\'docFees\']; ?></td>
                        <td><?php echo $row[\'appdate\']; ?></td>
                        <td><?php echo "#" . $row[\'token_no\']; ?></td>
                        <td><?php echo $row[\'expected_time\']; ?></td>';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);
echo "Admin panel updated!";
?>
