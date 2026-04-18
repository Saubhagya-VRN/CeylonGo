<?php
require_once 'app/lib/PHPMailer/Exception.php';
require_once 'app/lib/PHPMailer/PHPMailer.php';
require_once 'app/lib/PHPMailer/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'vinudilanya16@gmail.com';
    $mail->Password   = 'fcmm ooea bqkz wmce';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('vinudilanya16@gmail.com', 'Ceylon Go');
    $mail->addAddress('vinudilanya16@gmail.com');
    $mail->Subject = 'CeylonGo Test Email';
    $mail->isHTML(true);
    $mail->Body    = '<h2 style="color:green;">Email is working! Ready for demo.</h2>';
    $mail->AltBody = 'Email is working!';
    $mail->send();
    echo '<h2 style="color:green;">SUCCESS - Check your Gmail inbox now!</h2>';
} catch (Exception $e) {
    echo '<h2 style="color:red;">FAILED: ' . $mail->ErrorInfo . '</h2>';
}