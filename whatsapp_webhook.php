<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// Twilio sends data as POST
$from = $_POST['From'] ?? '';
$body = strtolower(trim($_POST['Body'] ?? ''));

// Format phone to match database (assumes 10 digits in DB)
$phone = str_replace(array('whatsapp:', '+'), '', $from);
$phone_10 = substr($phone, -10);

$query = mysqli_query($con, "SELECT * FROM patreg WHERE contact='$phone_10' LIMIT 1");

if (mysqli_num_rows($query) > 0) {
    $patient = mysqli_fetch_assoc($query);
    $pid = $patient['pid'];

    if ($body == 'hi' || $body == 'hello' || $body == 'book') {
        $last_app_q = mysqli_query($con, "SELECT doctor FROM appointmenttb WHERE pid='$pid' ORDER BY ID DESC LIMIT 1");
        if (mysqli_num_rows($last_app_q) > 0) {
            $last_doc = mysqli_fetch_assoc($last_app_q)['doctor'];
            $reply = "Welcome back, " . $patient['fname'] . "! 🏥\n\nWould you like to book a follow-up with Dr. $last_doc based on your last visit?\n\nReply *1* for Yes\nReply *2* for a New Problem";
        } else {
            $reply = "Welcome to Global Hospital, " . $patient['fname'] . "! 🏥\n\nTo book an appointment, please reply with your symptoms (e.g. 'I have a fever and headache').";
        }
    } else if ($body == '1') {
        // Here we would implement the actual auto-booking logic with the previous doctor.
        // For demonstration of the workflow, we confirm receipt.
        $reply = "Great! We are verifying Dr. availability for today. We will send you your Live Queue Link and QR Pass shortly! ✅";
    } else if ($body == '2') {
        $reply = "Understood. Please describe your new symptoms, and our Smart AI will suggest the right specialist for you.";
    } else {
        // Assume they are typing symptoms
        $reply = "Thank you. Our AI is analyzing your symptoms ('$body'). We will recommend a specialist and send you available time slots instantly.";
    }
} else {
    $reply = "Welcome to Global Hospital! 🏥\n\nIt looks like you are a new patient. Please complete a quick registration and book your first appointment via our portal:\nhttp://localhost/MABS/Hospital-Management-System-master/index.php\n\nOnce registered, you can manage everything right here on WhatsApp!";
}

// Generate Twilio TwiML Response
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<Response>\n";
echo "    <Message>$reply</Message>\n";
echo "</Response>";
?>