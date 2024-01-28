<?php
$email = $_GET['email'];
$subject = "Reset Password";
// $to = "richardmark.jamilla@nidec.com";
$to = $email;
$from = "E3Q6S <ncfl-mis@nidec.com>";

$headers = "From: $from\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= "Bcc: richardmark.jamilla@nidec.com\r\n";

$body = '
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>E3Q6S</title>
</head>

<body
    style="background-color: #f2f2f2; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5; margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="background-color: #ffffff; border-radius: 10px;">
                    <tr>
                        <td style="padding: 20px;">
                            <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">E3Q6S | ' . $subject . ' </h1>
                            <p>Dear ' . strtoupper(str_replace('.', ' ', str_replace("@nidec.com", "", $email))) . ',</p>
                            <p>You have requested to reset your password, click on the link below to continue</p>
                            <a href="http://10.216.128.101/E-3Q6S/Home/ResetPassword?q='.md5($email).'" target="_blank" style="background-color: #880202; border-radius: 5px; color: #fff;
                                 display: inline-block; font-size: 16px;
                                margin-top: 20px; padding: 10px 20px; text-decoration: none;">Reset Password</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center>
                                <p style="color:#789">
                                    <small>Enhance 3Q6S Audit System. 
                                    <br> This email is sytem generated.DO NOT REPLY .</small>
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

if ($email !== "") {
    $send = mail($to, $subject, $body, $headers);
    // $send = 1;
    if ($send) {
        // print_r($body);
        return 1;
    }
}
