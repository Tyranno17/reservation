<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';


//Load Composer's autoloader
require 'vendor\autoload.php';



//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                            //Enable verbose debug output
    $mail->isSMTP();                                                  //Send using SMTP
    $mail->Host       = 'smtp.office365.com';                         //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                         //Enable SMTP authentication
    $mail->Username   = 'reservation.vehicule@mondomaine.com'; //SMTP username
    $mail->Password   = 'xxxxxxxx';                            //SMTP password
    $mail->SMTPSecure = 'tls';                                        //Enable implicit TLS encryption
    $mail->Port       = 587;                                          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('reservation.vehicule@mondomaine.com', 'Mailer');
    $mail->addAddress($userEmail);     //Add a recipient


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);;

    $mail->send();
    echo 'Le message a été envoyé';
} catch (Exception $e) {
    echo "Le message n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}";
}
