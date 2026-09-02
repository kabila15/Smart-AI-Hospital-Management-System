<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!isset($_GET['token']) || empty($_GET['token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing token']);
    exit;
}

$token = mysqli_real_escape_string($con, $_GET['token']);
$query = mysqli_query($con, "SELECT * FROM appointmenttb WHERE qr_token='$token'");

if (mysqli_num_rows($query) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

$app = mysqli_fetch_assoc($query);
$doctor = $app['doctor'];
$my_token = $app['token_no'];
$appdate = $app['appdate'];
$expected = $app['expected_time'];
$delayed_mins = isset($app['delayed_mins']) ? (int) $app['delayed_mins'] : 0;

// Update expected if delayed
$final_expected = date("H:i:s", strtotime($expected) + ($delayed_mins * 60));

// Get serving token
$serving_query = mysqli_query($con, "SELECT token_no FROM appointmenttb WHERE doctor='$doctor' AND appdate='$appdate' AND serving_status=1 LIMIT 1");
$serving_token = mysqli_num_rows($serving_query) > 0 ? mysqli_fetch_assoc($serving_query)['token_no'] : "Waiting to start";

// Tokens ahead
$ahead_query = mysqli_query($con, "SELECT COUNT(*) as total FROM appointmenttb WHERE doctor='$doctor' AND appdate='$appdate' AND userStatus='1' AND serving_status=0 AND token_no < $my_token");
$ahead = (int) mysqli_fetch_assoc($ahead_query)['total'];

// Estimated Wait Time (15 mins per patient ahead)
$wait_mins = $ahead * 15;

echo json_encode([
    'status' => 'success',
    'my_token' => $my_token,
    'serving_token' => $serving_token,
    'ahead' => $ahead,
    'expected_time' => $final_expected,
    'delayed_mins' => $delayed_mins,
    'wait_mins' => $wait_mins,
    'doctor' => $doctor,
    'date' => $appdate
]);
?>