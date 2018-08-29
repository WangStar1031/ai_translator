<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

function custom_mail_send($mail_address, $subject, $body, $alt_body) {

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->Port       = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth   = true;
    $mail->Username = 'chris.green@betia.co';
    $mail->Password = 'tnP4e%U2djPK';
    $mail->SetFrom('chris.green@betia.co', 'JTS Support');
    $mail->addAddress($mail_address, 'ToEmail');
    $mail->IsHTML(true);

    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = $alt_body;

    if(!$mail->send()) {
        return $mail->ErrorInfo;
    } else {
        return "OK";
    }
}
function custom_mail_send_file($mail_address, $subject, $body, $alt_body, $fileUrl) {

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->Port       = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth   = true;
    $mail->Username = 'chris.green@betia.co';
    $mail->Password = 'tnP4e%U2djPK';
    $mail->SetFrom('chris.green@betia.co', 'JTS Support');
    $mail->addAddress($mail_address, 'ToEmail');
    $mail->IsHTML(true);

    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = $alt_body;
    $mail->addAttachment($fileUrl);

    if(!$mail->send()) {
        return $mail->ErrorInfo;
    } else {
        return "OK";
    }
}