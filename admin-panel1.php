<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

include('newfunc.php');

if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['docsub'])) {
    $doctor = $_POST['doctor'];
    $dpassword = $_POST['dpassword'];
    $demail = $_POST['demail'];
    $spec = $_POST['special'];
    $docFees = $_POST['docFees'];
    
    $m_start = $_POST['m_start'] . ':00';
    $m_end = $_POST['m_end'] . ':00';
    $m_cap = (int)$_POST['m_cap'];
    
    $e_start = $_POST['e_start'] . ':00';
    $e_end = $_POST['e_end'] . ':00';
    $e_cap = (int)$_POST['e_cap'];

    $query = "insert into doctb(username,password,email,spec,docFees,m_start,m_end,m_cap,e_start,e_end,e_cap)values('$doctor','$dpassword','$demail','$spec','$docFees','$m_start','$m_end',$m_cap,'$e_start','$e_end',$e_cap)";
    $result = mysqli_query($con, $query);
    if ($result) {
        echo "<script>alert('Doctor added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding doctor');</script>";
    }
}


if (isset($_POST['docsub1'])) {
    $demail = $_POST['demail'];
    $query = "delete from doctb where email='$demail';";
    $result = mysqli_query($con, $query);
    if ($result) {
        echo "<script>alert('Doctor removed successfully!');</script>";
    } else {
        echo "<script>alert('Unable to delete!');</script>";
    }
}

if (isset($_GET['del_sched'])) {
    $id = mysqli_real_escape_string($con, $_GET['del_sched']);
    $query = mysqli_query($con, "delete from doctor_schedule where id='$id'");
    if ($query) {
        echo "<script>alert('Shift removed successfully!');
    window.location.href = 'admin-panel1.php#list-sched';</script>";
    }
}

if (isset($_GET['approve_leave'])) {
    $id = mysqli_real_escape_string($con, $_GET['approve_leave']);

    $leave_query = mysqli_query($con, "SELECT doctor, leave_date FROM doctor_leaves WHERE id='$id'");
    if ($row = mysqli_fetch_assoc($leave_query)) {
        $doctor = $row['doctor'];
        $leave_date = $row['leave_date'];

        mysqli_query($con, "UPDATE doctor_leaves SET status='Approved' WHERE id='$id'");

        // Auto-cancel appointments on this date
        $app_query = mysqli_query($con, "SELECT ID, pid, fname, lname, contact FROM appointmenttb WHERE doctor='$doctor' AND appdate='$leave_date' AND userStatus='1' AND doctorStatus='1'");

        include_once('whatsapp_notification.php');

        while ($app_row = mysqli_fetch_assoc($app_query)) {
            $app_id = $app_row['ID'];
            $contact = $app_row['contact'];
            $fname = $app_row['fname'];

            mysqli_query($con, "UPDATE appointmenttb SET doctorStatus='0' WHERE ID='$app_id'");

            // Find alternate doctors
            $day_of_week = date("l", strtotime($leave_date));
            $spec_query = mysqli_query($con, "SELECT spec FROM doctb WHERE username='$doctor'");
            $alt_msg = "";

            if ($spec_row = mysqli_fetch_assoc($spec_query)) {
                $spec = $spec_row['spec'];
                $alt_docs_query = mysqli_query($con, "SELECT doctb.username FROM doctb JOIN doctor_schedule ON doctb.username = doctor_schedule.doctor_name WHERE doctb.spec='$spec' AND doctb.username != '$doctor' AND doctor_schedule.day_of_week='$day_of_week'");

                $alt_doc_names = [];
                while ($alt_row = mysqli_fetch_assoc($alt_docs_query)) {
                    $alt_doc_names[] = "Dr. " . $alt_row['username'];
                }
                if (count($alt_doc_names) > 0) {
                    $alt_msg = " We suggest booking with an alternate specialist today: " . implode(", ", $alt_doc_names) . ".";
                }
            }

            $msg = "Dear $fname, unfortunately Dr. $doctor is unavailable on $leave_date. Your appointment has been cancelled.$alt_msg Please log in to reschedule.";
            sendWhatsAppNotification($contact, $msg);
        }

        $_SESSION['alert_msg'] = "Leave approved and affected appointments cancelled.";
    }
    header("Location: admin-panel1.php#list-leave");
    exit();
}

if (isset($_GET['reject_leave'])) {
    $id = mysqli_real_escape_string($con, $_GET['reject_leave']);
    mysqli_query($con, "UPDATE doctor_leaves SET status='Rejected' WHERE id='$id'");
    $_SESSION['alert_msg'] = "Leave rejected.";
    header("Location: admin-panel1.php#list-leave");
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
    <title>Receptionist Dashboard – Global Hospital</title>
</head>

<body class="dashboard-body">

    <script>
        var check = function () {
            if (document.getElementById('dpassword').value == document.getElementById('cdpassword').value) {
                document.getElementById('message').style.color = '#5dd05d';
                document.getElementById('message').innerHTML = 'Matched';
            } else {
                document.getElementById('message').style.color = '#f55252';
                document.getElementById('message').innerHTML = 'Not Matching';
            }
        }
        function alphaOnly(event) {
            var key = event.keyCode;
            return ((key >= 65 && key <= 90) || key == 8 || key == 32);
        };
    </script>

    <?php
    if (isset($_SESSION['alert_msg'])) {
        echo "<script>alert('" . $_SESSION['alert_msg'] . "');</script>";
        unset($_SESSION['alert_msg']);
    }
    ?>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <nav class="sidebar" id="sidebar">
        <a class="sidebar-brand" href="#">
            <div class="brand-icon"><i class="fa fa-hospital-o"></i></div>
            <div>
                <span class="brand-name">Global Hospital</span>
                <span class="brand-sub">Receptionist Panel</span>
            </div>
        </a>
        <div class="sidebar-section-label">Navigation</div>
        <div class="list-group" id="list-tab" role="tablist">
            <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list"
                href="#list-dash" role="tab">
                <i class="fa fa-tachometer"></i> Dashboard
            </a>
            <a class="list-group-item list-group-item-action" href="#list-doc" id="list-doc-list" role="tab"
                data-toggle="list">
                <i class="fa fa-user-md"></i> Doctor List
            </a>
            <a class="list-group-item list-group-item-action" href="#list-pat" id="list-pat-list" role="tab"
                data-toggle="list">
                <i class="fa fa-users"></i> Patient List
            </a>
            <a class="list-group-item list-group-item-action" href="#list-app" id="list-app-list" role="tab"
                data-toggle="list">
                <i class="fa fa-calendar-check-o"></i> Appointment Details
            </a>
            <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab"
                data-toggle="list">
                <i class="fa fa-file-text-o"></i> Prescription List
            </a>
            <a class="list-group-item list-group-item-action" href="#list-leave" id="list-leave-list" role="tab"
                data-toggle="list">
                <i class="fa fa-calendar-times-o"></i> Manage Leaves
                <?php
                $pending_leaves_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM doctor_leaves WHERE status='Pending'"));
                if ($pending_leaves_count['cnt'] > 0) {
                    echo '<span class="badge badge-danger ml-1">' . $pending_leaves_count['cnt'] . '</span>';
                }
                ?>
            </a>
            <a class="list-group-item list-group-item-action" href="#list-sched" id="list-sched-list" role="tab"
                data-toggle="list">
                <i class="fa fa-clock-o"></i> Doctor Schedule
            </a>

            <a class="list-group-item list-group-item-action" href="#list-settings" id="list-adoc-list" role="tab"
                data-toggle="list">
                <i class="fa fa-user-plus"></i> Add Doctor
            </a>
            <a class="list-group-item list-group-item-action" href="#list-settings1" id="list-ddoc-list" role="tab"
                data-toggle="list">
                <i class="fa fa-user-times"></i> Delete Doctor
            </a>
            <a class="list-group-item list-group-item-action" href="#list-mes" id="list-mes-list" role="tab"
                data-toggle="list">
                <i class="fa fa-envelope-o"></i> Queries
            </a>
            <a class="list-group-item list-group-item-action" href="scan.php" target="_blank">
                <i class="fa fa-qrcode"></i> Scan QR Code
            </a>
        </div>
    </nav>

    <!-- ═══════════════ MAIN CONTENT ═══════════════ -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
            <h5 class="page-title"><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?><span id="headerSubtitle"> – Dashboard</span></h5>
            <div class="header-right">
                <span class="header-date"><i class="fa fa-calendar"></i> <span id="headerDate"></span></span>
                <div class="profile-wrapper">
                    <button class="user-badge" id="profileToggle" onclick="toggleProfile(event)" aria-expanded="false">
                        <div class="avatar"><i class="fa fa-user"></i></div>
                        <span class="badge-name"><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></span>
                        <i class="fa fa-angle-down" style="font-size:0.75rem; color:#6b7280; margin-left:2px;"></i>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-drop-header">
                            <div class="profile-drop-avatar"><i class="fa fa-user-circle-o" style="font-size:1.4rem;"></i></div>
                            <div>
                                <div class="profile-drop-name"><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></div>
                                <div class="profile-drop-role">Receptionist</div>
                            </div>
                        </div>
                        <div class="profile-drop-body">
                            <div class="profile-drop-item">
                                <i class="fa fa-shield"></i>
                                <span class="item-label">Role</span>
                                <span class="item-value">Receptionist / Admin</span>
                            </div>
                            <div class="profile-drop-item">
                                <i class="fa fa-hospital-o"></i>
                                <span class="item-label">Hospital</span>
                                <span class="item-value">Global Hospital</span>
                            </div>
                        </div>
                        <div class="profile-drop-footer">
                            <a href="logout1.php"><i class="fa fa-sign-out"></i> Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-user-md fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Doctor List</h5>
                                        <script>function clickDiv(id) { document.querySelector(id).click(); }</script>
                                        <p class="card-text mt-3"><a href="#list-doc"
                                                onclick="clickDiv('#list-doc-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">View Doctors</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-users fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Patient List</h5>
                                        <p class="card-text mt-3"><a href="#list-pat"
                                                onclick="clickDiv('#list-pat-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">View Patients</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-paperclip fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Appointment Details</h5>
                                        <p class="card-text mt-3">
                                            <a href="#list-app" onclick="clickDiv('#list-app-list')"
                                                class="btn btn-primary btn-sm px-3 py-2 rounded-pill mr-2">View Appointments</a>
                                            <a href="scan.php" target="_blank"
                                                class="btn btn-outline-success btn-sm px-3 py-2 rounded-pill"><i
                                                    class="fa fa-qrcode"></i> QR Scanner</a>
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
                                                class="fa fa-list-ul fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Prescription List</h5>
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
                                                class="fa fa-plus fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Manage Doctors</h5>
                                        <p class="card-text mt-3">
                                            <a href="#list-settings" onclick="clickDiv('#list-adoc-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill mr-2">Add</a>
                                            <a href="#list-settings1" onclick="clickDiv('#list-ddoc-list')"
                                                class="btn btn-outline-primary btn-sm px-4 py-2 rounded-pill">Delete</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card fixed-card shadow-sm border-0 text-center py-4">
                                    <div class="card-body">
                                        <span class="fa-stack fa-3x mb-3"><i class="fa fa-circle fa-stack-2x"
                                                style="color:#e6f2ff;"></i><i
                                                class="fa fa-calendar-times-o fa-stack-1x text-primary"></i></span>
                                        <h5 class="card-title font-weight-bold">Manage Leaves</h5>
                                        <p class="card-text mt-3"><a href="#list-leave"
                                                onclick="clickDiv('#list-leave-list')"
                                                class="btn btn-primary btn-sm px-4 py-2 rounded-pill">View Leaves</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manage Leaves -->
                <div class="tab-pane" id="list-leave" role="tabpanel" aria-labelledby="list-leave-list">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title font-weight-bold mb-4">Manage Doctor Leaves</h4>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $leave_result = mysqli_query($con, "SELECT * FROM doctor_leaves ORDER BY leave_date DESC");
                                        while ($row = mysqli_fetch_assoc($leave_result)) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $row['doctor']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['leave_date']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['leave_type']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['reason']; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row['status'] == 'Pending')
                                                        echo "<span class='badge badge-warning'>Pending</span>";
                                                    else if ($row['status'] == 'Approved')
                                                        echo "<span class='badge badge-success'>Approved</span>";
                                                    else if ($row['status'] == 'Rejected')
                                                        echo "<span class='badge badge-danger'>Rejected</span>";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 'Pending') { ?>
                                                        <a href="admin-panel1.php?approve_leave=<?php echo $row['id']; ?>"
                                                            class="btn btn-success btn-sm"
                                                            onClick="return confirm('Approve leave? Appointments will be auto-cancelled.');">Approve</a>
                                                        <a href="admin-panel1.php?reject_leave=<?php echo $row['id']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onClick="return confirm('Reject this leave?');">Reject</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor List -->
                <div class="tab-pane" id="list-doc" role="tabpanel">
                    <div class="col-md-8 mb-3">
                        <form class="form-group" action="doctorsearch.php" method="post">
                            <div class="row">
                                <div class="col-md-10"><input type="text" name="doctor_contact"
                                        placeholder="Enter Email ID" class="form-control"></div>
                                <div class="col-md-2"><input type="submit" name="doctor_search_submit"
                                        class="btn btn-primary" value="Search"></div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Specialization</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Fees</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $con2 = mysqli_connect("localhost", "root", "", "myhmsdb");
                            $result2 = mysqli_query($con2, "select * from doctb");
                            while ($row = mysqli_fetch_array($result2)) {
                                echo "<tr><td>{$row['username']}</td><td>{$row['spec']}</td><td>{$row['email']}</td><td>{$row['password']}</td><td>{$row['docFees']}</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    </div><!-- /table-responsive -->
                </div>

                <!-- Patient List -->
                <div class="tab-pane fade" id="list-pat" role="tabpanel">
                    <div class="col-md-8 mb-3">
                        <form class="form-group" action="patientsearch.php" method="post">
                            <div class="row">
                                <div class="col-md-10"><input type="text" name="patient_contact"
                                        placeholder="Enter Contact" class="form-control"></div>
                                <div class="col-md-2"><input type="submit" name="patient_search_submit"
                                        class="btn btn-primary" value="Search"></div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $con3 = mysqli_connect("localhost", "root", "", "myhmsdb");
                            $result3 = mysqli_query($con3, "select * from patreg");
                            while ($row = mysqli_fetch_array($result3)) {
                                echo "<tr><td>{$row['pid']}</td><td>{$row['fname']}</td><td>{$row['lname']}</td><td>{$row['gender']}</td><td>{$row['email']}</td><td>{$row['contact']}</td><td>{$row['password']}</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    </div><!-- /table-responsive -->
                </div>

                <!-- Prescription List -->
                <div class="tab-pane fade" id="list-pres" role="tabpanel">
                    <ul class="nav nav-pills nav-fill mb-4" id="adminPresTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="admin-new-pres-tab" data-custom-tab="admin-new-pres" href="javascript:void(0)" role="tab">
                                <i class="fa fa-file-text-o"></i> New Prescriptions (Today)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admin-old-pres-tab" data-custom-tab="admin-old-pres" href="javascript:void(0)" role="tab">
                                <i class="fa fa-history"></i> Old Prescriptions
                            </a>
                        </li>
                    </ul>

                    <div class="custom-tab-content" id="adminPresTabsContent">
                        <div class="custom-tab-pane" id="admin-new-pres" style="display:block;">
                            <div class="table-responsive mb-5">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Patient ID</th>
                                    <th>Appt ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Appt Date</th>
                                    <th>Time</th>
                                    <th>Disease</th>
                                    <th>Allergy</th>
                                    <th>Prescription</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $con4 = mysqli_connect("localhost", "root", "", "myhmsdb");
                                $result4 = mysqli_query($con4, "select p.*, a.paymentStatus from prestb p inner join appointmenttb a on p.ID=a.ID WHERE p.appdate = CURDATE()");
                                while ($row = mysqli_fetch_array($result4)) {
                                    echo "<tr><td>{$row['doctor']}</td><td>{$row['pid']}</td><td>{$row['ID']}</td><td>{$row['fname']}</td><td>{$row['lname']}</td><td>{$row['appdate']}</td><td>{$row['apptime']}</td><td>{$row['disease']}</td><td>{$row['allergy']}</td><td>{$row['prescription']}</td><td>{$row['paymentStatus']}</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                            </div>
                        </div>
                        
                        <div class="custom-tab-pane" id="admin-old-pres" style="display:none;">
                            <div class="table-responsive mb-5">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Patient ID</th>
                                    <th>Appt ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Appt Date</th>
                                    <th>Time</th>
                                    <th>Disease</th>
                                    <th>Allergy</th>
                                    <th>Prescription</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result4 = mysqli_query($con4, "select p.*, a.paymentStatus from prestb p inner join appointmenttb a on p.ID=a.ID WHERE p.appdate < CURDATE()");
                                while ($row = mysqli_fetch_array($result4)) {
                                    echo "<tr><td>{$row['doctor']}</td><td>{$row['pid']}</td><td>{$row['ID']}</td><td>{$row['fname']}</td><td>{$row['lname']}</td><td>{$row['appdate']}</td><td>{$row['apptime']}</td><td>{$row['disease']}</td><td>{$row['allergy']}</td><td>{$row['prescription']}</td><td>{$row['paymentStatus']}</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="tab-pane fade" id="list-app" style="font-size:15px;" role="tabpanel">
                    <div class="col-md-8 mb-3">
                        <form class="form-group" action="appsearch.php" method="post">
                            <div class="row">
                                <div class="col-md-10"><input type="text" name="app_contact" placeholder="Enter Contact" class="form-control"></div>
                                <div class="col-md-2"><input type="submit" name="app_search_submit" class="btn btn-primary" value="Search"></div>
                            </div>
                        </form>
                    </div>

                    <ul class="nav nav-pills nav-fill mb-4" id="adminAppTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="admin-today-apps-tab" data-custom-tab="admin-today-apps" href="javascript:void(0)" role="tab">
                                <i class="fa fa-calendar-check-o"></i> Today's Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admin-upcoming-apps-tab" data-custom-tab="admin-upcoming-apps" href="javascript:void(0)" role="tab">
                                <i class="fa fa-calendar-plus-o"></i> Upcoming
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admin-past-apps-tab" data-custom-tab="admin-past-apps" href="javascript:void(0)" role="tab">
                                <i class="fa fa-history"></i> Past / Completed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admin-cancelled-apps-tab" data-custom-tab="admin-cancelled-apps" href="javascript:void(0)" role="tab">
                                <i class="fa fa-times-circle"></i> Cancelled
                            </a>
                        </li>
                    </ul>

                    <div class="custom-tab-content" id="adminAppTabsContent">
                    <?php
                    function renderAdminApptTable($con5, $tab_id, $is_active, $query) {
                        $display = $is_active ? "block" : "none";
                        echo "<div class='custom-tab-pane' id='$tab_id' style='display:$display;'>";
                        echo "<div class='table-responsive mb-5'><table class='table table-hover'><thead><tr>";
                        echo "<th>Appt ID</th><th>Patient ID</th><th>First Name</th><th>Last Name</th><th>Contact</th><th>Doctor</th><th>Fees</th><th>Date</th><th>Time</th><th>Arrival</th><th>Status</th><th>Payment</th>";
                        echo "</tr></thead><tbody>";
                        $result = mysqli_query($con5, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $arrival = ($row['arrival_status'] == 1) ? "<span class='badge badge-success'>Checked-In</span>" : "<span class='badge badge-secondary'>Pending</span>";
                            $status = "Active";
                            if ($row['userStatus'] == 0) $status = "Cancelled by Patient";
                            if ($row['doctorStatus'] == 0) $status = "Cancelled by Doctor";
                            
                            echo "<tr>";
                            echo "<td>{$row['ID']}</td><td>{$row['pid']}</td><td>{$row['fname']}</td><td>{$row['lname']}</td><td>{$row['contact']}</td><td>{$row['doctor']}</td><td>{$row['docFees']}</td><td>{$row['appdate']}</td><td>{$row['apptime']}</td><td>{$arrival}</td><td>{$status}</td><td>{$row['paymentStatus']}</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                        echo "</div>";
                    }

                    $con5 = mysqli_connect("localhost", "root", "", "myhmsdb");

                    // Today's Appointments
                    renderAdminApptTable($con5, "admin-today-apps", true, "SELECT * FROM appointmenttb WHERE appdate = CURDATE() AND userStatus='1' AND doctorStatus='1'");

                    // Upcoming Appointments
                    renderAdminApptTable($con5, "admin-upcoming-apps", false, "SELECT * FROM appointmenttb WHERE appdate > CURDATE() AND userStatus='1' AND doctorStatus='1'");

                    // Past/Completed Appointments
                    renderAdminApptTable($con5, "admin-past-apps", false, "SELECT * FROM appointmenttb WHERE appdate < CURDATE() AND userStatus='1' AND doctorStatus='1'");

                    // Cancelled Appointments
                    renderAdminApptTable($con5, "admin-cancelled-apps", false, "SELECT * FROM appointmenttb WHERE userStatus='0' OR doctorStatus='0'");
                    ?>
                    </div>
                </div>

                <!-- Doctor Schedule -->
                <div class="tab-pane fade" id="list-sched" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-4">Doctor Schedules</h5>
                            <div class="table-responsive">
                            <table class="table table-hover text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Day</th>
                                        <th>Session</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Avg (min)</th>
                                        <th>Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr = mysqli_query($con, "SELECT * FROM doctor_schedule ORDER BY doctor_name, day_of_week");
                                    while ($s = mysqli_fetch_assoc($sr)) { ?>
                                        <tr>
                                            <td class="font-weight-bold text-primary">
                                                <?php echo $s['doctor_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['day_of_week']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['session_type']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['start_time']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['end_time']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['avg_consult_time']; ?>
                                            </td>
                                            <td>
                                                <?php echo $s['session_capacity']; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            </div><!-- /table-responsive -->
                        </div>
                    </div>
                </div>



                <!-- Add Doctor -->
                <div class="tab-pane fade" id="list-settings" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><i class="fa fa-user-plus"></i> Add Doctor</h5>
                            <form class="form-group" method="post" action="admin-panel1.php">
                                <div class="row">
                                    <div class="col-md-4"><label>Doctor Name:</label></div>
                                    <div class="col-md-8"><input type="text" class="form-control" name="doctor"
                                            onkeydown="return alphaOnly(event);" required></div><br><br>
                                    <div class="col-md-4"><label>Specialization:</label></div>
                                    <div class="col-md-8">
                                        <select name="special" class="form-control" required>
                                            <option value="head" disabled selected>Select Specialization</option>
                                            <option value="General">General</option>
                                            <option value="Cardiology">Cardiology</option>
                                            <option value="Neurology">Neurology</option>
                                            <option value="Orthopedics">Orthopedics</option>
                                            <option value="Pulmonology">Pulmonology</option>
                                            <option value="Dermatology">Dermatology</option>
                                            <option value="Gastroenterology">Gastroenterology</option>
                                            <option value="Ophthalmology">Ophthalmology</option>
                                            <option value="ENT">ENT</option>
                                            <option value="Pediatrics">Pediatrics</option>
                                            <option value="Endocrinology">Endocrinology</option>
                                        </select>
                                    </div><br><br>
                                    <div class="col-md-4"><label>Email ID:</label></div>
                                    <div class="col-md-8"><input type="email" class="form-control" name="demail"
                                            required></div>
                                    <br><br>
                                    <div class="col-md-4"><label>Password:</label></div>
                                    <div class="col-md-8"><input type="password" class="form-control" onkeyup="check();"
                                            name="dpassword" id="dpassword" required></div><br><br>
                                    <div class="col-md-4"><label>Confirm Password:</label></div>
                                    <div class="col-md-8" id="cpass"><input type="password" class="form-control"
                                            onkeyup="check();" name="cdpassword" id="cdpassword"
                                            required>&nbsp;&nbsp;<span id="message"></span></div><br><br>
                                    <div class="col-md-4"><label>Consultancy Fees:</label></div>
                                    <div class="col-md-8"><input type="text" class="form-control" name="docFees"
                                            required></div>
                                    <br><br>
                                </div>
                                <hr>
                                <h6 class="font-weight-bold mb-3 text-primary">Default Morning Shift</h6>
                                <div class="row">
                                    <div class="col-md-4"><label>Start Time:</label></div>
                                    <div class="col-md-8"><input type="time" class="form-control" name="m_start" value="10:00" required></div><br><br>
                                    <div class="col-md-4"><label>End Time:</label></div>
                                    <div class="col-md-8"><input type="time" class="form-control" name="m_end" value="13:00" required></div><br><br>
                                    <div class="col-md-4"><label>Patient Capacity:</label></div>
                                    <div class="col-md-8"><input type="number" class="form-control" name="m_cap" value="12" required></div><br><br>
                                </div>
                                <hr>
                                <h6 class="font-weight-bold mb-3 text-primary">Default Evening Shift</h6>
                                <div class="row">
                                    <div class="col-md-4"><label>Start Time:</label></div>
                                    <div class="col-md-8"><input type="time" class="form-control" name="e_start" value="17:00" required></div><br><br>
                                    <div class="col-md-4"><label>End Time:</label></div>
                                    <div class="col-md-8"><input type="time" class="form-control" name="e_end" value="20:00" required></div><br><br>
                                    <div class="col-md-4"><label>Patient Capacity:</label></div>
                                    <div class="col-md-8"><input type="number" class="form-control" name="e_cap" value="12" required></div><br><br>
                                </div>
                                <input type="submit" name="docsub" value="Add Doctor" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Doctor -->
                <div class="tab-pane fade" id="list-settings1" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><i class="fa fa-user-times"></i> Delete Doctor</h5>
                            <form class="form-group" method="post" action="admin-panel1.php">
                                <div class="row">
                                    <div class="col-md-4"><label>Email ID:</label></div>
                                    <div class="col-md-8"><input type="email" class="form-control" name="demail"
                                            required></div>
                                    <br><br>
                                </div>
                                <input type="submit" name="docsub1" value="Delete Doctor" class="btn btn-danger"
                                    onclick="return confirm('Do you really want to delete?')">
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Queries -->
                <div class="tab-pane fade" id="list-mes" role="tabpanel">
                    <div class="col-md-8 mb-3">
                        <form class="form-group" action="messearch.php" method="post">
                            <div class="row">
                                <div class="col-md-10"><input type="text" name="mes_contact" placeholder="Enter Contact"
                                        class="form-control"></div>
                                <div class="col-md-2"><input type="submit" name="mes_search_submit"
                                        class="btn btn-primary" value="Search"></div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $con6 = mysqli_connect("localhost", "root", "", "myhmsdb");
                            $result6 = mysqli_query($con6, "select * from contact;");
                            while ($row = mysqli_fetch_array($result6)) {
                                echo "<tr><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['contact']}</td><td>{$row['message']}</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    </div><!-- /table-responsive -->
                </div>

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
                document.querySelector('.top-header .page-title span').textContent = ' – ' + $(e.target).text().trim();
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
            var btn  = document.getElementById('profileToggle');
            var dd   = document.getElementById('profileDropdown');
            var rect = btn.getBoundingClientRect();
            var ddW  = dd.offsetWidth || 290;
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

        // Custom tab switcher for inner sub-tabs (Prescriptions & Appointments)
        // Uses data-custom-tab instead of Bootstrap's data-toggle="tab" to avoid
        // global tab plugin bleeding content across sidebar sections.
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-custom-tab]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var targetId = this.getAttribute('data-custom-tab');
                    // Find the parent nav container
                    var navParent = this.closest('ul');
                    // Deactivate all pills in the same nav
                    navParent.querySelectorAll('.nav-link').forEach(function (pill) {
                        pill.classList.remove('active');
                    });
                    // Activate the clicked pill
                    this.classList.add('active');
                    // Find the tab-content parent (sibling of the ul)
                    var tabContent = navParent.nextElementSibling;
                    // Hide all panes in this content area
                    tabContent.querySelectorAll('.custom-tab-pane').forEach(function (pane) {
                        pane.style.display = 'none';
                    });
                    // Show the target pane
                    var target = document.getElementById(targetId);
                    if (target) { target.style.display = 'block'; }
                });
            });
        });
    </script>

</body>

</html>
