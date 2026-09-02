<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!isset($_GET['aptId']) || empty($_GET['aptId'])) {
    die("<h2 style='text-align:center; margin-top:50px;'>Invalid Check-in Request.</h2>");
}

$id = mysqli_real_escape_string($con, $_GET['aptId']);

// Update check-in status
$update = mysqli_query($con, "UPDATE appointmenttb SET checked_in = 1, arrival_status = 1 WHERE ID = '$id'");

// Fetch details for display
$query = mysqli_query($con, "SELECT * FROM appointmenttb WHERE ID = '$id'");
$app = mysqli_fetch_assoc($query);

if (!$app) {
    die("<h2 style='text-align:center; margin-top:50px;'>Appointment not found.</h2>");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Instant Check-In – Global Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f0fdf4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .checkin-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: #22c55e;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-label {
            color: #64748b;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 700;
            color: #1e293b;
        }
    </style>
</head>

<body>
    <div class="checkin-card">
        <div class="success-icon"><i class="fa fa-check-circle"></i></div>
        <h2 class="mb-2">Checked-In!</h2>
        <p class="text-muted mb-4">The patient has been successfully marked as arrived.</p>

        <div class="text-left">
            <div class="detail-row">
                <span class="detail-label">Patient Name</span>
                <span class="detail-value">
                    <?php echo $app['fname'] . ' ' . $app['lname']; ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Assigned Doctor</span>
                <span class="detail-value">Dr.
                    <?php echo $app['doctor']; ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Medical Issue</span>
                <span class="detail-value">
                    <?php echo $app['symptom_text'] ? $app['symptom_text'] : 'General Checkup'; ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Appointment</span>
                <span class="detail-value">
                    <?php echo $app['appdate'] . ' @ ' . $app['apptime']; ?>
                </span>
            </div>
        </div>

        <button onclick="window.close();" class="btn btn-success btn-lg btn-block mt-4">Close Window</button>
    </div>
</body>

</html>