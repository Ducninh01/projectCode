<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../../configs/PHPMailer/Exception.php';
require '../../configs/PHPMailer/PHPMailer.php';
require '../../configs/PHPMailer/SMTP.php';

function resetPassword($getName, $getEmail, $token)
{
    $fullUrl = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
    $arrUrl = explode('frontend', $fullUrl);
    $lastUrl = $arrUrl[0] . 'frontend/authen/reset_password.php?token=' . $token . '&email=' . $getEmail;;

    $mail = new PHPMailer(true);
    $toEmail = 'duongvophi008@gmail.com';
    $passwordSecurity = 'gcxonuqdrqxtojye';

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $toEmail;
        $mail->Password = $passwordSecurity;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom($toEmail, $getName);
        $mail->addAddress($getEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Reset password notifications';

        $email_template = "
        <h2>Hello $getName </h2>
        <h3>You receive an email, requesting a password reset for your account.</h3>
        <h3>Click here too reset password</h3>
        <br/>
        <a href='$lastUrl'> $lastUrl </a>

        ";

        $mail->Body = $email_template;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
