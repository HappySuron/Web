
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Проверяем, была ли установлена сессия с идентификатором пользователя и пользователь аутентифицирован
if (!isset($_SESSION['user_id'])) {
    // Если пользователь не аутентифицирован, перенаправляем на страницу lk.php с параметром notification
    header("Location: lk.php?notification=auth_required");
    exit; // Завершаем выполнение текущего скрипта
}
?>
<!DOCTYPE html>
    <html lang="ru">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Билеты - GoGoL</title>
        <link rel="shortcut icon" href="images/icon.jpg" type="image/jpg">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,400;0,700;1,300&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/slick.css">
        <link rel="stylesheet" href="css/jquery.fancybox.css">
        <link rel="stylesheet" href="css/style.css">
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
                        <a class="menu__list-link menu__list-link-afisha" href="/tickets.php">БИЛЕТЫ</a>
                    </li>
                    <li class="menu__list-item">
                        <a class="menu__list-link" href="/lk.php">ЛИЧНЫЙ КАБИНЕТ</a>
                    </li>
                </ul>
            </nav>

            <div class="logo">
                <img src="images/logo.png" alt="Логотип" class="logo__img">
            </div>

        </header>

        <section class="tickets-section section-page">
            <div class="container">
                <h3 class="title">Билеты</h3>
                </br></br>
                <div>
                    <button class="filter-btn">Фильтр</button>
                    <button onclick="resetFilter()" class="filterN-btn">Сбросить фильтр</button>
                </div>
                <?php
                // Подключение к базе данных
                $db_host = 'localhost';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'theatre';

                // Устанавливаем соединение с базой данных
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                // Проверяем соединение
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Выполняем запрос
                $sql = "SELECT
                            b.id AS `ID билета`,
                            b.stoimost AS `stoimost`,
                            b.dostupnost AS `Доступность`,
                            b.nomer_mesta AS `nomer_mesta`,
                            b.nomer_ryada AS `nomer_ryada`,
                            s.id AS `id_s`,
                            p.nazvanie AS `nazvanie`,
                            p.junr AS `junr`,
                            p.author AS `author`,
                            s.data AS `date_s`,
                            s.vreamya AS `time_s`
                        FROM
                            bileti AS b
                        JOIN
                            spectakl AS s ON b.id_spectakl = s.id
                        JOIN
                            postanovka AS p ON s.id_postanovka = p.id
                        LEFT JOIN
                            zriteli_bileti AS zb ON b.id = zb.id_bileti
                        WHERE
                            (zb.id_bileti IS NULL AND b.dostupnost != 0)";


                        if (isset($_GET['stoimost']) && !empty($_GET['stoimost'])) {
                            $stoimost = $conn->real_escape_string($_GET['stoimost']);
                            $sql .= " AND b.stoimost = '$stoimost'";
                        }

                        if (isset($_GET['nomer_mesta']) && !empty($_GET['nomer_mesta'])) {
                            $nomer_mesta = $conn->real_escape_string($_GET['nomer_mesta']);
                            $sql .= " AND b.nomer_mesta = '$nomer_mesta'";
                        }

                        
                        if (isset($_GET['nomer_ryada']) && !empty($_GET['nomer_ryada'])) {
                            $nomer_ryada = $conn->real_escape_string($_GET['nomer_ryada']);
                            $sql .= " AND b.nomer_ryada = '$nomer_ryada'";
                        }

                        if (isset($_GET['nomer_ryada']) && !empty($_GET['nomer_ryada'])) {
                            $nomer_ryada = $conn->real_escape_string($_GET['nomer_ryada']);
                            $sql .= " AND b.nomer_ryada = '$nomer_ryada'";
                        }


                        if (isset($_GET['author']) && !empty($_GET['author'])) {
                            $author = $conn->real_escape_string($_GET['author']);
                            $sql .= " AND p .author = '$author'";
                        }

                        if (isset($_GET['nazvanie']) && !empty($_GET['nazvanie'])) {
                            $nazvanie = $conn->real_escape_string($_GET['nazvanie']);
                            $sql .= " AND p .nazvanie = '$nazvanie'";
                        }


                        if (isset($_GET['date_s']) && !empty($_GET['date_s'])) {
                            $date_s = $conn->real_escape_string($_GET['date_s']);
                            $sql .= " AND s.data = '$date_s'";
                        }

                        if (isset($_GET['time_s']) && !empty($_GET['time_s'])) {
                            $time_s = $conn->real_escape_string($_GET['time_s']);
                            $sql .= " AND s.vreamya = '$time_s'";
                        }
                        if (isset($_GET['junr'])) {
                            if (isset($_GET['junr'])) {
                                $junr = $conn->real_escape_string($_GET['junr']);
                                echo "Значение параметра junr: " . $junr; // Вывод значения параметра junr
                                $sql .= " AND p.junr = '$junr'";
                            }
                            
                        }
                        if (isset($_GET['id_s']) && !empty($_GET['id_s'])) {
                            $id_s = $conn->real_escape_string($_GET['id_s']);
                            $sql .= " AND s.id = '$id_s'";
                        }

                $result = $conn->query($sql);


                // Создаем заголовки таблицы
                echo "<table border='1' id='ticketsTable'>";
                echo "<tr>";
                while ($field = $result->fetch_field()) {
                    if ($field->name != 'ID билета' && $field->name != 'Доступность' && $field->name != 'id_s') {
                        $column_name = $field->name;
                        // Перевод английских названий на русский
                        switch ($column_name) {
                            case 'stoimost':
                                $column_name = 'Стоимость';
                                break;
                            case 'nomer_mesta':
                                $column_name = 'Номер места';
                                break;
                            case 'nomer_ryada':
                                $column_name = 'Номер ряда';
                                break;
                            case 'nazvanie':
                                $column_name = 'Название';
                                break;
                            case 'junr':
                                $column_name = 'Жанр';
                                break;
                            case 'author':
                                $column_name = 'Автор постановки';
                                break;
                            case 'date_s':
                                $column_name = 'Дата спектакля';
                                break;
                            case 'time_s':
                                $column_name = 'Время спектакля';
                                break;
                            default:
                                // По умолчанию оставляем английское название
                                break;
                        }
                        echo "<th>" . $column_name . "</th>";
                    }
                }
                echo "</tr>";

                // Создаем выпадающие списки для фильтрации
                echo "<tr>";
                $result->field_seek(0); // Возвращаем указатель результата к началу
                while ($field = $result->fetch_field()) {
                    if ($field->name != 'ID билета' && $field->name != 'Доступность' && $field->name != 'id_s') {
                        echo "<td>";
                        echo "<select id='$field->name'><option value=''>Все</option>";
                        // Получаем уникальные значения столбца и добавляем их в выпадающий список
                        $uniqueValues = array();
                        $result->data_seek(0); // Возвращаем указатель результата к началу
                        while ($row = $result->fetch_assoc()) {
                            $value = $row[$field->name];
                            if (!in_array($value, $uniqueValues)) {
                                $uniqueValues[] = $value;
                                echo "<option value='$value'>$value</option>";
                            }
                        }
                        echo "</select></td>";
                    }
                }
                echo "</tr>";

                // Возвращаем указатель результата к началу
                mysqli_data_seek($result, 0);

                // Выводим данные в таблицу
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        if ($key != 'ID билета' && $key != 'Доступность' && $key != 'id_s') {
                            echo "<td>" . $value . "</td>";
                        }
                    }
                    echo "<td><input type='checkbox' name='ticket_ids[]' value='" . $row['ID билета'] . "'></td>"; // добавленный чекбокс
                    echo "</tr>";
                }
                echo "</table>";

                // Закрываем соединение с базой данных
                $conn->close();
                ?>
            <button onclick="placeOrder()" class="order-btn">Заказать</button>
            </div>
        </section>

        <!-- Include necessary scripts at the bottom -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="js/slick.min.js"></script>
        <script src="js/jquery.fancybox.min.js"></script>
        <script src="js/main.js"></script>

        <script>
        $(document).ready(function() {
            // Функция для применения фильтров
            function applyFilter() {
                var url = new URL(window.location.href);
                $('select').each(function() {
                    var id = $(this).attr('id');
                    var val = $(this).val();
                    if (val !== '') {
                        url.searchParams.set(id, val);
                    } else {
                        url.searchParams.delete(id);
                    }
                });
                window.location.href = url.href;
            }

            // Функция для сброса фильтров
            window.resetFilter = function() {
                $('select').val('');
                applyFilter();
            };

            // Восстановление выбранных значений фильтров при загрузке страницы
            $('select').each(function() {
                var id = $(this).attr('id');
                var val = new URLSearchParams(window.location.search).get(id);
                if (val) {
                    $(this).val(val);
                }
            });

            // Привязка события нажатия кнопки фильтра
            $('.filter-btn').click(function() {
                applyFilter();
            });
        });
        
    </script>
    <script>
function placeOrder() {
    var ticketIds = [];
    $("input[name='ticket_ids[]']:checked").each(function() {
        ticketIds.push($(this).val());
    });
    if (ticketIds.length > 0) {
        // Выводим данные, которые будут отправлены на сервер
        console.log("Данные для отправки на сервер:", ticketIds);
        
        // Отправляем выбранные идентификаторы билетов на сервер для обработки заказа
        $.ajax({
            type: "POST",
            url: "place_order.php", // Путь к PHP файлу для обработки заказа
            data: { ticket_ids: ticketIds }, // Передаем выбранные ID билетов на сервер
            success: function(response) {
                alert("Билеты успешно заказаны!");
            },
            error: function(xhr, status, error) {
                alert("Произошла ошибка при заказе билетов.");
                console.error(xhr.responseText);
            }
        });
    } else {
        alert("Пожалуйста, выберите билеты для заказа.");
    }
}

    </script>

    </body>

    </html>
