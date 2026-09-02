<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// Simple routing to handle AJAX requests within the same file for convenience
if (isset($_POST['action']) && $_POST['action'] == 'check_in') {
    header('Content-Type: application/json');
    $aptId = isset($_POST['aptId']) ? intval($_POST['aptId']) : 0;

    if ($aptId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Appointment ID.']);
        exit;
    }

    // Check if appointment exists
    $query = "SELECT * FROM appointmenttb WHERE ID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $aptId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if already checked in (assuming serving_status=1 or a new status column)
        // Here we'll just use a 'checked_in' flag if it exists, or for this demo, return success
        // You might want to add a `checked_in` column to `appointmenttb` (INT default 0)

        // Update BOTH checked_in and arrival_status for consistency across all scanner handlers
        $updateQuery = "UPDATE appointmenttb SET checked_in = 1, arrival_status = 1 WHERE ID = ?";
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bind_param("i", $aptId);

        if ($updateStmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Patient Successfully Checked-In',
                'data' => [
                    'patient' => $row['fname'] . ' ' . $row['lname'],
                    'doctor' => 'Dr. ' . $row['doctor'],
                    'date' => $row['appdate'],
                    'time' => $row['apptime']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update check-in status.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Appointment not found in the database.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception QR Scanner</title>
    <!-- Use similar font and styles to the rest of the application -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f8ff;
            /* Match the hospital theme */
            color: #333;
            padding-top: 50px;
        }

        .scanner-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: auto;
            text-align: center;
        }

        #reader {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .result-box {
            display: none;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="scanner-container">
            <h3 class="mb-4"><i class="fa fa-qrcode text-primary"></i> Patient Check-In Scanner</h3>

            <div id="reader"></div>

            <div id="statusMessage" class="alert" style="display:none;"></div>

            <div class="result-box card border-success" id="resultCard">
                <div class="card-header bg-success text-white">
                    <i class="fa fa-check-circle"></i> Check-In Successful
                </div>
                <div class="card-body text-left">
                    <p><strong>Patient Name:</strong> <span id="resPatient"></span></p>
                    <p><strong>Doctor:</strong> <span id="resDoctor"></span></p>
                    <p><strong>Date:</strong> <span id="resDate"></span></p>
                    <p><strong>Time:</strong> <span id="resTime"></span></p>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-outline-primary" onclick="resetScanner()">Scan Next Patient</button>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML5 QR Code Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let html5QrcodeScanner = null;

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning temporarily once we get a result
            html5QrcodeScanner.clear();

            let aptId = null;
            try {
                // Try to parse as JSON first (Old format)
                if (decodedText.trim().startsWith('{')) {
                    const qrData = JSON.parse(decodedText);
                    aptId = qrData.aptId;
                }
                // Handle URL format (New format from WhatsApp)
                else if (decodedText.includes('aptId=')) {
                    const params = new URLSearchParams(decodedText.split('?')[1]);
                    aptId = params.get('aptId');
                }

                if (aptId) {
                    processCheckIn(aptId);
                } else {
                    showError("Invalid QR Code Format: Missing Appointment ID");
                }
            } catch (e) {
                showError("Invalid QR Code: Scanned data is not in a recognized format.");
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
            // console.warn(`Code scan error = ${error}`);
        }

        function processCheckIn(aptId) {
            $.ajax({
                url: 'scan.php',
                type: 'POST',
                data: {
                    action: 'check_in',
                    aptId: aptId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Hide scanner, show result
                        $('#reader').hide();

                        $('#resPatient').text(response.data.patient);
                        $('#resDoctor').text(response.data.doctor);
                        $('#resDate').text(response.data.date);
                        $('#resTime').text(response.data.time);

                        $('#statusMessage').removeClass('alert-danger').addClass('alert-success').html('<i class="fa fa-check"></i> ' + response.message).show();
                        $('#resultCard').fadeIn();
                    } else {
                        showError(response.message);
                    }
                },
                error: function () {
                    showError("Server Connection Error. Please try again.");
                }
            });
        }

        function showError(msg) {
            $('#statusMessage').removeClass('alert-success').addClass('alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + msg).show();
            // Automatically reset scanner after error so they can try again quickly
            setTimeout(resetScanner, 3000);
        }

        function resetScanner() {
            $('#resultCard').hide();
            $('#statusMessage').hide();
            $('#reader').show();
            startScanner();
        }

        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        // Initialize on load
        $(document).ready(function () {
            startScanner();
        });
    </script>
</body>

</html>