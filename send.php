<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if (isset($_POST['email'])) {
    $to = "puplap3@gmail.com";
    $from = "puplap3@gmail.com";
    $subject = "Заполнена контактная форма на сайте " . $_SERVER['HTTP_REFERER'];

    $message = "Имя пользователя: ".$_POST['nameFF']."\nEmail пользователя ".$_POST['contactFF']."\nСообщение: ".$_POST['projectFF']."\n\nАдрес сайта: ".$_SERVER['HTTP_REFERER'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        //$mail->SMTPDebug = 2; // 2 for debugging output, 0 for no output
        $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'puplap3@gmail.com'; // Set your SMTP username
        $mail->Password   = 'cjgf bitz rces nify';   // Set your SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587; // You may need to adjust this based on your SMTP server configuration
        $mail->CharSet    = 'UTF-8';
        // Sender information
        $mail->setFrom($from);
        $mail->addReplyTo($from);

        // Recipient
        $mail->addAddress($to);

        // Attachments
        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $mail->addAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        }

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        echo $_POST['name'] . ', Ваше сообщение отправлено, спасибо!';
    } catch (Exception $e) {
        echo 'Извините, письмо не отправлено. Ошибка: ', $mail->ErrorInfo;
    }
}
?>
