<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Global Hospitals – Welcome</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #fff;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ═══════════════════════════════════════
           NAVBAR
        ═══════════════════════════════════════ */
        .navbar {
            padding: 20px 0;
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #1e293b !important;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
        }

        .navbar-brand i {
            color: #2563eb;
            background: rgba(37, 99, 235, 0.1);
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748b !important;
            margin-left: 30px;
            padding: 6px 0 !important;
            transition: color 0.2s ease;
            background: transparent !important;
        }

        .nav-link:hover {
            color: #2563eb !important;
            text-decoration: none;
        }

        .navbar-toggler {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px;
        }

        /* ── Hero Section ── */
        .hero {
            padding: 80px 0 100px;
            background: radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 0% 100%, rgba(37, 99, 235, 0.03) 0%, transparent 50%);
            position: relative;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: rgba(37, 99, 235, 0.03);
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .hero-title span {
            color: #2563eb;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 550px;
        }

        /* ── Role Cards ── */
        .role-section {
            padding-bottom: 100px;
        }

        .role-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 40px;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .role-card:hover {
            transform: translateY(-10px);
            border-color: #2563eb;
            box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.1);
        }

        .role-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 28px;
            transition: all 0.3s ease;
        }

        .patient-icon {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .doctor-icon {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
        }

        .admin-icon {
            background: rgba(168, 85, 247, 0.1);
            color: #9333ea;
        }

        .role-card:hover .role-icon {
            transform: scale(1.1);
        }

        .role-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .role-info {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .role-link {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
        }

        .role-link i {
            transition: transform 0.2s ease;
        }

        .role-card:hover .role-link i {
            transform: translateX(5px);
        }

        .btn-register {
            background: #2563eb;
            color: #fff;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
        }

        .btn-register:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4);
            color: #fff;
        }

        /* ── Modal Registration ── */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            padding: 25px;
        }

        .modal-title {
            font-weight: 800;
            color: #1e293b;
        }

        .modal-body {
            padding: 30px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        @media (max-width: 991px) {
            .hero {
                padding: 50px 0 60px;
                text-align: center;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-desc {
                margin: 0 auto 30px;
            }
            .d-flex.gap-3 {
                justify-content: center;
            }
        }
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
            }
            .navbar-brand i {
                width: 32px;
                height: 32px;
                font-size: 1.1rem;
            }
            .role-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <!-- ═══ NAVBAR ═══ -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa fa-hospital-o"></i>
                GLOBAL HOSPITALS
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="fa fa-bars" style="color:#1e293b;"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php" style="color:#2563eb !important;">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.html">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-badge">Healthcare Redefined</div>
                    <h1 class="hero-title">Better health for a <span>Better world</span></h1>
                    <p class="hero-desc">Experience world-class healthcare with our advanced management system. Fast,
                        secure, and patient-centric services at your fingertips.</p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-register" data-toggle="modal" data-target="#registerModal">Register
                            Now</button>
                        <a href="index1.php" class="btn btn-link nav-link font-weight-bold p-3">Sign In</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div style="position: relative;">
                        <img src="images/doctor_patient_sidebar.jpg" class="img-fluid rounded-circle shadow-lg"
                            style="border: 15px solid #fff;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles -->
    <section class="role-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="role-card" onclick="window.location.href='index1.php?role=patient'">
                        <div class="role-icon patient-icon"><i class="fa fa-user-plus"></i></div>
                        <h3 class="role-name">Patient</h3>
                        <p class="role-info">Book appointments, track live queue status, and view your medical
                            prescriptions online.</p>
                        <span class="role-link">Patient Portal <i class="fa fa-arrow-right"></i></span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="role-card" onclick="window.location.href='index1.php?role=doctor'">
                        <div class="role-icon doctor-icon"><i class="fa fa-stethoscope"></i></div>
                        <h3 class="role-name">Doctor</h3>
                        <p class="role-info">Manage your daily schedule, treat patients, and issue digital prescriptions
                            securely.</p>
                        <span class="role-link">Doctor Portal <i class="fa fa-arrow-right"></i></span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="role-card" onclick="window.location.href='index1.php?role=admin'">
                        <div class="role-icon admin-icon"><i class="fa fa-shield"></i></div>
                        <h3 class="role-name">Receptionist</h3>
                        <p class="role-info">Coordinate patient flow, manage doctor leaves, and oversee overall hospital
                            operations.</p>
                        <span class="role-link">Admin Portal <i class="fa fa-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Modal (Consolidated) -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Patient Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" action="func2.php" onsubmit="return checklen()">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="fname" placeholder="John" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="lname" placeholder="Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" name="email" placeholder="john@example.com"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number *</label>
                                <input type="tel" name="contact" class="form-control" placeholder="10-digit number"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="reg-pass"
                                        class="form-control password-input" placeholder="Min 6 characters" required
                                        onkeyup="check()">
                                    <div class="input-group-append">
                                        <span class="input-group-text-toggle"
                                            onclick="togglePassword('reg-pass', this)"><i class="fa fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <div class="input-group">
                                    <input type="password" name="cpassword" id="reg-cpass"
                                        class="form-control password-input" placeholder="Re-enter password" required
                                        onkeyup="check()">
                                    <div class="input-group-append">
                                        <span class="input-group-text-toggle"
                                            onclick="togglePassword('reg-cpass', this)"><i class="fa fa-eye"></i></span>
                                    </div>
                                </div>
                                <small id="message" class="mt-1 d-block"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g1" name="gender" value="Male" class="custom-control-input"
                                        checked>
                                    <label class="custom-control-label" for="g1">Male</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g2" name="gender" value="Female"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="g2">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="patsub1" class="btn-register">Register Account</button>
                        </div>
                    </form>
                </div>
            </div>
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
        function check() {
            if (document.getElementById('reg-pass').value == document.getElementById('reg-cpass').value) {
                document.getElementById('message').style.color = '#16a34a';
                document.getElementById('message').innerHTML = '✓ Matched';
            } else {
                document.getElementById('message').style.color = '#dc2626';
                document.getElementById('message').innerHTML = '✗ Not Matching';
            }
        }
        function checklen() {
            if (document.getElementById("reg-pass").value.length < 6) {
                alert("Password must be at least 6 characters long.");
                return false;
            }
            return true;
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('register') === '1') {
                $('#registerModal').modal('show');
            }
        });
    </script>
</body>

</html>