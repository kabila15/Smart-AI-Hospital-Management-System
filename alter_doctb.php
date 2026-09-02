<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (!$con) { die("Connection failed"); }

// Add columns
$sql1 = "ALTER TABLE doctb 
    ADD COLUMN m_start TIME DEFAULT '10:00:00',
    ADD COLUMN m_end TIME DEFAULT '13:00:00',
    ADD COLUMN m_cap INT DEFAULT 12,
    ADD COLUMN e_start TIME DEFAULT '17:00:00',
    ADD COLUMN e_end TIME DEFAULT '20:00:00',
    ADD COLUMN e_cap INT DEFAULT 12";

if (mysqli_query($con, $sql1)) {
    echo "Columns added successfully.\n";
} else {
    echo "Error adding columns (they might already exist): " . mysqli_error($con) . "\n";
}

// Update existing records
$sql2 = "UPDATE doctb SET 
    m_start = '10:00:00', m_end = '13:00:00', m_cap = 12,
    e_start = '17:00:00', e_end = '20:00:00', e_cap = 12
    WHERE m_start IS NULL";

if (mysqli_query($con, $sql2)) {
    echo "Records updated successfully.\n";
} else {
    echo "Error updating records: " . mysqli_error($con) . "\n";
}
?>
