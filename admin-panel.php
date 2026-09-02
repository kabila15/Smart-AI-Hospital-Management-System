<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include('func.php');
include('newfunc.php');
include('whatsapp_notification.php');
$con = mysqli_connect("localhost", "root", "", "myhmsdb");


if (!isset($_SESSION['pid']) || empty($_SESSION['pid'])) {
  header("Location: index1.php");
  exit();
}

$pid = $_SESSION['pid'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$fname = $_SESSION['fname'];
$gender = $_SESSION['gender'];
$lname = $_SESSION['lname'];
$contact = $_SESSION['contact'];


if (isset($_POST['app-submit'])) {
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
  $docFees = mysqli_real_escape_string($con, $_POST['docFees']);
  $appdate = mysqli_real_escape_string($con, $_POST['appdate']);
  $session_type = isset($_POST['session_type']) ? mysqli_real_escape_string($con, $_POST['session_type']) : 'Morning';

  // Find doctor's session for this day
  $day_of_week = date("l", strtotime($appdate));

  $leave_check = mysqli_query($con, "SELECT id FROM doctor_leaves WHERE doctor='$doctor' AND leave_date='$appdate' AND status='Approved'");
  if (mysqli_num_rows($leave_check) > 0) {
    echo "<script>alert('Sorry, Dr. $doctor is on leave for the selected date. Please choose another date or doctor.'); window.location.href = 'admin-panel.php';</script>";
    exit();
  }

  $sched_query = mysqli_query($con, "select * from doctor_schedule where doctor_name='$doctor' and day_of_week='$day_of_week' and session_type='$session_type' LIMIT 1");

  if (mysqli_num_rows($sched_query) > 0) {
    $sched = mysqli_fetch_assoc($sched_query);
    $start_time = $sched['start_time'];
    $avg_time = $sched['avg_consult_time'] ? $sched['avg_consult_time'] : 15;
    $capacity = $sched['session_capacity'] ? $sched['session_capacity'] : 12;

    // Count existing tokens for this doctor and date and session
    $count_query = mysqli_query($con, "select count(*) as total from appointmenttb where doctor='$doctor' and appdate='$appdate' and session_type='$session_type' and userStatus='1'");
    $count_data = mysqli_fetch_assoc($count_query);
    $current_tokens = $count_data['total'];

    if ($current_tokens < $capacity) {
      $token_no = $current_tokens + 1;

      // Calculate Expected Time: start_time + (token_no - 1) * avg_time
      $seconds_to_add = ($token_no - 1) * $avg_time * 60;
      $expected_time = date("H:i:s", strtotime($start_time) + $seconds_to_add);

      $symptoms = isset($_POST['symptoms']) ? mysqli_real_escape_string($con, $_POST['symptoms']) : '';
      $is_priority = isset($_POST['is_priority']) ? (int) $_POST['is_priority'] : 0;

      // Priority sorting or assignment logic could be added here if we had more complex token management
      // But for now, we just save the flag so the doctor sees it. Priority patients could technically bypass capacity if needed by the hospital admin.

      $is_priority = isset($_POST['is_priority']) ? (int) $_POST['is_priority'] : 0;
      $qr_token = md5(uniqid(rand(), true));

      $query = "insert into appointmenttb(pid,fname,lname,gender,email,contact,doctor,docFees,appdate,apptime,userStatus,doctorStatus,token_no,expected_time,serving_status,paymentStatus,symptom_text,is_priority,session_type,qr_token) values('$pid','$fname','$lname','$gender','$email','$contact','$doctor','$docFees','$appdate','$expected_time','1','1','$token_no','$expected_time','0','Pending','$symptoms','$is_priority','$session_type','$qr_token')";
      $result = mysqli_query($con, $query);
      if ($result) {
        // Use the real LAN IP so the link works on the patient's mobile device (not just 'localhost')
        $lan_ip = '10.58.160.167';
        if (!empty($_SERVER['HTTP_HOST'])) {
            $host_parts = explode(':', $_SERVER['HTTP_HOST']);
            $h_ip = trim($host_parts[0]);
            if (filter_var($h_ip, FILTER_VALIDATE_IP) && $h_ip !== '127.0.0.1' && strpos($h_ip, '172.') !== 0) {
                $lan_ip = $h_ip;
            }
        }
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $live_link = $scheme . '://' . $lan_ip . '/q.php?token=' . $qr_token;

        $msg = "Hello $fname $lname, your booking with Dr. $doctor on $appdate is confirmed.\nToken: #$token_no\nExpected Time: $expected_time\n\nPlease show this QR code at reception.\n\nTrack your live queue status here:\n" . $live_link;

        $appointmentId = mysqli_insert_id($con);
        if (!$appointmentId)
          $appointmentId = $token_no;

        $appDetails = [
          'appointmentId' => $appointmentId,
          'patientName' => trim("$fname $lname"),
          'doctorName' => $doctor,
          'date' => $appdate,
          'time' => $expected_time
        ];

        sendWhatsAppNotification($contact, $msg, $appDetails);
        echo "<script>alert('Appointment booked! Token: #$token_no. Approximate time: $expected_time'); window.location.href = 'admin-panel.php';</script>";
      } else {
        echo "<script>alert('Error processing booking: " . mysqli_error($con) . "');</script>";
      }
    } else {
      echo "<script>alert('Sorry, this doctor\'s session is full for the selected date.');</script>";
    }
  } else {
    echo "<script>alert('This doctor has no scheduled session for $day_of_week. Please choose another date.');</script>";
  }
}

if (isset($_GET['cancel'])) {
  $id = mysqli_real_escape_string($con, $_GET['ID']);
  $query = mysqli_query($con, "update appointmenttb set userStatus='0' where ID = '$id'");
  if ($query) {
    $pt_query = mysqli_query($con, "SELECT * FROM appointmenttb WHERE ID='$id'");
    if(mysqli_num_rows($pt_query) > 0) {
        $pt = mysqli_fetch_assoc($pt_query);
        $contact = $pt['contact'];
        $doc = $pt['doctor'];
        $date = date("d M Y", strtotime($pt['appdate']));
        $msg = "🚨 *Appointment Cancelled*\n\nHello " . $pt['fname'] . ",\nYour appointment with Dr. $doc on $date has been successfully cancelled.\n\nThank you.";
        sendWhatsAppNotification($contact, $msg);
    }
    echo "<script>alert('Your appointment successfully cancelled');
      window.location.href = 'admin-panel.php#app-hist';</script>";
  }
}

?>
<!DOCTYPE html>
<?php





function generate_bill()
{
  $con = mysqli_connect("localhost", "root", "", "myhmsdb");
  $pid = $_SESSION['pid'];
  $output = '';
  $query = mysqli_query($con, "select p.pid,p.ID,p.fname,p.lname,p.doctor,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,a.docFees from prestb p inner join appointmenttb a on p.ID=a.ID and p.pid = '$pid' and p.ID = '" . $_GET['ID'] . "'");
  while ($row = mysqli_fetch_array($query)) {
    $output .= '
    <label> Patient ID : </label>' . $row["pid"] . '<br/><br/>
    <label> Appointment ID : </label>' . $row["ID"] . '<br/><br/>
    <label> Patient Name : </label>' . $row["fname"] . ' ' . $row["lname"] . '<br/><br/>
    <label> Doctor Name : </label>' . $row["doctor"] . '<br/><br/>
    <label> Appointment Date : </label>' . $row["appdate"] . '<br/><br/>
    <label> Appointment Time : </label>' . $row["apptime"] . '<br/><br/>
    <label> Disease : </label>' . $row["disease"] . '<br/><br/>
    <label> Allergies : </label>' . $row["allergy"] . '<br/><br/>
    <label> Prescription : </label>' . $row["prescription"] . '<br/><br/>
    <label> Fees Paid : </label>' . $row["docFees"] . '<br/>
    
    ';

  }

  return $output;
}


if (isset($_GET["generate_bill"])) {
  require_once("TCPDF/tcpdf.php");
  $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $obj_pdf->SetCreator(PDF_CREATOR);
  $obj_pdf->SetTitle("Generate Bill");
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
  $obj_pdf->SetHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $obj_pdf->SetFooterFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $obj_pdf->SetDefaultMonospacedFont('helvetica');
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
  $obj_pdf->SetPrintHeader(false);
  $obj_pdf->SetPrintFooter(false);
  $obj_pdf->SetAutoPageBreak(TRUE, 10);
  $obj_pdf->SetFont('helvetica', '', 12);
  $obj_pdf->AddPage();

  $content = '';

  $content .= '
      <br/>
      <h2 align ="center"> Global Hospital</h2></br>
      <h3 align ="center"> Payment Receipt</h3>
      <p align="center">Received with thanks by: <strong>Hospital Administration</strong></p>
      <hr>
  ';

  $content .= generate_bill();
  $obj_pdf->writeHTML($content);
  ob_end_clean();
  $obj_pdf->Output("bill.pdf", 'I');

}

function get_specs()
{
  $con = mysqli_connect("localhost", "root", "", "myhmsdb");
  $query = mysqli_query($con, "select username,spec from doctb");
  $docarray = array();
  while ($row = mysqli_fetch_assoc($query)) {
    $docarray[] = $row;
  }
  return json_encode($docarray);
}

?>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Dashboard CSS -->
  <link rel="stylesheet" href="dashboard.css?v=2.0">
  <title>Patient Dashboard – Global Hospital</title>
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
        <span class="brand-sub">Patient Portal</span>
      </div>
    </a>

    <div class="sidebar-section-label">Navigation</div>

    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list" href="#list-dash"
        role="tab">
        <i class="fa fa-tachometer"></i> Dashboard
      </a>
      <a class="list-group-item list-group-item-action" id="list-home-list" data-toggle="list" href="#list-home"
        role="tab">
        <i class="fa fa-calendar-plus-o"></i> Book Appointment
      </a>
      <a class="list-group-item list-group-item-action" href="#app-hist" id="list-pat-list" role="tab"
        data-toggle="list">
        <i class="fa fa-history"></i> Appointment History
      </a>
      <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab"
        data-toggle="list">
        <i class="fa fa-file-text-o"></i> Prescriptions
      </a>
    </div>

  </nav>

  <!-- ═══════════════ MAIN CONTENT ═══════════════ -->
  <div class="main-content">

    <!-- Top Header -->
    <div class="top-header">
      <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fa fa-bars"></i>
      </button>
      <h5 class="page-title">
        <?php echo $username; ?><span id="headerSubtitle"> – Dashboard</span>
      </h5>
      <div class="header-right">
        <span class="header-date">
          <i class="fa fa-calendar"></i>
          <span id="headerDate"></span>
        </span>
        <div class="profile-wrapper">
          <button class="user-badge" id="profileToggle" onclick="toggleProfile(event)" aria-expanded="false">
            <div class="avatar"><?php echo strtoupper(substr($fname, 0, 1)); ?></div>
            <span class="badge-name"><?php echo $fname . ' ' . $lname; ?></span>
            <i class="fa fa-angle-down" style="font-size:0.75rem; color:#6b7280; margin-left:2px;"></i>
          </button>
          <div class="profile-dropdown" id="profileDropdown">
            <div class="profile-drop-header">
              <div class="profile-drop-avatar"><?php echo strtoupper(substr($fname, 0, 1)); ?></div>
              <div>
                <div class="profile-drop-name"><?php echo $fname . ' ' . $lname; ?></div>
                <div class="profile-drop-role">Patient Account</div>
              </div>
            </div>
            <div class="profile-drop-body">
              <div class="profile-drop-item">
                <i class="fa fa-id-badge"></i>
                <span class="item-label">Patient ID</span>
                <span class="item-value"><?php echo $pid; ?></span>
              </div>
              <div class="profile-drop-item">
                <i class="fa fa-envelope"></i>
                <span class="item-label">Email</span>
                <span class="item-value"><?php echo $email; ?></span>
              </div>
              <div class="profile-drop-item">
                <i class="fa fa-phone"></i>
                <span class="item-label">Contact</span>
                <span class="item-value"><?php echo $contact; ?></span>
              </div>
              <div class="profile-drop-item">
                <i class="fa fa-venus-mars"></i>
                <span class="item-label">Gender</span>
                <span class="item-value"><?php echo $gender; ?></span>
              </div>
            </div>
            <div class="profile-drop-footer">
              <a href="logout.php"><i class="fa fa-sign-out"></i> Sign Out</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scrollable Content Area -->
    <div class="content-area">
      <div class="tab-content" id="nav-tabContent">

        <!-- ── Dashboard Tab ── -->
        <div class="tab-pane show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12" style="margin-bottom: 30px;">
                <div class="card" style="border-left: 5px solid #2563eb;">
                  <div class="card-body">
                    <h4 class="card-title" style="color: #1e40af;"><i class="fa fa-clock-o"></i> Live Queue Status</h4>
                    <hr>
                    <?php
                    $con = mysqli_connect("localhost", "root", "", "myhmsdb");
                    $q_query = mysqli_query($con, "SELECT doctor, token_no, expected_time FROM appointmenttb WHERE pid='$pid' AND appdate=CURDATE() AND userStatus='1' ORDER BY ID DESC LIMIT 1");
                    if (mysqli_num_rows($q_query) > 0) {
                      $my_app = mysqli_fetch_assoc($q_query);
                      $my_doc = $my_app['doctor'];
                      $my_token = $my_app['token_no'];
                      $s_query = mysqli_query($con, "SELECT token_no FROM appointmenttb WHERE doctor='$my_doc' AND appdate=CURDATE() AND serving_status=1 LIMIT 1");
                      $serving_token = mysqli_num_rows($s_query) > 0 ? mysqli_fetch_assoc($s_query)['token_no'] : "None yet";
                      $ahead_query = mysqli_query($con, "SELECT COUNT(*) as total FROM appointmenttb WHERE doctor='$my_doc' AND appdate=CURDATE() AND userStatus='1' AND serving_status=0 AND token_no < $my_token");
                      $ahead = mysqli_fetch_assoc($ahead_query)['total'];
                      echo "<div class='row text-center'>
                                  <div class='col-sm-4'><h5>Your Token</h5><h3>#$my_token</h3></div>
                                  <div class='col-sm-4'><h5>Serving</h5><h3>#$serving_token</h3></div>
                                  <div class='col-sm-4'><h5>Ahead</h5><h3>$ahead</h3></div>
                                </div>";
                    } else {
                      echo "<p class='card-text'>You have no appointments scheduled for today. Book one now to see your position in the queue!</p>";
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-4">
                <div class="card fixed-card shadow-sm border-0 text-center py-4 rounded-lg">
                  <div class="card-body">
                    <span class="fa-stack fa-3x mb-3">
                      <i class="fa fa-circle fa-stack-2x" style="color:#e6f2ff;"></i>
                      <i class="fa fa-terminal fa-stack-1x text-primary"></i>
                    </span>
                    <h5 class="card-title font-weight-bold text-dark">Book Appointment</h5>
                    <script>
                      function clickDiv(id) { document.querySelector(id).click(); }
                    </script>
                    <p class="card-text mt-3">
                      <a href="#list-home" onclick="clickDiv('#list-home-list')"
                        class="btn btn-primary btn-sm px-4 py-2 rounded-pill shadow-sm">Book Appointment</a>
                    </p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 mb-4">
                <div class="card fixed-card shadow-sm border-0 text-center py-4 rounded-lg">
                  <div class="card-body">
                    <span class="fa-stack fa-3x mb-3">
                      <i class="fa fa-circle fa-stack-2x" style="color:#e6f2ff;"></i>
                      <i class="fa fa-paperclip fa-stack-1x text-primary"></i>
                    </span>
                    <h5 class="card-title font-weight-bold text-dark">My Appointments</h5>
                    <p class="card-text mt-3">
                      <a href="#app-hist" onclick="clickDiv('#list-pat-list')"
                        class="btn btn-primary btn-sm px-4 py-2 rounded-pill shadow-sm">View History</a>
                    </p>
                  </div>
                </div>
              </div>

              <div class="col-md-4 mb-4">
                <div class="card fixed-card shadow-sm border-0 text-center py-4 rounded-lg">
                  <div class="card-body">
                    <span class="fa-stack fa-3x mb-3">
                      <i class="fa fa-circle fa-stack-2x" style="color:#e6f2ff;"></i>
                      <i class="fa fa-list-ul fa-stack-1x text-primary"></i>
                    </span>
                    <h5 class="card-title font-weight-bold text-dark">Prescriptions</h5>
                    <p class="card-text mt-3">
                      <a href="#list-pres" onclick="clickDiv('#list-pres-list')"
                        class="btn btn-primary btn-sm px-4 py-2 rounded-pill shadow-sm">View List</a>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Book Appointment Tab ── -->
        <div class="tab-pane" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
          <div class="container-fluid">
            <div class="card">
              <div class="card-body">
                <center>
                  <h4>Create an appointment</h4>
                </center><br>
                <form class="form-group" method="post" action="admin-panel.php">
                  
                  <div class="form-group row align-items-center mb-4">
                    <div class="col-md-4"><label for="symptoms" class="col-form-label font-weight-bold text-dark">Symptoms (Describe your condition):</label></div>
                    <div class="col-md-8">
                      <textarea name="symptoms" class="form-control" id="symptoms" rows="3"
                        placeholder="e.g., severe chest pain and shortness of breath" required></textarea>
                      <span id="ai_suggestion_msg" style="display:block; margin-top:5px; font-weight:bold;"></span>
                    </div>
                  </div>

                  <input type="hidden" name="is_priority" id="is_priority" value="0">

                  <script>
                    function fetchPrediction(symptomText) {
                      if (symptomText.length > 5) {
                        fetch(`predict.php?symptoms=${encodeURIComponent(symptomText)}`)
                          .then(res => res.json())
                          .then(data => {
                            if (data.status === 'success') {
                              let specDropdown = document.getElementById('spec');
                              let msgSpan = document.getElementById('ai_suggestion_msg');
                              for (let i = 0; i < specDropdown.options.length; i++) {
                                if (specDropdown.options[i].text.toLowerCase() === data.prediction.toLowerCase()) {
                                  specDropdown.selectedIndex = i;
                                  specDropdown.dispatchEvent(new Event('change'));
                                  break;
                                }
                              }
                              document.getElementById('is_priority').value = data.priority;
                              if (data.priority == 1) {
                                msgSpan.innerHTML = "<span class='text-danger'><i class='fa fa-exclamation-triangle'></i> High Priority Identified: Suggesting " + data.prediction + " slots first.</span>";
                              } else {
                                msgSpan.innerHTML = "<span class='text-success'><i class='fa fa-check'></i> AI Suggestion: " + data.prediction + "</span>";
                              }
                            }
                          });
                      }
                    }
                    document.addEventListener('DOMContentLoaded', function () {
                      document.getElementById('symptoms').addEventListener('blur', function () { fetchPrediction(this.value); });
                      document.getElementById('symptoms').addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); fetchPrediction(this.value); }
                      });
                    });
                  </script>

                  <div class="form-group row align-items-center mb-4">
                    <div class="col-md-4"><label for="spec" class="col-form-label font-weight-bold text-dark">Specialization:</label></div>
                    <div class="col-md-8">
                      <select name="spec" class="form-control" id="spec" required>
                        <option value="" disabled selected>Select Specialization</option>
                        <?php display_specs(); ?>
                      </select>
                    </div>
                  </div>

                  <script>
                    document.addEventListener('DOMContentLoaded', function () {
                      document.getElementById('spec').onchange = function updateDoctors() {
                        let spec = this.value;
                        let doctorSelect = document.getElementById('doctor');
                        let docs = [...doctorSelect.options];
                        doctorSelect.value = "";
                        document.getElementById('docFees').value = "";
                        document.getElementById('date_field').style.display = "none";
                        document.getElementById('time_field').style.display = "none";
                        document.getElementById('availability_msg').innerText = "";
                        docs.forEach((el) => {
                          if (spec === "" || el.value === "" || el.getAttribute("data-spec") === spec) {
                            el.style.display = "block";
                          } else {
                            el.style.display = "none";
                          }
                        });
                        checkAvailability();
                      };
                    });
                  </script>

                  <div class="form-group row align-items-center mb-4">
                    <div class="col-md-4"><label for="doctor" class="col-form-label font-weight-bold text-dark">Doctors:</label></div>
                    <div class="col-md-8">
                      <select name="doctor" class="form-control" id="doctor" required="required">
                        <option value="" disabled selected>Select Doctor</option>
                        <?php display_docs(); ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row align-items-center mb-4">
                    <div class="col-md-4"><label for="docFees" class="col-form-label font-weight-bold text-dark">Consultancy Fees</label></div>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="docFees" id="docFees" readonly="readonly" />
                    </div>
                  </div>

                  <div id="date_field" style="display: none;">
                    <div class="form-group row align-items-center mb-4">
                      <div class="col-md-4"><label for="appdate" class="col-form-label font-weight-bold text-dark">Appointment Date</label></div>
                      <div class="col-md-8">
                        <input type="date" class="form-control" name="appdate" id="appdate" onchange="checkAvailability()" required>
                      </div>
                    </div>
                    
                    <div class="form-group row align-items-center mb-4">
                      <div class="col-md-4"><label for="session_type" class="col-form-label font-weight-bold text-dark">Session</label></div>
                      <div class="col-md-8">
                        <select name="session_type" id="session_type" class="form-control" onchange="checkAvailability()">
                          <option value="Morning">Morning (10:00 AM - 1:00 PM)</option>
                          <option value="Evening">Evening (5:00 PM - 8:00 PM)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div id="time_field" style="display: none;">
                    <input type="hidden" name="apptime" value="00:00:00">
                  </div>

                  <div class="form-group row mb-4">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                      <span id="availability_msg" style="display: block; margin-top: 5px; font-weight: bold;"></span>
                      <div id="suggestion_block"
                        style="display: none; margin-top: 10px; padding: 15px; border: 1px dashed #007bff; border-radius: 8px; background-color: #f8fafc;">
                        <h6 style="color: #007bff; font-weight: 700;" class="mb-2"><i class="fa fa-lightbulb-o"></i> Smart Suggestions:</h6>
                        <ul id="suggestion_list" style="padding-left: 20px; font-size: 0.9em; margin-bottom: 0;"></ul>
                      </div>
                    </div>
                  </div>

                  <script>
                    function checkAvailability() {
                      let doctor = document.getElementById('doctor').value;
                      let date = document.getElementById('appdate').value;
                      let session = document.getElementById('session_type') ? document.getElementById('session_type').value : 'Morning';
                      let msgSpan = document.getElementById('availability_msg');
                      let sugBlock = document.getElementById('suggestion_block');
                      let sugList = document.getElementById('suggestion_list');
                      let submitBtn = document.querySelector('input[name="app-submit"]');
                      if (doctor && date) {
                        fetch(`check_availability.php?doctor=${encodeURIComponent(doctor)}&appdate=${encodeURIComponent(date)}&session_type=${encodeURIComponent(session)}`)
                          .then(response => { if (!response.ok) { throw new Error("Server response was not ok (" + response.status + ")"); } return response.json(); })
                          .then(data => {
                            msgSpan.innerText = ""; sugBlock.style.display = "none"; sugList.innerHTML = "";
                            if (data.status === 'error') { msgSpan.innerText = "Backend Error: " + data.message; msgSpan.className = "text-danger"; submitBtn.disabled = true; }
                            else if (data.status === 'unavailable') { msgSpan.innerText = data.message; msgSpan.className = "text-danger"; submitBtn.disabled = true; showSuggestions(data.suggestions); }
                            else if (data.status === 'full') { msgSpan.innerText = "Doctor is fully booked for today (Capacity: " + data.capacity + ")."; msgSpan.className = "text-warning"; submitBtn.disabled = true; showSuggestions(data.suggestions); }
                            else { msgSpan.innerText = "Available! Tokens booked: " + data.tokens_booked + "/" + data.capacity; msgSpan.className = "text-success"; submitBtn.disabled = false; }
                          })
                          .catch(err => { msgSpan.innerText = "Failed to fetch: " + err.message; msgSpan.className = "text-danger"; });
                      }
                    }
                    function showSuggestions(suggestions) {
                      let sugBlock = document.getElementById('suggestion_block');
                      let hasSuggestions = false;
                      if (suggestions.next_available_date) { addSuggestion(`Next free date: <a href="javascript:void(0)" onclick="applyDate('${suggestions.next_available_date}')">${suggestions.next_available_date}</a>`); hasSuggestions = true; }
                      if (suggestions.alternative_doctors && suggestions.alternative_doctors.length > 0) { suggestions.alternative_doctors.forEach(doc => { addSuggestion(`Available doctor today: <a href="javascript:void(0)" onclick="applyDoctor('${doc}')">Dr. ${doc}</a>`); }); hasSuggestions = true; }
                      if (hasSuggestions) { sugBlock.style.display = "block"; }
                    }
                    function addSuggestion(html) { let li = document.createElement('li'); li.innerHTML = html; document.getElementById('suggestion_list').appendChild(li); }
                    function applyDate(date) { document.getElementById('appdate').value = date; checkAvailability(); }
                    function applyDoctor(docName) {
                      let docSelect = document.getElementById('doctor');
                      for (let i = 0; i < docSelect.options.length; i++) {
                        if (docSelect.options[i].value === docName) { docSelect.selectedIndex = i; docSelect.dispatchEvent(new Event('change')); break; }
                      }
                    }
                    document.addEventListener('DOMContentLoaded', function () {
                      document.getElementById('doctor').addEventListener('change', function () {
                        let selectedOption = this.options[this.selectedIndex];
                        let fees = selectedOption.getAttribute('data-value');
                        document.getElementById('docFees').value = fees;
                        if (this.value) { document.getElementById('date_field').style.display = "block"; document.getElementById('appdate').value = ""; document.getElementById('availability_msg').innerText = ""; checkAvailability(); }
                      });
                      const today = new Date().toISOString().split('T')[0];
                      document.getElementById('appdate').setAttribute('min', today);
                    });
                  </script>

                  <div class="form-group row mt-4">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                      <input type="submit" name="app-submit" value="Create new entry" class="btn btn-primary px-5 py-2.5 rounded-lg font-weight-bold shadow-sm" id="inputbtn">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div><br>
        </div>

        <!-- ── Appointment History Tab ── -->
        <div class="tab-pane" id="app-hist" role="tabpanel" aria-labelledby="list-pat-list">
          <div class="container-fluid">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <ul class="nav nav-pills nav-fill mb-4" id="patientAppTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="upcoming-apps-tab" data-toggle="tab" href="#upcoming-apps" role="tab">
                      <i class="fa fa-calendar-plus-o"></i> Upcoming Appointments
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="past-apps-tab" data-toggle="tab" href="#past-apps" role="tab">
                      <i class="fa fa-history"></i> Past / Completed
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="cancelled-apps-tab" data-toggle="tab" href="#cancelled-apps" role="tab">
                      <i class="fa fa-times-circle"></i> Cancelled
                    </a>
                  </li>
                </ul>

                <div class="tab-content" id="patientAppTabsContent">
                  <div class="tab-pane fade show active" id="upcoming-apps" role="tabpanel">
                    <div class="table-responsive mb-5">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Doctor Name</th>
                        <th scope="col">Consultancy Fees</th>
                        <th scope="col">Date</th>
                        <th scope="col">Token #</th>
                        <th scope="col">Expected Time (Approx)</th>
                        <th scope="col">Current Status</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $con = mysqli_connect("localhost", "root", "", "myhmsdb");
                      $query = "SELECT * FROM appointmenttb WHERE fname ='$fname' AND lname='$lname' AND appdate >= CURDATE() AND userStatus='1' AND doctorStatus='1'";
                      $result = mysqli_query($con, $query);
                      while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row['doctor']; ?></td>
                          <td><?php echo $row['docFees']; ?></td>
                          <td><?php echo $row['appdate']; ?></td>
                          <td><?php echo "#" . $row['token_no']; ?></td>
                          <td><?php echo $row['expected_time']; ?></td>
                          <td>Confirmed</td>
                          <td>
                              <a href="admin-panel.php?ID=<?php echo $row['ID'] ?>&cancel=update"
                                onClick="return confirm('Are you sure you want to cancel this appointment ?')"
                                title="Cancel Appointment">
                                <button class="btn btn-danger btn-sm">Cancel</button>
                              </a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                </div>
                
                <div class="tab-pane fade" id="past-apps" role="tabpanel">
                  <div class="table-responsive mb-5">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Doctor Name</th>
                        <th scope="col">Consultancy Fees</th>
                        <th scope="col">Date</th>
                        <th scope="col">Token #</th>
                        <th scope="col">Expected Time (Approx)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT * FROM appointmenttb WHERE fname ='$fname' AND lname='$lname' AND appdate < CURDATE() AND userStatus='1' AND doctorStatus='1'";
                      $result = mysqli_query($con, $query);
                      while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row['doctor']; ?></td>
                          <td><?php echo $row['docFees']; ?></td>
                          <td><?php echo $row['appdate']; ?></td>
                          <td><?php echo "#" . $row['token_no']; ?></td>
                          <td><?php echo $row['expected_time']; ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                </div>
                
                <div class="tab-pane fade" id="cancelled-apps" role="tabpanel">
                  <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Doctor Name</th>
                        <th scope="col">Consultancy Fees</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT * FROM appointmenttb WHERE fname ='$fname' AND lname='$lname' AND (userStatus='0' OR doctorStatus='0')";
                      $result = mysqli_query($con, $query);
                      while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row['doctor']; ?></td>
                          <td><?php echo $row['docFees']; ?></td>
                          <td><?php echo $row['appdate']; ?></td>
                          <td>
                            <?php if ($row['userStatus'] == 0) {
                              echo "Cancelled by You";
                            } else {
                              echo "Cancelled by Doctor";
                            } ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                </div> <!-- End tab-pane -->
                </div> <!-- End tab-content -->
              </div>
            </div>
          </div><br>
        </div>

        <!-- ── Prescriptions Tab ── -->
        <div class="tab-pane" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
          <div class="container-fluid">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <ul class="nav nav-pills nav-fill mb-4" id="patientPresTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="new-pres-tab" data-toggle="tab" href="#new-pres" role="tab">
                      <i class="fa fa-file-text-o"></i> New Prescriptions (Today)
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="old-pres-tab" data-toggle="tab" href="#old-pres" role="tab">
                      <i class="fa fa-history"></i> Old Prescriptions
                    </a>
                  </li>
                </ul>

                <div class="tab-content" id="patientPresTabsContent">
                  <div class="tab-pane fade show active" id="new-pres" role="tabpanel">
                    <div class="table-responsive mb-5">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Doctor Name</th>
                        <th scope="col">Appointment ID</th>
                        <th scope="col">Appointment Date</th>
                        <th scope="col">Appointment Time</th>
                        <th scope="col">Diseases</th>
                        <th scope="col">Allergies</th>
                        <th scope="col">Prescriptions</th>
                        <th scope="col">Bill Payment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "select p.doctor,p.ID,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,a.paymentStatus,a.docFees from prestb p inner join appointmenttb a on p.ID=a.ID where p.pid='$pid' AND p.appdate=CURDATE();";
                      $result = mysqli_query($con, $query);
                      while ($row = mysqli_fetch_array($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row['doctor']; ?></td>
                          <td><?php echo $row['ID']; ?></td>
                          <td><?php echo $row['appdate']; ?></td>
                          <td><?php echo $row['apptime']; ?></td>
                          <?php if ($row['paymentStatus'] == 'Paid') { ?>
                            <td><?php echo $row['disease']; ?></td>
                            <td><?php echo $row['allergy']; ?></td>
                            <td><?php echo $row['prescription']; ?></td>
                            <td>
                              <form method="get">
                                <a href="admin-panel.php?ID=<?php echo $row['ID'] ?>">
                                  <input type="hidden" name="ID" value="<?php echo $row['ID'] ?>" />
                                  <input type="submit" name="generate_bill" class="btn btn-success" value="Download Bill" />
                                </a>
                              </form>
                            </td>
                          <?php } else { ?>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td>
                              <a href="payment.php?ID=<?php echo $row['ID'] ?>&fees=<?php echo $row['docFees'] ?>"
                                class="btn btn-warning">Pay Consultation Fee</a>
                            </td>
                          <?php } ?>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                </div>
                
                <div class="tab-pane fade" id="old-pres" role="tabpanel">
                  <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Doctor Name</th>
                        <th scope="col">Appointment ID</th>
                        <th scope="col">Appointment Date</th>
                        <th scope="col">Appointment Time</th>
                        <th scope="col">Diseases</th>
                        <th scope="col">Allergies</th>
                        <th scope="col">Prescriptions</th>
                        <th scope="col">Bill Payment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "select p.doctor,p.ID,p.appdate,p.apptime,p.disease,p.allergy,p.prescription,a.paymentStatus,a.docFees from prestb p inner join appointmenttb a on p.ID=a.ID where p.pid='$pid' AND p.appdate<CURDATE();";
                      $result = mysqli_query($con, $query);
                      while ($row = mysqli_fetch_array($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row['doctor']; ?></td>
                          <td><?php echo $row['ID']; ?></td>
                          <td><?php echo $row['appdate']; ?></td>
                          <td><?php echo $row['apptime']; ?></td>
                          <?php if ($row['paymentStatus'] == 'Paid') { ?>
                            <td><?php echo $row['disease']; ?></td>
                            <td><?php echo $row['allergy']; ?></td>
                            <td><?php echo $row['prescription']; ?></td>
                            <td>
                              <form method="get">
                                <a href="admin-panel.php?ID=<?php echo $row['ID'] ?>">
                                  <input type="hidden" name="ID" value="<?php echo $row['ID'] ?>" />
                                  <input type="submit" name="generate_bill" class="btn btn-success" value="Download Bill" />
                                </a>
                              </form>
                            </td>
                          <?php } else { ?>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td><span class="text-danger">Payment Required</span></td>
                            <td>
                              <a href="payment.php?ID=<?php echo $row['ID'] ?>&fees=<?php echo $row['docFees'] ?>"
                                class="btn btn-warning">Pay Consultation Fee</a>
                            </td>
                          <?php } ?>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                </div> <!-- End tab-pane -->
                </div> <!-- End tab-content -->
              </div><!-- /tab-content -->
            </div><!-- /content-area -->
          </div><!-- /main-content -->

          <!-- jQuery, Popper, Bootstrap -->
          <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
          <script>
            // Restore tab from URL hash
            $(document).ready(function () {
              var hash = window.location.hash;
              if (hash) { $('.list-group a[href="' + hash + '"]').tab('show'); }
              // Update active sidebar item on tab change
              $('#list-tab a').on('shown.bs.tab', function (e) {
                document.querySelector('.top-header .page-title span').textContent = ' – ' + $(e.target).text().trim();
              });
            });
            // Live date in header
            (function () {
              var d = new Date();
              var opts = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
              document.getElementById('headerDate').textContent = d.toLocaleDateString('en-US', opts);
            })();
            // Mobile sidebar toggle
            function toggleSidebar() {
              document.getElementById('sidebar').classList.toggle('show');
              document.getElementById('sidebarOverlay').classList.toggle('show');
            }
            // Profile dropdown toggle
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
            // Close dropdown when clicking outside
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
