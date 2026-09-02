<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('func1.php');
include('whatsapp_notification.php');
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!isset($_SESSION['dname']) || empty($_SESSION['dname'])) {
    header("Location: index.php");
    exit();
}

$doctor = $_SESSION['dname'];

if (isset($_POST['leave_submit'])) {
    $dname = $_SESSION['dname'];
    $leave_date = mysqli_real_escape_string($con, $_POST['leave_date']);
    $leave_type = mysqli_real_escape_string($con, $_POST['leave_type']);
    $reason = mysqli_real_escape_string($con, $_POST['reason']);

    $chk_query = mysqli_query($con, "select * from doctor_leaves where doctor='$dname' and leave_date='$leave_date'");
    if (mysqli_num_rows($chk_query) > 0) {
        $_SESSION['alert_msg'] = "You have already submitted a leave request for this date.";
    } else {
        $status = ($leave_type == 'Emergency') ? 'Approved' : 'Pending';
        $insert = mysqli_query($con, "insert into doctor_leaves(doctor,leave_date,leave_type,reason,status) values('$dname','$leave_date','$leave_type','$reason','$status')");
        if ($insert) {
            $_SESSION['alert_msg'] = "Leave request submitted successfully.";
            
            // If Emergency Leave, trigger rescheduling for affected patients
            if ($leave_type == 'Emergency') {
                // 1. Get current doctor specialization
                $spec_q = mysqli_query($con, "SELECT spec FROM doctb WHERE username='$dname' LIMIT 1");
                $spec_row = mysqli_fetch_assoc($spec_q);
                $specialization = $spec_row['spec'];

                // 2. Find all alternative doctors with the same specialization
                $alt_docs = [];
                $alt_q = mysqli_query($con, "SELECT username, docFees FROM doctb WHERE spec='$specialization' AND username != '$dname'");
                while ($alt_row = mysqli_fetch_assoc($alt_q)) {
                    $alt_docs[] = $alt_row;
                }

                // 3. Find available slots for alternative doctors on this day
                $day_of_week = date('l', strtotime($leave_date));
                $options = [];
                foreach ($alt_docs as $alt_doc) {
                    $alt_name = $alt_doc['username'];
                    $sched_q = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_name='$alt_name' AND day_of_week='$day_of_week'");
                    while ($sched = mysqli_fetch_assoc($sched_q)) {
                        $session_type = $sched['session_type'];
                        // Count booked appointments
                        $booked_q = mysqli_query($con, "SELECT COUNT(*) as cnt FROM appointmenttb WHERE doctor='$alt_name' AND appdate='$leave_date' AND session_type='$session_type' AND userStatus='1'");
                        $booked_row = mysqli_fetch_assoc($booked_q);
                        $booked_cnt = $booked_row['cnt'];
                        
                        if ($booked_cnt < $sched['session_capacity']) {
                            $token_no = $booked_cnt + 1;
                            $start_time = $sched['start_time'];
                            $avg_consult_time = $sched['avg_consult_time'];
                            $expected_seconds = strtotime($start_time) + ($booked_cnt * $avg_consult_time * 60);
                            $expected_time = date('H:i:s', $expected_seconds);
                            
                            $options[] = [
                                'doctor_name' => $alt_name,
                                'session_type' => $session_type,
                                'token_no' => $token_no,
                                'expected_time' => $expected_time,
                                'display_time' => date('h:i A', $expected_seconds),
                                'fees' => $alt_doc['docFees']
                            ];
                        }
                    }
                }

                // 4. Get all active appointments for the current doctor on this date
                $apt_q = mysqli_query($con, "SELECT * FROM appointmenttb WHERE doctor='$dname' AND appdate='$leave_date' AND userStatus='1'");
                $notified_count = 0;
                while ($apt = mysqli_fetch_assoc($apt_q)) {
                    if (count($options) > 0) {
                        triggerEmergencyReschedule($apt['contact'], $apt['ID'], $dname, $leave_date, $specialization, $options);
                    } else {
                        // No alternatives available, send cancellation notice
                        $cancel_msg = "🚨 *Emergency Leave Notice*\n\nDear " . $apt['fname'] . ",\nDr. $dname has taken emergency leave for today ($leave_date).\nUnfortunately, no other doctors are available with the same specialization today. Your appointment (ID: " . $apt['ID'] . ") has been cancelled. Please visit our website to rebook for another date.";
                        sendWhatsAppNotification($apt['contact'], $cancel_msg);
                        mysqli_query($con, "UPDATE appointmenttb SET userStatus='0' WHERE ID='" . $apt['ID'] . "'");
                    }
                    $notified_count++;
                }
                
                if ($notified_count > 0) {
                    $_SESSION['alert_msg'] = "Emergency leave approved! $notified_count affected patients have been notified via WhatsApp with rescheduling options.";
                } else {
                    $_SESSION['alert_msg'] = "Emergency leave approved. No appointments were scheduled for this day.";
                }
            }
        } else {
            $_SESSION['alert_msg'] = "Failed to submit leave request.";
        }
    }
    header("Location: doctor-panel.php");
    exit();
}

if (isset($_POST['update_schedule'])) {
    $schedule = isset($_POST['schedule']) ? $_POST['schedule'] : [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $sessions = ['Morning', 'Evening'];
    
    $doc_info_q = mysqli_query($con, "SELECT * FROM doctb WHERE username='$doctor'");
    $doc_info = mysqli_fetch_assoc($doc_info_q);

    foreach ($days as $day) {
        foreach ($sessions as $session) {
            $is_checked = isset($schedule[$day][$session]) && $schedule[$day][$session] == '1';
            
            $check = mysqli_query($con, "SELECT id FROM doctor_schedule WHERE doctor_name='$doctor' AND day_of_week='$day' AND session_type='$session'");
            $exists = (mysqli_num_rows($check) > 0);
            
            if ($is_checked && !$exists) {
                if ($session == 'Morning') {
                    $start = $doc_info['m_start'];
                    $end = $doc_info['m_end'];
                    $cap = $doc_info['m_cap'];
                } else {
                    $start = $doc_info['e_start'];
                    $end = $doc_info['e_end'];
                    $cap = $doc_info['e_cap'];
                }
                mysqli_query($con, "INSERT INTO doctor_schedule (doctor_name, day_of_week, start_time, end_time, avg_consult_time, session_capacity, session_type) VALUES ('$doctor', '$day', '$start', '$end', 15, $cap, '$session')");
            } else if (!$is_checked && $exists) {
                mysqli_query($con, "DELETE FROM doctor_schedule WHERE doctor_name='$doctor' AND day_of_week='$day' AND session_type='$session'");
            }
        }
    }
    $_SESSION['alert_msg'] = "Weekly schedule updated successfully!";
    header("Location: doctor-panel.php#list-sched");
    exit();
}

if (isset($_POST['delay_submit'])) {
    $dname = $_SESSION['dname'];
    $delay_mins = (int) $_POST['delay_mins'];

    $update = mysqli_query($con, "update appointmenttb set delayed_mins = delayed_mins + $delay_mins where doctor='$dname' and appdate=CURDATE() and userStatus='1' and serving_status=0");

    if ($update) {
        $notify_q = mysqli_query($con, "select * from appointmenttb where doctor='$dname' and appdate=CURDATE() and userStatus='1' and serving_status=0");
        while ($pt = mysqli_fetch_assoc($notify_q)) {
            $new_expected = date("h:i A", strtotime($pt['expected_time']) + ($pt['delayed_mins'] * 60));
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/MABS/Hospital-Management-System-master";
            $msg = "🚨 *Smart Delay Alert*\n\nHello " . $pt['fname'] . ",\nDr. $dname is currently running approx $delay_mins mins late due to ongoing consults or emergencies.\n\nYour revised expected time is *$new_expected*.\n\nTrack your real-time queue status here:\n" . $base_url . "/live_queue.php?token=" . $pt['qr_token'];
            sendWhatsAppNotification($pt['contact'], $msg);
        }
        $_SESSION['alert_msg'] = "Delay announced successfully. Patients have been notified via WhatsApp.";
    } else {
        $_SESSION['alert_msg'] = "Failed to announce delay.";
    }
    header("Location: doctor-panel.php#list-delay");
    exit();
}

if (isset($_GET['serve_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['serve_id']);
    // Complete any currently serving patient first
    mysqli_query($con, "update appointmenttb set serving_status=2 where serving_status=1 and doctor='$doctor' and appdate=CURDATE()");
    // Serve new patient
    mysqli_query($con, "update appointmenttb set serving_status=1 where ID='$id'");
    header("Location: doctor-panel.php#list-home");
    exit();
}

if (isset($_GET['complete'])) {
    $id = mysqli_real_escape_string($con, $_GET['complete']);
    mysqli_query($con, "update appointmenttb set serving_status=2 where ID='$id'");
    header("Location: doctor-panel.php#list-home");
    exit();
}

if (isset($_GET['skip'])) {
    $id = mysqli_real_escape_string($con, $_GET['skip']);
    mysqli_query($con, "update appointmenttb set serving_status=3 where ID='$id'");
    header("Location: doctor-panel.php#list-home");
    exit();
}

if (isset($_POST['pres_submit'])) {
    $app_id = (int) $_POST['app_id'];
    $pid = (int) $_POST['pid'];
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $disease = mysqli_real_escape_string($con, $_POST['disease']);
    $allergy = mysqli_real_escape_string($con, $_POST['allergy']);
    $prescription = mysqli_real_escape_string($con, $_POST['prescription']);
    
    // Get appdate and apptime from the appointment
    $app_q = mysqli_query($con, "SELECT appdate, apptime FROM appointmenttb WHERE ID='$app_id'");
    $app_row = mysqli_fetch_assoc($app_q);
    $appdate = $app_row['appdate'];
    $apptime = $app_row['apptime'];
    
    $ins_query = mysqli_query($con, "insert into prestb(doctor,pid,ID,fname,lname,appdate,apptime,disease,allergy,prescription) values ('$doctor','$pid','$app_id','$fname','$lname','$appdate','$apptime','$disease','$allergy','$prescription')");
    
    if ($ins_query) {
        // Update appointment status to completed (serving_status=2) and payment to Pending
        mysqli_query($con, "update appointmenttb set paymentStatus='Pending', serving_status=2 where ID='$app_id'");
        $_SESSION['alert_msg'] = "Prescription saved successfully!";
    } else {
        $_SESSION['alert_msg'] = "Failed to save prescription.";
    }
    header("Location: doctor-panel.php#list-home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="dashboard.css?v=2.0">
    <title>Doctor Dashboard – Global Hospital</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <nav class="sidebar" id="sidebar">
        <a class="sidebar-brand" href="#">
            <div class="brand-icon"><i class="fa fa-hospital-o"></i></div>
            <div>
                <span class="brand-name">Global Hospital</span>
                <span class="brand-sub">Doctor Portal</span>
            </div>
        </a>
        <div class="sidebar-section-label">Navigation</div>
        <div class="list-group" id="list-tab" role="tablist">
            <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list"
                href="#list-dash" role="tab">
                <i class="fa fa-tachometer"></i> Dashboard
            </a>
            <a class="list-group-item list-group-item-action" href="#list-home" id="list-home-list" role="tab"
                data-toggle="list">
                <i class="fa fa-calendar"></i> My Appointments
            </a>
            <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab"
                data-toggle="list">
                <i class="fa fa-file-text-o"></i> Patient Prescriptions
            </a>
            <a class="list-group-item list-group-item-action" href="#list-delay" id="list-delay-list" role="tab"
                data-toggle="list">
                <i class="fa fa-clock-o"></i> Smart Delay Management
            </a>
            <a class="list-group-item list-group-item-action" href="#list-sched" id="list-sched-list" role="tab"
                data-toggle="list">
                <i class="fa fa-calendar-check-o"></i> My Schedule
            </a>
            <a class="list-group-item list-group-item-action" href="#list-leave" id="list-leave-list" role="tab"
                data-toggle="list">
                <i class="fa fa-calendar-times-o"></i> Apply for Leave
            </a>
        </div>
    </nav>

    <!-- ═══════════════ MAIN CONTENT ═══════════════ -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
            <h5 class="page-title">Dr.
                <?php echo $doctor; ?><span id="headerSubtitle"> – Dashboard</span>
            </h5>
            <div class="header-right">
                <span class="header-date"><i class="fa fa-calendar"></i> <span id="headerDate"></span></span>
                <div class="profile-wrapper">
                  <button class="user-badge" id="profileToggle" onclick="toggleProfile(event)" aria-expanded="false">
                    <div class="avatar"><?php echo strtoupper(substr($doctor, 0, 1)); ?></div>
                    <span class="badge-name">Dr. <?php echo $doctor; ?></span>
                    <i class="fa fa-angle-down" style="font-size:0.75rem; color:#6b7280; margin-left:2px;"></i>
                  </button>
                  <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-drop-header">
                      <div class="profile-drop-avatar"><?php echo strtoupper(substr($doctor, 0, 1)); ?></div>
                      <div>
                        <div class="profile-drop-name">Dr. <?php echo $doctor; ?></div>
                        <div class="profile-drop-role">Doctor Account</div>
                      </div>
                    </div>
                    <div class="profile-drop-body">
                      <?php
                        $doc_info = mysqli_query($con, "SELECT spec, email FROM doctb WHERE username='$doctor' LIMIT 1");
                        $doc_row = ($doc_info && mysqli_num_rows($doc_info) > 0) ? mysqli_fetch_assoc($doc_info) : [];
                      ?>
                      <div class="profile-drop-item">
                        <i class="fa fa-stethoscope"></i>
                        <span class="item-label">Specialty</span>
                        <span class="item-value"><?php echo $doc_row['spec'] ?? '—'; ?></span>
                      </div>
                      <div class="profile-drop-item">
                        <i class="fa fa-envelope"></i>
                        <span class="item-label">Email</span>
                        <span class="item-value"><?php echo $doc_row['email'] ?? '—'; ?></span>
                      </div>
                      <div class="profile-drop-item">
                        <i class="fa fa-phone"></i>
                        <span class="item-label">Contact</span>
                        <span class="item-value"><?php echo $doc_row['contact'] ?? '—'; ?></span>
                      </div>
                    </div>
                    <div class="profile-drop-footer">
                      <a href="logout.php"><i class="fa fa-sign-out"></i> Sign Out</a>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($_SESSION['alert_msg'])) { ?>
                <div class="alert alert-info alert-dismissible fade show mx-3 mt-3" role="alert" style="border-radius:10px;">
                    <i class="fa fa-info-circle"></i> <?php echo $_SESSION['alert_msg']; unset($_SESSION['alert_msg']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php } ?>
            <div class="tab-content" id="nav-tabContent">

                <!-- Dashboard -->
                <div class="tab-pane show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-calendar fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">My Appointments</h5>
                                        <script>function clickDiv(id) { document.querySelector(id).click(); }</script>
                                        <p class="card-text mt-3"><a href="#list-home"
                                                onclick="clickDiv('#list-home-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">View
                                                Appointments</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-file-text-o fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Patient Prescriptions</h5>
                                        <p class="card-text mt-3"><a href="#list-pres"
                                                onclick="clickDiv('#list-pres-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">View
                                                Prescriptions</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-calendar-check-o fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">My Schedule</h5>
                                        <p class="card-text mt-3"><a href="#list-sched"
                                                onclick="clickDiv('#list-sched-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">Manage Schedule</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-calendar-times-o fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Apply for Leave</h5>
                                        <p class="card-text mt-3"><a href="#list-leave"
                                                onclick="clickDiv('#list-leave-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">Apply Leave</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Schedule -->
                <div class="tab-pane fade" id="list-sched" role="tabpanel" aria-labelledby="list-sched-list">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-4"><i class="fa fa-calendar-check-o"></i> My Weekly Schedule</h5>
                            <p class="text-muted">Select your availability for Morning and Evening sessions.</p>
                            
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            $current_sched = [];
                            $sq = mysqli_query($con, "SELECT day_of_week, session_type FROM doctor_schedule WHERE doctor_name='$doctor'");
                            while($sr = mysqli_fetch_assoc($sq)) {
                                $current_sched[$sr['day_of_week']][$sr['session_type']] = true;
                            }
                            $doc_settings_q = mysqli_query($con, "SELECT * FROM doctb WHERE username='$doctor'");
                            $doc_settings = mysqli_fetch_assoc($doc_settings_q);
                            $m_str = date('h:i A', strtotime($doc_settings['m_start'])) . ' - ' . date('h:i A', strtotime($doc_settings['m_end']));
                            $e_str = date('h:i A', strtotime($doc_settings['e_start'])) . ' - ' . date('h:i A', strtotime($doc_settings['e_end']));
                            ?>
                            <form method="post" action="doctor-panel.php">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Day</th>
                                                <th>Morning (<?php echo $m_str; ?>)</th>
                                                <th>Evening (<?php echo $e_str; ?>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($days as $day) { ?>
                                            <tr>
                                                <td class="align-middle font-weight-bold"><?php echo $day; ?></td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="m_<?php echo $day; ?>" name="schedule[<?php echo $day; ?>][Morning]" value="1" <?php echo isset($current_sched[$day]['Morning']) ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="m_<?php echo $day; ?>">Available</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="e_<?php echo $day; ?>" name="schedule[<?php echo $day; ?>][Evening]" value="1" <?php echo isset($current_sched[$day]['Evening']) ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="e_<?php echo $day; ?>">Available</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right mt-3">
                                    <button type="submit" name="update_schedule" class="btn btn-primary px-4 rounded-pill">Save Weekly Schedule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- My Appointments -->
                <div class="tab-pane" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4"><label>Filter by Date:</label></div>
                                <div class="col-md-4">
                                    <form method="get" action="doctor-panel.php">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="filter_date"
                                                value="<?php echo isset($_GET['filter_date']) ? $_GET['filter_date'] : ''; ?>">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                            if (!function_exists('renderAppointmentTable')) {
                                function renderAppointmentTable($appointments) {
                                    if (empty($appointments)) {
                                        echo '<div class="alert alert-info text-center py-4 my-2"><i class="fa fa-info-circle"></i> No appointments found in this category.</div>';
                                        return;
                                    }
                                    echo '<div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Doctor</th>
                                                    <th>Fees</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Token</th>
                                                    <th>Status</th>
                                                    <th>Serving</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                    foreach ($appointments as $row) {
                                        $is_cancelled = ($row['userStatus'] == 0);
                                        
                                        if ($is_cancelled) {
                                            $status_text = "<span class='badge' style='background:#ffe0e0;color:#c0392b;padding:5px 10px;border-radius:20px;font-size:0.8rem;'><i class='fa fa-times-circle'></i> Cancelled by Patient</span>";
                                        } else if ($row['arrival_status'] == 1) {
                                            $status_text = "<span class='badge badge-info'>Arrived</span>";
                                        } else if ($row['serving_status'] == 2) {
                                            $status_text = "<span class='badge badge-success'>Completed</span>";
                                        } else {
                                            $status_text = "<span class='badge badge-secondary' style='background:#e8f5e9;color:#2e7d32;padding:5px 10px;border-radius:20px;font-size:0.8rem;'>Active</span>";
                                        }
                                        
                                        // Hide serving/action for cancelled appointments
                                        $serving_action = '';
                                        $action_btn = '';
                                        if (!$is_cancelled) {
                                            if ($row['serving_status'] == 1) {
                                                $serving_action = '<span class="badge badge-success">Serving</span>';
                                            } else if ($row['serving_status'] == 2) {
                                                $serving_action = '<span class="badge badge-secondary">Done</span>';
                                            } else {
                                                $serving_action = '<a href="doctor-panel.php?serve_id=' . $row['ID'] . '" class="btn btn-warning btn-sm">Serve</a>';
                                            }
                                            $action_btn = '<a href="doctor-panel.php?pres_id=' . $row['ID'] . '" class="btn btn-primary btn-sm">Prescribe</a>';
                                        } else {
                                            $serving_action = '<span class="text-muted">—</span>';
                                            $action_btn = '<span class="text-muted small">—</span>';
                                        }
                                        
                                        $row_style = $is_cancelled ? ' style="background:#fff8f8;opacity:0.85;"' : '';
                                        
                                        echo '<tr' . $row_style . '>
                                            <td>' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</td>
                                            <td>' . htmlspecialchars($row['contact']) . '</td>
                                            <td>' . htmlspecialchars($row['doctor']) . '</td>
                                            <td>' . htmlspecialchars($row['docFees']) . '</td>
                                            <td>' . htmlspecialchars($row['appdate']) . '</td>
                                            <td>' . htmlspecialchars($row['apptime']) . '</td>
                                            <td>#' . htmlspecialchars($row['token_no']) . '</td>
                                            <td>' . $status_text . '</td>
                                            <td>' . $serving_action . '</td>
                                            <td>' . $action_btn . '</td>
                                        </tr>';
                                    }
                                    echo '</tbody>
                                        </table>
                                    </div>';
                                }
                            }

                            $con = mysqli_connect("localhost", "root", "", "myhmsdb");
                            $today_date = date('Y-m-d');
                            $old_appointments = [];
                            $today_appointments = [];
                            $upcoming_appointments = [];

                            $filter_date = isset($_GET['filter_date']) ? mysqli_real_escape_string($con, $_GET['filter_date']) : '';
                            $query = $filter_date
                                ? "SELECT * FROM appointmenttb WHERE doctor='$doctor' AND appdate='$filter_date' AND doctorStatus='1' ORDER BY token_no ASC"
                                : "SELECT * FROM appointmenttb WHERE doctor='$doctor' AND doctorStatus='1' ORDER BY appdate DESC, token_no ASC";
                            $result = mysqli_query($con, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($row['appdate'] < $today_date) {
                                    $old_appointments[] = $row;
                                } else if ($row['appdate'] == $today_date) {
                                    $today_appointments[] = $row;
                                } else {
                                    $upcoming_appointments[] = $row;
                                }
                            }
                            ?>

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-pills nav-fill mb-4" id="appointmentTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="old-apps-tab" data-toggle="tab" href="#old-apps" role="tab" aria-controls="old-apps" aria-selected="false">
                                        <i class="fa fa-history"></i> Old Appointments (<?php echo count($old_appointments); ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="today-apps-tab" data-toggle="tab" href="#today-apps" role="tab" aria-controls="today-apps" aria-selected="true">
                                        <i class="fa fa-calendar-check-o"></i> Today / Current (<?php echo count($today_appointments); ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="upcoming-apps-tab" data-toggle="tab" href="#upcoming-apps" role="tab" aria-controls="upcoming-apps" aria-selected="false">
                                        <i class="fa fa-calendar-plus-o"></i> Upcoming Appointments (<?php echo count($upcoming_appointments); ?>)
                                    </a>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content" id="appointmentTabsContent">
                                <div class="tab-pane fade" id="old-apps" role="tabpanel" aria-labelledby="old-apps-tab">
                                    <?php renderAppointmentTable($old_appointments); ?>
                                </div>
                                <div class="tab-pane fade show active" id="today-apps" role="tabpanel" aria-labelledby="today-apps-tab">
                                    <?php renderAppointmentTable($today_appointments); ?>
                                </div>
                                <div class="tab-pane fade" id="upcoming-apps" role="tabpanel" aria-labelledby="upcoming-apps-tab">
                                    <?php renderAppointmentTable($upcoming_appointments); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Prescriptions -->
                <div class="tab-pane" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Patient ID</th>
                                            <th>Appt ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Disease</th>
                                            <th>Allergy</th>
                                            <th>Prescription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $con2 = mysqli_connect("localhost", "root", "", "myhmsdb");
                                        $query2 = "select * from prestb where doctor='$doctor'";
                                        $result2 = mysqli_query($con2, $query2);
                                        while ($row = mysqli_fetch_array($result2)) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $row['doctor']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['pid']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['ID']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['fname']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['lname']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['appdate']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['apptime']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['disease']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['allergy']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['prescription']; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Smart Delay Management -->
                <div class="tab-pane" id="list-delay" role="tabpanel" aria-labelledby="list-delay-list">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="font-weight-bold mb-4"><i class="fa fa-clock-o"></i> Smart Delay Management</h4>
                            <p class="text-muted mb-4">You can announce a delay if you are running late. This will
                                update
                                the expected time for all your pending appointments today and notify patients via
                                WhatsApp.</p>

                            <form method="post" action="doctor-panel.php">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label font-weight-bold">Additional Delay (Minutes):</label>
                                        <input type="number" name="delay_mins" class="form-control"
                                            placeholder="e.g. 15" required min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" name="delay_submit" class="btn btn-primary px-4">
                                            <i class="fa fa-bullhorn"></i> Announce Delay
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <?php
                            $dname = $_SESSION['dname'];
                            $delay_check = mysqli_query($con, "SELECT SUM(delayed_mins) as total_delay FROM appointmenttb WHERE doctor='$dname' AND appdate=CURDATE()");
                            $row_delay = mysqli_fetch_assoc($delay_check);
                            $current_total = $row_delay['total_delay'] ? $row_delay['total_delay'] : 0;
                            if ($current_total > 0) {
                                echo "<div class='alert alert-info mt-4'><i class='fa fa-info-circle'></i> Current total delay announced today: <strong>$current_total minutes</strong></div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Apply for Leave -->
                <div class="tab-pane" id="list-leave" role="tabpanel" aria-labelledby="list-leave-list">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><i class="fa fa-calendar-times-o"></i> Apply for Leave
                            </h5>
                            <form method="post" action="doctor-panel.php">
                                <div class="row">
                                    <div class="col-md-4"><label>Leave Date:</label></div>
                                    <div class="col-md-8"><input type="date" name="leave_date" class="form-control"
                                            required min="<?php echo date('Y-m-d'); ?>"></div>
                                    <br><br>
                                    <div class="col-md-4"><label>Leave Type:</label></div>
                                    <div class="col-md-8">
                                        <select name="leave_type" class="form-control" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="Sick Leave">Sick Leave</option>
                                            <option value="Personal Leave">Personal Leave</option>
                                            <option value="Emergency">Emergency</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div><br><br>
                                    <div class="col-md-4"><label>Reason:</label></div>
                                    <div class="col-md-8"><textarea name="reason" class="form-control" rows="3"
                                            placeholder="Describe the reason for your leave..."></textarea></div>
                                    <br><br>
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8 mt-4"><input type="submit" name="leave_submit"
                                            value="Submit Leave Request" class="btn btn-primary"></div>
                                </div>
                            </form>

                            <hr>
                            <h6>My Leave History</h6>
                            <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $con3 = mysqli_connect("localhost", "root", "", "myhmsdb");
                                    $lr = mysqli_query($con3, "SELECT * FROM doctor_leaves WHERE doctor='$doctor' ORDER BY leave_date DESC");
                                    while ($lrow = mysqli_fetch_assoc($lr)) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $lrow['leave_date']; ?>
                                            </td>
                                            <td>
                                                <?php echo $lrow['leave_type']; ?>
                                            </td>
                                            <td>
                                                <?php echo $lrow['reason']; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($lrow['status'] == 'Pending')
                                                    echo "<span class='badge badge-warning'>Pending</span>";
                                                else if ($lrow['status'] == 'Approved')
                                                    echo "<span class='badge badge-success'>Approved</span>";
                                                else
                                                    echo "<span class='badge badge-danger'>Rejected</span>";
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            </div><!-- /table-responsive -->
                        </div>
                    </div>
                </div>

                <!-- Prescription Modal trigger -->
                <?php
                if (isset($_GET['pres_id'])) {
                    $pres_id = (int) $_GET['pres_id'];
                    $con_p = mysqli_connect("localhost", "root", "", "myhmsdb");
                    $prow = mysqli_fetch_assoc(mysqli_query($con_p, "SELECT * FROM appointmenttb WHERE ID=$pres_id AND doctor='$doctor'"));
                    if ($prow) { ?>
                        <div class="modal fade show d-block" id="presModal" tabindex="-1" role="dialog"
                            style="background:rgba(0,0,0,.5);">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:#1e3a5f;color:#fff;border:none;">
                                        <h5 class="modal-title">Write Prescription –
                                            <?php echo $prow['fname'] . ' ' . $prow['lname']; ?>
                                        </h5>
                                        <a href="doctor-panel.php" class="close" style="color:#fff;opacity:.8;">&times;</a>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="doctor-panel.php">
                                            <input type="hidden" name="app_id" value="<?php echo $pres_id; ?>">
                                            <input type="hidden" name="fname" value="<?php echo $prow['fname']; ?>">
                                            <input type="hidden" name="lname" value="<?php echo $prow['lname']; ?>">
                                            <input type="hidden" name="pid" value="<?php echo $prow['pid']; ?>">
                                            <div class="form-group"><label>Disease</label><input type="text" name="disease"
                                                    class="form-control" required></div>
                                            <div class="form-group"><label>Allergy</label><input type="text" name="allergy"
                                                    class="form-control" required></div>
                                            <div class="form-group"><label>Prescription</label><textarea name="prescription"
                                                    class="form-control" rows="3" required></textarea></div>
                                            <button type="submit" name="pres_submit" class="btn btn-primary">Save
                                                Prescription</button>
                                            <a href="doctor-panel.php" class="btn btn-secondary ml-2">Cancel</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                }
                ?>

            </div><!-- /tab-content -->
        </div><!-- /content-area -->
    </div><!-- /main-content -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            var hash = window.location.hash;
            if (hash) { $('.list-group a[href="' + hash + '"]').tab('show'); }
            $('#list-tab a').on('shown.bs.tab', function (e) {
                var txt = $(e.target).text().trim();
                document.querySelector('.top-header .page-title span').textContent = ' – ' + txt;
            });
        });
        (function () {
            var d = new Date();
            document.getElementById('headerDate').textContent = d.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
        })();
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        function toggleProfile(e) {
            e.stopPropagation();
            var btn = document.getElementById('profileToggle');
            var dd  = document.getElementById('profileDropdown');
            var rect = btn.getBoundingClientRect();
            var ddW  = dd.offsetWidth || 290;
            // Position below button, aligned to its right edge
            dd.style.top  = (rect.bottom + 8) + 'px';
            dd.style.left = Math.max(8, rect.right - ddW) + 'px';
            dd.style.right = 'auto';
            dd.classList.toggle('open');
            btn.setAttribute('aria-expanded', dd.classList.contains('open'));
        }
        document.addEventListener('click', function(e) {
            var wrapper = document.querySelector('.profile-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                var dd = document.getElementById('profileDropdown');
                if (dd) { dd.classList.remove('open'); }
            }
        });
    </script>

</body>

</html>
