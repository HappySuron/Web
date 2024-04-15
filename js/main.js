$(function(){

    $('.menu__btn').on('click', function () {
        $('.menu__list').toggleClass('menu__list--active')
    });

    $('[data-fancybox]').fancybox({
        youtube : {
            controls : 1,
            showinfo : 1
        }
    });

    $('.heroes__slider-img').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        asNavFor: '.heroes__slider-text',
        prevArrow: '<button class="slick-btn slick-prev"><img src="images/arrow-left.png" alt="prev"></button>',
        nextArrow: '<button class="slick-btn slick-next"><img src="images/arrow-right.png" alt="next"></button>',
        responsive: [
          {
            breakpoint: 769,
            settings: {
              arrows: false
            }
          }
        ]
      });

      $('.heroes__slider-text').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        asNavFor: '.heroes__slider-img',
        fade: true,
        arrows: false
      });

});


// Обработчик события клика на кнопке "Посмотреть выступления"
// Обработчик события клика на кнопке "Посмотреть выступления"
$(document).on('click', '.menu__list-see', function() {
    // Получаем id спектакля из data-id атрибута кнопки
    var spectacleId = $(this).data('id');

    // Отправляем AJAX запрос
    $.ajax({
        url: 'get_spectacle_info.php', // Путь к файлу обработчику на сервере
        method: 'POST',
        data: { postanovkaId: spectacleId }, // Данные, которые отправляем на сервер
        success: function(response) {
            // Парсим JSON ответ
            var spectacleData = JSON.parse(response);

            // Очищаем таблицу
            $('#spectacleTable tbody').empty();

            // Добавляем данные в таблицу
            spectacleData.forEach(function(spectacle) {
                var newRow = '<tr>' +
                    '<td>' + spectacle.data + '</td>' +
                    '<td>' + spectacle.vreamya + '</td>' +
                    '<td>' + spectacle.nazvanie + '</td>' +
                    '<td>' + spectacle.junr + '</td>' +
                    '<td>' + spectacle.author + '</td>' +
                    '<td>' + spectacle.vremya_dlitelnosti + '</td>' +
                    '<td><button class="buy_tickets" data-id="' + spectacle.id + '">Купить билеты</button></td>' +
                    '</tr>';
                $('#spectacleTable tbody').append(newRow);
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText); // Если произошла ошибка, выводим её в консоль
        }
    });
});

// $(document).ready(function() {
//     // Прослушиваем событие клика на кнопке "Фильтр"
//     $('button.filter-btn').click(function(event) {
//         event.preventDefault(); // Предотвращаем стандартное поведение кнопки
//         var url = new URL(window.location.href);
//         $('select').each(function() {
//             var id = $(this).attr('id');
//             var val = $(this).val();
//             if (val !== '') {
//                 url.searchParams.set(id, val);
//             } else {
//                 url.searchParams.delete(id);
//             }
//         });
//         window.location.href = url.href;
//     });
// });




