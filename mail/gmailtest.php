<?php
//include "PHPMailer.php" ; //you have to upload class files "class.phpmailer.php" and "class.smtp.php"
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require '../vendor/autoload.php';

$mail = new PHPMailer(true);
 
$mail->IsSMTP();
$mail->SMTPDebug = 4;  
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
//test
$mail->SMTPAutoTLS = false;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

//end test
$mail->Host = "smtp.gmail.com";
$mail->Port = 465;
$mail->Username = "knowledge@programming.oultoncollege.com";
$mail->Password = "Oultons2011";
 
$mail->From = "knowledge@programming.oultoncollege.com";
$mail->FromName = "demouser";
 
$mail->AddAddress("tigerwil@gmail.com","Marc Williams");
$mail->Subject = "This is the subject";
$mail->Body = "This is the body";
$mail->WordWrap = 50;
$mail->IsHTML(true);
 
if(!$mail->Send()) {
echo "Mailer Error: " . $mail->ErrorInfo;
} else {
echo "Message has been sent";
}