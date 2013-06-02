/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 14.07.12
 * Time: 14:34
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    var myMap;
    var collection;
    var arrPlacemark = {};
    var club;
    var address;


// Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);

    function init() {
        // Создание экземпляра карты и его привязка к контейнеру с
        // заданным id ("map").

        myMap = new ymaps.Map('map', {
            // При инициализации карты обязательно нужно указать
            // её центр и коэффициент масштабирования.
            center:[55.76, 37.64], // Москва
            zoom:11
        });

        myMap.controls
            // Кнопка изменения масштаба.
            .add('zoomControl', { left:5, top:5 })
            // Список типов карты
            .add('typeSelector')
            // Стандартный набор кнопок
            .add('mapTools', { left:35, top:5 });

        collection = new ymaps.GeoObjectCollection();

        $.ajax({
            url:"/bitrix/ui/ajax/club/map.php",
            type:"post",
            dataType:"json",
            success:function (data) {

                if (data.status != "ok")
                    return;

                club = data.club;
                address = data.address;

                addMarkers();
            }
        });

    }

    $('#clear').toggle(function(){
        removeMarkers();
        $(".type_club").removeAttr('checked');
        $(this).val("Показать");
    },function(){
        $(this).val("Убрать");
        $(".type_club").attr('checked', 'checked');
        removeMarkers();
        addMarkers();
    });
    $('.type_club').click(function(){
        removeMarkers();
        addMarkers();
    });


    function removeMarkers() {
        collection.removeAll();
    }

    function addMarkers() {
        var LON;
        var LAT;

        var text;
        for (var i in club) {
            if(!issetType(club[i])){
                continue;
            }

            LON = address[club[i]['ID']]['LON'];
            LAT = address[club[i]['ID']]['LAT'];
            text =
                "<table class='PlacemarkInfo'><tr><td><img src='" + club[i]['PREVIEW_PICTURE'] + "'/></td><td>" +
                    "<div><b>Время работы:</b><br/> " + club[i]['PROPERTY_TIME_WORKING_VALUE'] + "</div>" +
                    "<div><b>Телефон:</b><br/> " + address[club[i]['ID']]['PHONE'] + "</div>" +
                    "</td></tr></table>";
            Placemark = new ymaps.Placemark([LON, LAT], {
                // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                balloonContentHeader:"<span class='PlacemarkInfoHeader'><a href='/club/" + club[i]['ID'] + "'  target='_blank'>" + club[i]['NAME'] + "</a></span>",
                balloonContentBody:text,
                balloonContentFooter:"<a href='/club/" + club[i]['ID'] + "/#stock' class='button orange' target='_blank'>Посмотреть акции и скидки</a>",
                hintContent:"<b class='PlacemarkInfoHint'>" + club[i]['NAME'] + "</b> " + club[i]['PROPERTY_TYPE_FACILITY'],
                metro:true
            });

            Placemark.events
                .add('mouseenter', function (e) {
                    // Ссылку на объект, вызвавший событие,
                    // можно получить из поля 'target'.
                    e.get('target').options.set('preset', 'twirl#greenIcon');
                })
                .add('mouseleave', function (e) {
                    e.get('target').options.unset('preset');
                });
            collection.add(Placemark);
        }


        myMap.geoObjects.add(collection);
    }

    function issetType(clubData){
        var arrTypes=[];
        $("input.type_club:checkbox:checked").each(function(){
            arrTypes.push($(this).val())
        });
        var dupl=[];
        dupl=duplicat(clubData['PROPERTY_TYPE_FACILITY_VALUE'],arrTypes);

        if(dupl.length>0){
            return true;
        }else{
            return false;
        }

    }

    function duplicat(b, c) {
        for (var d = [], e = {}, f = {}, a = 0; a < b.length; a++) e[b[a]] = !0;
        for (a = 0; a < c.length; a++) f[c[a]] = !0;
        for (var g in e) f[g] && d.push(g);
        return d
    }
});



