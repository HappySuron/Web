<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Афиша - GoGoL</title>
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
                    <a class="menu__list-link menu__list-link-afisha" href="#">АФИША</a>
                </li>
                <li class="menu__list-item">
                    <a class="menu__list-link" href="/tickets.php">БИЛЕТЫ</a>
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

    <section class="heroes section-page">
        <div class="container">
            <h3 class="title">
                Спектакли
            </h3>
            <div class="heroes__inner">
                <div class="heroes__slider-img">
                    <?php
                        include 'db_connection.php'; // Подключаем файл с функциями подключения к базе данных

                        // Устанавливаем соединение с базой данных
                        $conn = OpenCon();

                        // Запрос к базе данных для получения путей к изображениям звездного состава
                        $sql_images = "SELECT url_kartinki FROM postanovka";
                        $result_images = $conn->query($sql_images);

                        // Вывод изображений звездного состава
                        if ($result_images->num_rows > 0) {
                            // Вывод данных каждого изображения
                            while($row = $result_images->fetch_assoc()) {
                                echo "<img class='heroes__images' src='" . $row["url_kartinki"] . "' alt=''>";
                            }
                        } else {
                            echo "Нет данных о звездном составе";
                        }

                        // Закрываем соединение с базой данных
                        CloseCon($conn);
                    ?>
                </div>
                <div class="heroes__slider-text">
                    <?php
                    // include 'db_connection.php'; // Подключаем файл с функциями подключения к базе данных

                    // Устанавливаем соединение с базой данных
                    $conn = OpenCon();

                    // Запрос к базе данных для получения информации о звездном составе
                    $sql = "SELECT id, nazvanie, opisanie, junr, author FROM postanovka";
                    $result = $conn->query($sql);

                    // Вывод информации о звездном составе
                    if ($result->num_rows > 0) {
                        // Вывод данных каждого актера
                        while($row = $result->fetch_assoc()) {
                            echo "<div class='heroes__text'>
                                    <h4 class='heroes__name'>" . $row["nazvanie"] . "</h4>
                                    <p>" . $row["opisanie"] . "</p>
                                    <p>" . $row["junr"] . "</p>
                                    <p>" . $row["author"] . "</p>
                                    <button class='menu__list-see' data-id='" . $row["id"] . "'>Посмотреть выступления</button>
                                </div>";
                        }
                    } else {
                        echo "Нет данных о звездном составе";
                    }

                    // Закрываем соединение с базой данных
                    CloseCon($conn);
                    ?>
                </div>
            </div>
        </div>
    </section>

    <section class="spectakl-table">
    <h3 class = "title">Информация о спектаклях</h3>
    <div class="container">
    <table id="spectacleTable">
    <thead>
    <tr>
        <th>Дата</th>
        <th>Время</th>
        <th>Название постановки</th>
        <th>Жанр</th>
        <th>Автор</th>
        <th>Длительность</th>
    </tr>
</thead>
<tbody>
<?php
// Проверяем наличие данных о спектаклях
if ($spectacleData && count($spectacleData) > 0) {
    // Если есть данные, выводим таблицу
    foreach ($spectacleData as $spectacle) {
        echo "<tr>" .
            "<td>" . $spectacle['data'] . "</td>" .
            "<td>" . $spectacle['vreamya'] . "</td>" .
            "<td>" . $spectacle['nazvanie'] . "</td>" .
            "<td>" . $spectacle['junr'] . "</td>" .
            "<td>" . $spectacle['author'] . "</td>" .
            "<td>" . $spectacle['vremya_dlitelnosti'] . "</td>" .
            "<td><button class='buy_tickets' data-id='" . $spectacle['id'] . "'>Купить билеты</button></td>" .
            "</tr>";
    }
} else {
    // Если данных нет, выводим сообщение
    echo "<tr><td colspan='6'>Необходимо выбрать спектакль</td></tr>";
}
?>


</tbody>

    </table>
        </div>
</section>


    <!-- Include necessary scripts at the bottom -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="js/main.js"></script>
    <script>
    $(document).ready(function() {
        // Обработка нажатия на кнопку "Купить билеты"
        $(document).on('click', '.buy_tickets', function() {
            // Получаем id спектакля
            var spectacleId = $(this).data('id');
            
            // Формируем URL с параметром id спектакля
            var url = '/tickets.php?id_s=' + spectacleId;
            
            // Переходим по сформированной ссылке
            window.location.href = url;
        });
    });
</script>



</body>

</html>
