<?php
$to = "miguel.dechavez@nidec.com";
$series = $_GET['series'];

$overall = file_get_contents('php://input');
$data = array();
parse_str($overall, $data);
// $to = "richardmark.jamilla@nidec.com";
$sender = "HRAR <ncfl-mis@nidec.com>";

$headers = "From: $sender\r\n";
$headers .= "Reply-To: $sender\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
// $headers .= 'Cc: sarahlly.torino@nidec.com, jhess.ortega@nidec.com' . "\r\n";
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
                            <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">Overall Attendance Ratio for series ' . str_replace("_", " ", $series) . ' </h1>
                            <p>Dear Mr./Ms. ' . strtoupper(str_replace("@nidec.com", "", $to)) . ',</p>
                            <p>Please see below for the overall attendance ratio for series ' . str_replace("_", " ", $series) . '</p>
                        </td>
                    </tr>
                    <tr>
                        <table style="margin-left: 20px; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Division</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Entity</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Absence %</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">SL</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">VL</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">UA</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Late</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">UT</th>
                                    <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Attendance %</th>
                                </tr>
                            </thead>
                            <tbody>';

foreach ($data['overallNcfl'] as $ncfl) {
    $body .= '
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">' . $ncfl['division'] . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . $ncfl['entity'] . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['absent_ratio'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['sl_percentage'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['vl_percentage'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['lwop_percentage'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['late_percentage'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['early_exit_percentage'], 2) . '</td>
            <td style="padding: 10px; border: 1px solid #ddd;">' . round($ncfl['ratio'], 2) . '</td>
        </tr>
    ';
}

$body .= '
        </tbody>
    </table>
<table style="margin-left: 20px; border-collapse: collapse; font-size: 12px; margin-top: 15px;">
    <thead>
        <tr>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Division</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Entity</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Absence %</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">SL</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">VL</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">UA</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Late</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">UT</th>
            <th style="word-wrap: break-word; border: 1px solid #dee2e6; padding: 6px 8px">Attendance %</th>
        </tr>
    </thead>
<tbody>';

foreach ($data['overallNpfl'] as $npfl) {
    $body .= '<tr>
        <td style="padding: 10px; border: 1px solid #ddd;">' . $npfl['division'] . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . $npfl['entity'] . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['absent_ratio'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['sl_percentage'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['vl_percentage'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['lwop_percentage'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['late_percentage'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['early_exit_percentage'], 2) . '</td>
        <td style="padding: 10px; border: 1px solid #ddd;">' . round($npfl['ratio'], 2) . '</td>
    </tr>
    ';
}

$body .= '</tbody>
    </table>
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

if ($to !== "") {
    $send = mail($to, 'Overall Attendance Ratio for series ' . str_replace("_", " ", $series), $body, $headers);
    // $send = 1;
    if ($send) {
        return 1;
    }
}
