<?php
$to = $_GET['to'];
$subject = $_GET['subject'];
$notifMsg = $_GET['notifMsg'];
$division = $_GET['division'];
$series = $_GET['series_id'];

$ncfl_ratio =           $_GET['ncfl_ratio'];
$ncfl_absent_ratio =    $_GET['ncfl_absent_ratio'];
$ncfl_sl_percentage =   $_GET['ncfl_sl_percentage'];
$ncfl_vl_percentage =   $_GET['ncfl_vl_percentage'];
$ncfl_lwop_percentage = $_GET['ncfl_lwop_percentage'];
$ncfl_late_percentage = $_GET['ncfl_late_percentage'];
$ncfl_early_exit_percentage = $_GET['ncfl_early_exit_percentage'];

$npfl_ratio =           $_GET['npfl_ratio'];
$npfl_absent_ratio =    $_GET['npfl_absent_ratio'];
$npfl_sl_percentage =   $_GET['npfl_sl_percentage'];
$npfl_vl_percentage =   $_GET['npfl_vl_percentage'];
$npfl_lwop_percentage = $_GET['npfl_lwop_percentage'];
$npfl_late_percentage = $_GET['npfl_late_percentage'];
$npfl_early_exit_percentage = $_GET['npfl_early_exit_percentage'];



// $to = "richardmark.jamilla@nidec.com";
$sender = "HRAR <ncfl-mis@nidec.com>";

$headers = "From: $sender\r\n";
$headers .= "Reply-To: $sender\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
// $headers .= 'Cc: gener.fajardo@nidec.com, john.bognot@nidec.com' . "\r\n";
// $headers .= "Bcc: richardmark.jamilla@nidec.com, jonalyn.oliva@nidec.com, miguel.dechavez@nidec.com\r\n";

$body = '
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>HRAR</title>
</head>

<body
    style="background-color: #f2f2f2; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; border-radius: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="background-color: #ffffff; border-radius: 0;">
                    <tr>
                        <td style="padding: 20px;">
                            <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">' . $division . ' Attendance Ratio | ' . $subject . ' </h1>
                            <p>Dear Mr./Ms. ' . strtoupper(str_replace("@nidec.com", "", $to)) . ',</p>
                            <p>' . $notifMsg . '</p>
                            <p style="color: red; font-weight: bold;">NOTE: <span style="color: black; font-weight: normal;">Please settle your approval within 24 hours. We will consider this request as approved if we did not receive your approval within the next 24 hours</span></p>
                            <p>Thank you.</p>
                        </td>
                    </tr>
                    <tr>
                        <table style="margin-left: 20px; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">Division</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">Entity</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">Absence %</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">SL</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">VL</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">UA</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">Late</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">UT</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">Attendance %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $division . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">NCFL</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_absent_ratio . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_sl_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_vl_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_lwop_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_late_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_early_exit_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $ncfl_ratio . '</td>
                                </tr>
                                <tr>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $division . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">NPFL</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_absent_ratio . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_sl_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_vl_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_lwop_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_late_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_early_exit_percentage . '</td>
                                    <td style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 8px 10px">' .  $npfl_ratio . '</td>
                                </tr>
                            </tbody>
                        </table>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <a id="approve_btn" style="padding: 8px 12px; border: 1px solid #0d6efd; margin-right: 8px; color: #0d6efd; text-decoration: none;" href="http://10.216.8.90/attendanceratio/notifapproval?status=approved&division=' . str_replace(" ", "%20", $division) . '&series=' . $series . '">Approve</a>
                            <a id="reject_btn" style="padding: 8px 12px; border: 1px solid #bb2d3b; color: #bb2d3b; text-decoration: none;" href="http://10.216.8.90/attendanceratio/notifapproval?status=rejected&division=' . str_replace(" ", "%20", $division) . '&series=' . $series . '">Reject</a>
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
    $send = mail($to, "[HRAR] Attendance Ratio " . $subject, $body, $headers);
    // $send = 1;
    if ($send) {
        return 1;
    }
}
