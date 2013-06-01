/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 14.07.12
 * Time: 14:34
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    var myMap;

// Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);

    function init() {
        // Создание экземпляра карты и его привязка к контейнеру с
        // заданным id ("map").

        myMap = new ymaps.Map('map', {
            // При инициализации карты обязательно нужно указать
            // её центр и коэффициент масштабирования.
            center:[55.76, 37.64], // Москва
            zoom:10
        });

        myMap.controls
            // Кнопка изменения масштаба.
            .add('zoomControl', { left:5, top:5 })
            // Список типов карты
            .add('typeSelector')
            // Стандартный набор кнопок
            .add('mapTools', { left:35, top:5 });


        $.ajax({
            url:"/bitrix/ui/ajax/club/map.php",
            type:"post",
            dataType:"json",
            success:function(data){

                if(data.status!="ok")
                    return;

               var club = data.club;
                var address = data.address;

                for (var i in club) {
                    var LON;
                    var LAT;
                    LON = address[club[i]['ID']]['LON'];
                    LAT = address[club[i]['ID']]['LAT'];

                    var text;

                    text=
                        "<table class='PlacemarkInfo'><tr><td><img src='"+club[i]['PREVIEW_PICTURE']+"'/></td>"+
                    "<td>" +
                    "<div><span class='PlacemarkInfoHeader'><a href='/club/"+club[i]['ID']+"'  target='_blank'>"+club[i]['NAME']+"</a></span></div>"+

                    "<div><b>Время работы:</b><br/> "+club[i]['PROPERTY_TIME_WORKING_VALUE']+"</div>"+
                    "<div><b>Телефон:</b><br/> "+address[club[i]['ID']]['PHONE']+"</div>"+
//                    "<div>"+club[i]['PREVIEW_PICTURE']+"</div>"+
                    "</td></tr></table>";
                    Placemark = new ymaps.Placemark([LON,LAT], {
                        // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                        balloonContentBody:text,
                        balloonContentFooter:"<a href='/club/"+club[i]['ID']+"/#stock' class='button orange' target='_blank'>Посмотреть акции и скидки</a>",
                        hintContent:"<b class='PlacemarkInfoHint'>"+club[i]['NAME']+"</b> "+club[i]['PROPERTY_TYPE_FACILITY']
                    });

                    myMap.geoObjects.add(Placemark);
                }
            }
        });




    }



});

