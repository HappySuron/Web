<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $to = "zaburdinslv@mail.ru"; // Замените на вашу электронную почту
    $subject = "Сообщение с формы контактов";
    $body = "Имя: $name\nEmail: $email\n\n$message";

    if (mail($to, $subject, $body)) {
        echo "<p>Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.</p>";
    } else {
        echo "<p>Ошибка при отправке сообщения. Пожалуйста, попробуйте позже.</p>";
    }
}
header("Location: index.html");
?>

