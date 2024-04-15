<?php
session_start();
// Проверка аутентификации пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: lk.php?notification=auth_required");
    exit;
}

// Подключение к базе данных
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'theatre';
// Подключение к базе данных
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем ID билетов из POST запроса
if (isset($_POST['ticket_ids'])) {
    $ticketIds = $_POST['ticket_ids'];

    foreach ($ticketIds as $ticketId) {
        // Проверяем, есть ли такой билет уже в корзине
        $sql_check_ticket = "SELECT id FROM temp_tickets WHERE id = $ticketId";
        $result_check_ticket = $conn->query($sql_check_ticket);

        // Если билет уже есть в корзине, пропускаем его
        if ($result_check_ticket->num_rows > 0) {
            continue;
        }

        // Вставка ID билета во временную таблицу
        $sql_insert_ticket = "INSERT INTO temp_tickets (id) VALUES ($ticketId)";
        $conn->query($sql_insert_ticket) or die("Error inserting ticket ID: " . $conn->error);
    }
}

// Закрываем соединение с базой данных
$conn->close();
?>
