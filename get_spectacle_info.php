<?php
include 'db_connection.php'; // Подключаем файл с функциями подключения к базе данных

// Получаем id постановки из POST запроса
$postanovkaId = $_POST['postanovkaId'];

// Устанавливаем соединение с базой данных
$conn = OpenCon();

// Подготавливаем SQL запрос с использованием параметров, чтобы избежать SQL инъекций
$sql = "SELECT s.id, s.data, s.vreamya, p.nazvanie, p.junr, p.author, p.vremya_dlitelnosti
            FROM spectakl s
            INNER JOIN postanovka p ON s.id_postanovka = p.id
            WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postanovkaId);
$stmt->execute();
$result = $stmt->get_result();

// Инициализируем переменную для хранения результата
$response = [];

// Обрабатываем результат запроса
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Формируем массив с информацией о спектакле
        $spectacleInfo = [
            'data' => $row["data"],
            'vreamya' => $row["vreamya"],
            'nazvanie' => $row["nazvanie"],
            'junr' => $row["junr"],
            'author' => $row["author"],
            'vremya_dlitelnosti' => $row["vremya_dlitelnosti"],
            'id'=> $row["id"]
        ];
        // Добавляем информацию о спектакле в результат
        $response[] = $spectacleInfo;
    }
}

// Возвращаем результат в формате JSON
echo json_encode($response);

// Закрываем соединение с базой данных
CloseCon($conn);
?>
