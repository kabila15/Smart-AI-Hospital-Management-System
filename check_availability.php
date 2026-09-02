<?php
// Suppress warnings to ensure clean JSON output
error_reporting(0);

$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

if (isset($_GET['doctor']) && isset($_GET['appdate']) && isset($_GET['session_type'])) {
    $doctor = mysqli_real_escape_string($con, $_GET['doctor']);
    $appdate = mysqli_real_escape_string($con, $_GET['appdate']);
    $session_type = mysqli_real_escape_string($con, $_GET['session_type']);
    $day_of_week = date('l', strtotime($appdate));

    $response = [
        'status' => 'available',
        'message' => '',
        'capacity' => 0,
        'tokens_booked' => 0,
        'is_full' => false,
        'suggestions' => [
            'next_available_date' => null,
            'alternative_doctors' => [],
        ]
    ];

    // Check if doctor has an approved leave on this date
    $leave_check = mysqli_query($con, "SELECT id FROM doctor_leaves WHERE doctor='$doctor' AND leave_date='$appdate' AND status='Approved'");
    if (mysqli_num_rows($leave_check) > 0) {
        $response['status'] = 'unavailable';
        $response['message'] = "Dr. $doctor is on leave on this date. Please select another date.";
    } else {
        // Fetch doctor session details
        $sched_query = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_name='$doctor' AND day_of_week='$day_of_week' AND session_type='$session_type' LIMIT 1");
        if (mysqli_num_rows($sched_query) > 0) {
            $sched = mysqli_fetch_assoc($sched_query);
            $response['capacity'] = (int) ($sched['session_capacity'] ? $sched['session_capacity'] : 12);

            // Count booked tokens
            $booking_query = "SELECT COUNT(*) as total FROM appointmenttb WHERE doctor='$doctor' AND appdate='$appdate' AND session_type='$session_type' AND userStatus='1'";
            $booking_res = mysqli_query($con, $booking_query);
            $booking_data = mysqli_fetch_assoc($booking_res);
            $response['tokens_booked'] = (int) $booking_data['total'];

            if ($response['tokens_booked'] >= $response['capacity']) {
                $response['is_full'] = true;
                $response['status'] = 'full';
            }
        } else {
            $response['status'] = 'unavailable';
            $response['message'] = "Doctor is not scheduled for a $session_type session on this day ($day_of_week).";
        }
    }

    // --- SUGGESTION LOGIC ---

    // 1. Find Alternative Doctors in same specialization with space
    $spec_query = "SELECT spec FROM doctb WHERE username='$doctor'";
    $spec_res = mysqli_query($con, $spec_query);
    if ($spec_res && mysqli_num_rows($spec_res) > 0) {
        $spec_row = mysqli_fetch_assoc($spec_res);
        $spec = $spec_row['spec'];

        $alt_docs_query = "SELECT doctb.username, ds.session_capacity 
                           FROM doctb 
                           JOIN doctor_schedule ds ON doctb.username = ds.doctor_name 
                           WHERE doctb.spec='$spec' AND doctb.username != '$doctor' AND ds.day_of_week='$day_of_week' AND ds.session_type='$session_type'
                           AND doctb.username NOT IN (SELECT doctor FROM doctor_leaves WHERE leave_date='$appdate' AND status='Approved')";
        $alt_docs_res = mysqli_query($con, $alt_docs_query);
        while ($alt_row = mysqli_fetch_assoc($alt_docs_res)) {
            $alt_doctor = $alt_row['username'];
            $alt_cap = (int) $alt_row['session_capacity'];

            $alt_booking_query = "SELECT COUNT(*) as count FROM appointmenttb WHERE doctor='$alt_doctor' AND appdate='$appdate' AND session_type='$session_type' AND userStatus='1'";
            $alt_booking_res = mysqli_query($con, $alt_booking_query);
            $alt_booking_row = mysqli_fetch_assoc($alt_booking_res);

            if ($alt_booking_row['count'] < $alt_cap) {
                $response['suggestions']['alternative_doctors'][] = $alt_doctor;
            }
        }
    }

    // 2. Find Next Available Date for this doctor
    for ($d = 1; $d <= 7; $d++) {
        $next_date = date('Y-m-d', strtotime($appdate . " +$d days"));
        $next_day = date('l', strtotime($next_date));

        $ns_query = mysqli_query($con, "SELECT session_capacity FROM doctor_schedule WHERE doctor_name='$doctor' AND day_of_week='$next_day' AND session_type='$session_type' LIMIT 1");
        if (mysqli_num_rows($ns_query) > 0) {
            $ns_row = mysqli_fetch_assoc($ns_query);
            $ns_cap = (int) $ns_row['session_capacity'];

            $nb_query = mysqli_query($con, "SELECT COUNT(*) as count FROM appointmenttb WHERE doctor='$doctor' AND appdate='$next_date' AND session_type='$session_type' AND userStatus='1'");
            $nb_row = mysqli_fetch_assoc($nb_query);

            $leave_check_next = mysqli_query($con, "SELECT id FROM doctor_leaves WHERE doctor='$doctor' AND leave_date='$next_date' AND status='Approved'");

            if ($nb_row['count'] < $ns_cap && mysqli_num_rows($leave_check_next) == 0) {
                $response['suggestions']['next_available_date'] = $next_date;
                break;
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>