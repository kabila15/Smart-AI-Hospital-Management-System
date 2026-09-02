<?php
include("header.php");
$role = isset($_GET['role']) ? $_GET['role'] : 'patient';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login – Global Hospital</title>
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="dashboard.css">
</head>

<body class="login-body">

  <div class="login-card">
    <div class="login-header">
      <i class="fa fa-hospital-o brand-icon"></i>
      <h4 class="mb-1 font-weight-bold">Global Hospital</h4>
      <p class="small text-white-50 mb-0">Healthcare Management Portal</p>
    </div>

    <nav class="login-nav-tabs nav nav-pills" id="loginTab" role="tablist">
      <a class="nav-link <?= ($role == 'patient') ? 'active' : '' ?>" id="patient-tab" data-toggle="pill"
        href="#patient-login" role="tab">Patient</a>
      <a class="nav-link <?= ($role == 'doctor') ? 'active' : '' ?>" id="doctor-tab" data-toggle="pill"
        href="#doctor-login" role="tab">Doctor</a>
      <a class="nav-link <?= ($role == 'admin') ? 'active' : '' ?>" id="admin-tab" data-toggle="pill"
        href="#admin-login" role="tab">Receptionist</a>
    </nav>

    <div class="tab-content login-tabs-content" id="loginTabContent">

      <!-- Patient Login -->
      <div class="tab-pane fade <?= ($role == 'patient') ? 'show active' : '' ?>" id="patient-login" role="tabpanel">
        <form class="login-form" method="POST" action="func.php">
          <div class="form-group mb-4">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
          </div>
          <div class="form-group mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password2" class="form-control password-input" id="patient-pass"
                placeholder="••••••••" required>
              <div class="input-group-append">
                <span class="input-group-text-toggle" onclick="togglePassword('patient-pass', this)">
                  <i class="fa fa-eye"></i>
                </span>
              </div>
            </div>
          </div>
          <button type="submit" name="patsub" class="login-btn">
            <i class="fa fa-sign-in mr-2"></i> Log In as Patient
          </button>
          <div class="text-center mt-3">
            <span class="small text-muted">New here? <a href="index.php" class="text-primary font-weight-600">Create an
                account</a></span>
          </div>
        </form>
      </div>

      <!-- Doctor Login -->
      <div class="tab-pane fade <?= ($role == 'doctor') ? 'show active' : '' ?>" id="doctor-login" role="tabpanel">
        <form class="login-form" method="POST" action="func1.php">
          <div class="form-group mb-4">
            <label class="form-label">Username</label>
            <input type="text" name="username3" class="form-control" placeholder="Enter username" required>
          </div>
          <div class="form-group mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password3" class="form-control password-input" id="doctor-pass"
                placeholder="••••••••" required>
              <div class="input-group-append">
                <span class="input-group-text-toggle" onclick="togglePassword('doctor-pass', this)">
                  <i class="fa fa-eye"></i>
                </span>
              </div>
            </div>
          </div>
          <button type="submit" name="docsub1" class="login-btn">
            <i class="fa fa-sign-in mr-2"></i> Log In as Doctor
          </button>
        </form>
      </div>

      <!-- Admin Login -->
      <div class="tab-pane fade <?= ($role == 'admin') ? 'show active' : '' ?>" id="admin-login" role="tabpanel">
        <form class="login-form" method="POST" action="func3.php">
          <div class="form-group mb-4">
            <label class="form-label">Admin Username</label>
            <input type="text" name="username1" class="form-control" placeholder="Enter admin username" required>
          </div>
          <div class="form-group mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password2" class="form-control password-input" id="admin-pass"
                placeholder="••••••••" required>
              <div class="input-group-append">
                <span class="input-group-text-toggle" onclick="togglePassword('admin-pass', this)">
                  <i class="fa fa-eye"></i>
                </span>
              </div>
            </div>
          </div>
          <button type="submit" name="adsub" class="login-btn">
            <i class="fa fa-sign-in mr-2"></i> Log In as Admin
          </button>
        </form>
      </div>

    </div>

    <div class="login-footer">
      <a href="index.php" class="text-muted small"><i class="fa fa-long-arrow-left mr-1"></i> Back to Home Page</a>
    </div>
  </div>

  <script>
    function togglePassword(inputId, el) {
      const input = document.getElementById(inputId);
      const icon = el.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
      }
    }
  </script>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>