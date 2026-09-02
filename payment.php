<?php
session_start();
$con=mysqli_connect("localhost","root","","myhmsdb");

if(!isset($_SESSION['pid']) || empty($_SESSION['pid'])){
    header("Location: index1.php");
    exit();
}

if(!isset($_GET['ID']) || !isset($_GET['fees'])){
    header("Location: admin-panel.php");
    exit();
}

$id = $_GET['ID'];
$fees = $_GET['fees'];

if(isset($_POST['pay_submit'])){
    $query = mysqli_query($con, "update appointmenttb set paymentStatus='Paid' where ID='$id'");
    if($query){
        echo "<script>alert('Payment Successful! Your prescription is now unlocked.'); window.location.href = 'admin-panel.php';</script>";
    } else {
        echo "<script>alert('Payment Failed. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Admin Payment Gateway</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'IBM Plex Sans', sans-serif; }
        .payment-card { max-width: 500px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .hospital-brand { text-align: center; margin-bottom: 30px; }
        .hospital-brand i { color: #342ac1; font-size: 40px; }
        .btn-pay { background: #342ac1; color: #fff; border: none; width: 100%; padding: 12px; font-size: 18px; border-radius: 5px; cursor: pointer; }
        .btn-pay:hover { background: #2a2299; }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="hospital-brand">
                <h3>Global Hospital</h3>
                <p class="text-muted">Centralized Admin Payment Gateway</p>
            </div>
            <hr>
            <div class="mb-4">
                <p><strong>Appointment ID:</strong> #<?php echo $id; ?></p>
                <p><strong>Payee:</strong> Hospital Administration</p>
                <h4 class="text-primary mt-3">Amount: ₹<?php echo $fees; ?></h4>
            </div>
            <form method="post">
                <div class="form-group">
                    <label>Cardholder Name</label>
                    <input type="text" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-control" placeholder="1234 5678 9101 1121" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" class="form-control" placeholder="MM/YY" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="password" class="form-control" placeholder="***" required>
                        </div>
                    </div>
                </div>
                <button type="submit" name="pay_submit" class="btn-pay">Securely Pay to Admin</button>
            </form>
            <div class="text-center mt-3">
                <a href="admin-panel.php" class="text-muted small">Cancel and Return</a>
            </div>
        </div>
    </div>
</body>
</html>