<?php
session_start();

// Выводим переданные параметры на страницу для проверки
// echo "<pre>";
// print_r($_GET);
// echo "</pre>";

// Проверяем, был ли передан параметр notification и его значение равно 'auth_required'
if (isset($_GET['notification']) && $_GET['notification'] === 'auth_required') {
    // Выводим уведомление
    echo "<script>alert('Для просмотра билетов, вам необходимо авторизоваться');</script>";
}

$db_host = 'localhost';  
$db_user = 'root';       
$db_pass = '';       
$db_name = 'theatre';  

// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Проверка соединения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Защита от SQL-инъекций
    $login = $conn->real_escape_string($_POST['login']);
    $password = $conn->real_escape_string($_POST['password']);

    // Выполнение SQL запроса
    $sql = "SELECT * FROM zriteli WHERE login='$login' AND password='$password'";
    $result = $conn->query($sql);

    // Проверяем, есть ли пользователь с таким логином и паролем
    if ($result->num_rows > 0) {
        // Успешная авторизация
        $user_data = $result->fetch_assoc();
        $_SESSION['login'] = $login; // Сохраняем имя пользователя в сессии
        $_SESSION['fio'] = $user_data['fio']; // Сохраняем ФИО пользователя в сессии
        $_SESSION['user_id'] = $user_data['id']; // Сохраняем ID пользователя в сессии
        $login_success = true; // Переменная для отображения сообщения об успешной авторизации
    } else {
        // Неправильный логин или пароль
        $error = "Неправильный логин или пароль.";
    }

    // Закрываем соединение с базой данных
    $conn->close();
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

// Проверяем, был ли отправлен запрос на удаление билета
if (isset($_POST['delete_ticket'])) {
    // Подключение к базе данных
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Проверка соединения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Получаем ID билета для удаления
    $ticket_id = $_POST['ticket_id'];

    // SQL-запрос на удаление билета из таблицы temp_tickets
    $sql_delete = "DELETE FROM temp_tickets WHERE id = $ticket_id";

    // Выполнение запроса на удаление
    if ($conn->query($sql_delete) === TRUE) {
        // Успешное удаление
        echo "<script>alert('Билет успешно удален');</script>";
    } else {
        // Ошибка при удалении
        echo "<script>alert('Ошибка при удалении билета: " . $conn->error . "');</script>";
    }

    // Закрываем соединение с базой данных
    $conn->close();
}
if (isset($_POST['order_tickets'])) {
    // Подключение к базе данных
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    // Проверка соединения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Получаем ID текущего пользователя
    $user_id = $_SESSION['user_id'];
    
    // Получаем ID выбранных билетов из таблицы temp_tickets
    $sql_select_temp_tickets = "SELECT id FROM temp_tickets";
    $result_select_temp_tickets = $conn->query($sql_select_temp_tickets);
    
    // Проверяем, есть ли результаты
    if ($result_select_temp_tickets->num_rows > 0) {
        // Для каждого билета вставляем запись в таблицу zriteli_bileti
        while ($row = $result_select_temp_tickets->fetch_assoc()) {
            $ticket_id = $row['id'];
            // SQL-запрос на добавление записи в таблицу zriteli_bileti
            $sql_insert_zriteli_bileti = "INSERT INTO zriteli_bileti (id_zriteli, id_bileti) VALUES ($user_id, $ticket_id)";
            // Выполнение запроса на добавление
            $conn->query($sql_insert_zriteli_bileti);
        }
        
        // Очищаем таблицу temp_tickets
        $sql_delete_temp_tickets = "DELETE FROM temp_tickets";
        $conn->query($sql_delete_temp_tickets);
        
        // Сообщаем пользователю об успешном оформлении заказа
        echo "<script>alert('Заказ успешно оформлен');</script>";
    } else {
        // Сообщаем пользователю, что корзина пуста
        echo "<script>alert('Корзина пуста');</script>";
    }
    
    // Закрываем соединение с базой данных
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ЛК - GoGoL</title>
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
        .login-form input[type="text"],
        .login-form input[type="password"],
        .login-form button {
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
                    <a class="menu__list-link" href="/afisha.php">АФИША</a>
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

    <section class="login-section section-page">
        <div class="container">
            <h3 class="title">Личный кабинет</h3>
            <?php if (isset($login_success) && $login_success): ?>
                <!-- Если пользователь уже авторизован, показываем приветствие и ФИО -->
                <p>Добро пожаловать, <?php echo $_SESSION['fio']; ?>!</p>
                <!-- Добавляем кнопку для выхода -->
                <form method="post">
                    <button type="submit" name="logout" class="logout-button">Выход</button>
                </form>
                
                <!-- Таблица для отображения выбранных билетов -->
                <!-- <h3>Выбранные билеты:</h3>
                <table border="1">
                    <tr>
                        <th>ID билета</th>
                    </tr>
                </table> -->
                
            <?php else: ?>
                <!-- Если пользователь не авторизован, показываем форму входа -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
                    class="login-form <?php if (isset($_SESSION['login'])) echo 'hidden'; ?>">
                    <label for="login">Логин:</label>
                    <input type="text" id="login" name="login"><br><br>
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password"><br><br>
                    <button type="submit">Войти</button>
                </form>
                <?php if (isset($error)): ?>
                    <!-- Выводим сообщение об ошибке -->
                    <p><?php echo $error; ?></p>
                <?php endif; ?>
                <p class="<?php if (isset($_SESSION['login'])) echo 'hidden'; ?>">Еще нет аккаунта? <a
                        href="registration.php">Зарегистрироваться</a></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="cart-section section-page">
        <div class="container">
            <h3 class="title">Корзина</h3>
            <?php
            // Подключение к базе данных
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            // Проверка соединения
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Ваш SQL-запрос
            $sql_query = "SELECT
                            b.id AS `номер билета`,
                            b.stoimost AS `стоимость`,
                            b.nomer_mesta AS `номер места`,
                            b.nomer_ryada AS `номер ряда`,
                            p.nazvanie AS `название`,
                            s.data AS `дата`,
                            s.vreamya AS `время`
                        FROM
                            bileti AS b
                        JOIN
                            spectakl AS s ON b.id_spectakl = s.id
                        JOIN
                            postanovka AS p ON s.id_postanovka = p.id
                        LEFT JOIN
                            zriteli_bileti AS zb ON b.id = zb.id_bileti
                        WHERE 
                            b.id IN (SELECT id FROM temp_tickets)";

            // Выполнение запроса
            $result = $conn->query($sql_query);

            // Если есть результаты, выводим их в таблице
            if ($result->num_rows > 0) {
                echo "<table border='1'>";
                // Вывод заголовков таблицы
                echo "<tr>";
                while ($field = $result->fetch_field()) {
                    echo "<th>" . $field->name . "</th>";
                }
                echo "</tr>";
                // Вывод данных
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<td>" . $value . "</td>";
                    }
                    // Добавляем кнопку справа от каждой строки
                    echo "<td><form method='post'><input type='hidden' name='ticket_id' value='" . $row['номер билета'] . "'><button type='submit' name='delete_ticket' class ='delete_ticket'>Удалить</button></form></td>";
                    echo "</tr>";
                }
                echo "</table>";
                
            } else {
                // Если результаты отсутствуют, выводим сообщение
                echo "<p>Нет данных для отображения.</p>";
            }

            // Закрываем соединение с базой данных
            $conn->close();
            ?>
            <div style="text-align: center;">
            <form method='post'>
            <button type="submit" name="order_tickets" align = "center" class ="order_tickets">Оформить заказ</button>
            </form>
        </div>
        </div>
    </section>
    <section class="user-tickets-section section-page">
    <div class="container">
        <h3 class="title">Билеты пользователя</h3>
        <?php
        // Подключение к базе данных
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        // Проверка соединения
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Получаем ID текущего пользователя
        $user_id = $_SESSION['user_id'];

        // SQL-запрос для получения билетов текущего пользователя
        $sql_user_tickets = "SELECT
                                b.id AS `номер билета`,
                                b.stoimost AS `стоимость`,
                                b.nomer_mesta AS `номер места`,
                                b.nomer_ryada AS `номер ряда`,
                                p.nazvanie AS `название спектакля`,
                                s.data AS `дата`,
                                s.vreamya AS `время`
                            FROM
                                zriteli_bileti AS zb
                            JOIN
                                bileti AS b ON zb.id_bileti = b.id
                            JOIN
                                spectakl AS s ON b.id_spectakl = s.id
                            JOIN
                                postanovka AS p ON s.id_postanovka = p.id
                            WHERE
                                zb.id_zriteli = $user_id";

        // Выполнение запроса
        $result_user_tickets = $conn->query($sql_user_tickets);

        // Если есть результаты, выводим их в таблице
        if ($result_user_tickets->num_rows > 0) {
            echo "<table border='1'>";
            // Вывод заголовков таблицы
            echo "<tr>";
            while ($field = $result_user_tickets->fetch_field()) {
                echo "<th>" . $field->name . "</th>";
            }
            echo "</tr>";
            // Вывод данных
            while ($row = $result_user_tickets->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            // Если результаты отсутствуют, выводим сообщение
            echo "<p>У вас пока нет билетов.</p>";
        }

        // Закрываем соединение с базой данных
        $conn->close();
        ?>
    </div>
</section>


    <!-- Include necessary scripts at the bottom -->
    <script>
        // JavaScript код для обработки события нажатия на кнопку "Удалить"
        const deleteButtons = document.querySelectorAll('button[name="delete_ticket"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const ticketId = this.parentNode.querySelector('input[name="ticket_id"]').value;
                // Отправка запроса на сервер для удаления билета с указанным ID
                fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'delete_ticket=&ticket_id=' + ticketId,
                })
                .then(response => {
                    if (response.ok) {
                        // Обновление страницы после успешного удаления
                        window.location.reload();
                    } else {
                        console.error('Ошибка удаления билета');
                    }
                })
                .catch(error => {
                    console.error('Ошибка удаления билета:', error);
                });
            });
        });
    </script>


</body>

</html>
