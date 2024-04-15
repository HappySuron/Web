<?php
session_start();
// Подключение к базе данных
$db_host = 'localhost';  
$db_user = 'root';       
$db_pass = '';       
$db_name = 'theatre';      

// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка на заполненность полей
    if (empty($_POST['fio']) || empty($_POST['contact']) || empty($_POST['login']) || empty($_POST['password'])) {
        $error = "Пожалуйста, заполните все поля.";
    } else {
        // Подключение к базе данных
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        // Проверка соединения
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Защита от SQL-инъекций
        $fio = $conn->real_escape_string($_POST['fio']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $login = $conn->real_escape_string($_POST['login']);
        $password = $conn->real_escape_string($_POST['password']);

        // Выполнение SQL запроса для проверки уникальности логина
        $check_login_sql = "SELECT * FROM zriteli WHERE login='$login'";
        $check_login_result = $conn->query($check_login_sql);

        // Проверяем, есть ли пользователь с таким логином
        if ($check_login_result->num_rows > 0) {
            // Логин уже занят
            $error = "Логин уже занят, выберите другой.";
        } else {
            // Логин свободен, выполняем регистрацию
            $register_sql = "INSERT INTO zriteli (fio, contact, login, password) VALUES ('$fio', '$contact', '$login', '$password')";
            if ($conn->query($register_sql) === TRUE) {
                // Успешная регистрация
                $_SESSION['login'] = $login; // Сохраняем имя пользователя в сессии
                $_SESSION['fio'] = $fio; // Сохраняем ФИО пользователя в сессии
                $_SESSION['user_id'] = $conn->insert_id; // Сохраняем ID пользователя в сессии
                $login_success = true; // Переменная для отображения сообщения об успешной регистрации
            } else {
                // Ошибка при регистрации
                $error = "Ошибка при регистрации: " . $conn->error;
            }
        }

        // Закрываем соединение с базой данных
        $conn->close();
    }
}

// Проверяем, есть ли пользователь в сессии
if (isset($_SESSION['login'])) {
    // Если пользователь уже авторизован, показываем сообщение об успешной авторизации
    $login_success = true;
}

// Проверяем, была ли нажата кнопка "Выход"
if (isset($_POST['logout'])) {
    // Очищаем все переменные сессии
    $_SESSION = array();

    // Уничтожаем сессию
    session_destroy();

    // Перенаправляем пользователя на главную страницу или другую страницу
    //header("Location: index.php");
    //exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Регистрация - GoGoL</title>
    <link rel="shortcut icon" href="images/icon.jpg" type="image/jpg">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,400;0,700;1,300&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/jquery.fancybox.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom.css"> <!-- Ваш файл с настройками CSS -->
    <style>
        .registration-form input[type="text"],
        .registration-form input[type="password"],
        .registration-form input[type="contact"],
        .registration-form button {
            z-index: 999;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <header class="header">
        <nav class="menu">
            <button class="menu__btn">
                <span></span>
            </button>
            <ul class="menu__list">
                <li class="menu__list-item">
                    <a class="menu__list-link" href="/index.html">ГЛАВНАЯ</a>
                </li>
                <li class="menu__list-item">
                    <a class="menu__list-link" href="#">АФИША</a>
                </li>
                <li class="menu__list-item">
                    <a class="menu__list-link" href="/tickets.php">БИЛЕТЫ</a>
                </li>
                <li class="menu__list-item">
                    <a class="menu__list-link menu__list-link-afisha" href="#">ЛИЧНЫЙ КАБИНЕТ</a>
                </li>
            </ul>
        </nav>

        <div class="logo">
            <img src="images/logo.png" alt="Логотип" class="logo__img">
        </div>

    </header>

    <section class="registration-section section-page">
        <div class="container">
            <h3 class="title">Регистрация</h3>
            <?php if (isset($login_success) && $login_success): ?>
                <!-- Если регистрация прошла успешно, показываем приветствие и ФИО -->
                <p>Добро пожаловать, <?php echo $_SESSION['fio']; ?>!</p>
                <!-- Добавляем кнопку для выхода -->
                <form method="post">
                    <button type="submit" name="logout" class="logout-button">Выход</button>
                </form>
            <?php else: ?>
                <!-- Если регистрация не была завершена, показываем форму для регистрации -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                    class="registration-form">
                    <label for="fio">ФИО:</label>
                    <input type="text" id="fio" name="fio"><br><br>
                    <label for="contact">Email:</label>
                    <input type="email" id="contact" name="contact"><br><br>
                    <label for="login">Логин:</label>
                    <input type="text" id="login" name="login"><br><br>
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password"><br><br>
                    <button type="submit" class="b_slv">Зарегистрироваться</button>
                </form>
                <?php if (isset($error)): ?>
                    <!-- Выводим сообщение об ошибке, если есть -->
                    <p><?php echo $error; ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Include necessary scripts at the bottom -->
</body>

</html>
