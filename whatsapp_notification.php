<?php

function sendWhatsAppNotification($toNumber, $message, $appointmentDetails = null)
{
    $url = "http://localhost:3000/send-appointment-notification";

    $data = array(
        'contact' => $toNumber,
        'message' => $message
    );

    if (is_array($appointmentDetails)) {
        $data = array_merge($data, $appointmentDetails);
    }

    $json_data = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    // Make sure timeout is reasonable, node service might take a sec to generate QR
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("WhatsApp Local Node Error: " . $error);
        return false;
    }

    $response_data = json_decode($response, true);
    if (isset($response_data['status']) && $response_data['status'] == 'success') {
        return true;
    } else {
        error_log("WhatsApp Local Node Failed: " . $response);
        return false;
    }
}

function triggerEmergencyReschedule($toNumber, $appointmentId, $doctor, $leave_date, $specialization, $options)
{
    $url = "http://localhost:3000/trigger-emergency-reschedule";
    $data = array(
        'contact' => $toNumber,
        'appointmentId' => $appointmentId,
        'doctor' => $doctor,
        'leave_date' => $leave_date,
        'specialization' => $specialization,
        'options' => $options
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("WhatsApp Emergency Reschedule Node Error: " . $error);
        return false;
    }
    return true;
}
?>