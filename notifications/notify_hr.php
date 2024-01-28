<?php
$to = "miguel.dechavez@nidec.com";
$series = $_GET['series'];
$status = $_GET['status'];
$division = $_GET['division'];
$reason = $_GET['reason'];

// $to = "richardmark.jamilla@nidec.com";
$sender = "HRAR <ncfl-mis@nidec.com>";

$headers = "From: $sender\r\n";
$headers .= "Reply-To: $sender\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
// $headers .= 'Cc: jhess.ortega@nidec.com, sarahlly.torino@nidec.com' . "\r\n";
// $headers .= "Bcc: richardmark.jamilla@nidec.com, jonalyn.oliva@nidec.com, miguel.dechavez@nidec.com\r\n";

$body = '
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>HRAR</title>
</head>

<body
    style="background-color: #f2f2f2; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; border-radius: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="background-color: #ffffff; border-radius: 0;">
                    <tr>
                        <td style="padding: 20px;">
                            <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">' . $division . ' | Attendance Ratio for ' . str_replace("_", "-", $series) . ' has been ' . $status . ($reason == '' || $reason == null ? ' without reason ' : ' with reason: ' . $reason) . ' </h1>
                            <p>Dear Mr./Ms. ' . strtoupper(str_replace("@nidec.com", "", $to)) . $division . ' Attendance Ratio Approval has been updated. You may visit HRAR Application site and check status details by clicking the button below.</p>
                            <p>Thank you.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <a id="approve_btn" style="padding: 8px 12px; border: 1px solid #0d6efd; margin-right: 8px; color: #0d6efd; text-decoration: none;" href="http://10.216.8.90/attendanceratio">Visit HR Attendance Ratio Application</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center>
                                <p style="color:#789">
                                    <small>HR Attendance Ratio Provider. 
                                    <br> This email is auto generated. Do not reply.</small>
                                </p>
                            </center>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
';

// print_r($body);
if ($to !== "") {
    $send = mail($to, "[HRAR] " . $division . " Attendance Ratio Approval has been updated", $body, $headers);
    // $send = 1;
    if ($send) {
        return 1;
    }
}
