<?php

require_once __DIR__ . '/vendor/autoload.php';
function phpVersionCheck(){
    $version = phpversion();
    $MasterV = explode(".", $version)[0];
    if( $MasterV < 7)
        return false;
    return true;
}
function custom_mail_send($mail_address, $subject, $body, $alt_body) {
    if( !phpVersionCheck())
        return "PHP Version Error";
    $transport = (new Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))
        ->setUsername('chris.green@betia.co')
        ->setPassword('tnP4e%U2djPK');
    $mailer = new Swift_Mailer($transport);
    $message = (new Swift_Message($subject))
        ->setFrom(['chris.green@betia.co' => 'JTS Support'])
        ->setTo([$mail_address => 'ToEmail'])
        ->setBody($body)
        ->addPart($alt_body);

    if(!$mailer->send($message)) {
        return $mail->ErrorInfo;
    } else {
        return "OK";
    }
}
function custom_mail_send_file($mail_address, $subject, $body, $alt_body, $fileUrl) {
    if( !phpVersionCheck())
        return "PHP Version Error";
    $transport = (new Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))
        ->setUsername('chris.green@betia.co')
        ->setPassword('tnP4e%U2djPK');
    $mailer = new Swift_Mailer($transport);
    $message = (new Swift_Message($subject))
        ->setFrom(['chris.green@betia.co' => 'JTS Support'])
        ->setTo([$mail_address => 'ToEmail'])
        ->setBody($body)
        ->addPart($alt_body)
        ->attach( $file_Url);

    if(!$mailer->send($message)) {
        return $mail->ErrorInfo;
    } else {
        return "OK";
    }
}