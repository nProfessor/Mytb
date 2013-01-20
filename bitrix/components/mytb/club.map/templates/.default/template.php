<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU&onload=init" type="text/javascript"></script>
<?=$arResult["ADDRESS"][0]['ADDRESS']?>
<div id="YMapsID" style="height: 500px"></div>

    <?if($arResult["ADDRESS"][0]['LAT']==0):
    foreach($arResult['ADDRESS'] as $address){
        $obj=json_decode(file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode(trim($address["ADDRESS"]))."&format=json"));

        list($LAT,$LON)=explode(" ",$obj->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

        $res=new MyTbCore;
        $res->update(intval($address["ID"]),array(
            "LON"=>$LON,
            "LAT"=>$LAT,
        ),"address");
        $arResult["ADDRESS"][0]['LON']=$LON;
        $arResult["ADDRESS"][0]['LAT']=$LAT;
    }

endif;?>
<script type="text/javascript">
    var myMap;


    function init(){
        myMap = new ymaps.Map ("YMapsID", {
            center: [<?=$arResult["ADDRESS"][0]['LON']?>, <?=$arResult["ADDRESS"][0]['LAT']?>],
            zoom: 12
        });

        myMap.controls
            // Кнопка изменения масштаба
                .add('zoomControl')
            // Кнопка изменения масштаба - компактный вариант
            // Расположим её справа
                .add('smallZoomControl', { right: 5, top: 75 })
            // Стандартный набор кнопок
                .add('mapTools');

    <?foreach($arResult["ADDRESS"] as $var):?>
        myPlacemark = new ymaps.Placemark([<?=$var['LON']?>, <?=$var['LAT']?>], {
            iconContent:'<?=$arResult["NAME"]?>'
        }, {
            // Опции
            // Иконка метки будет растягиваться под ее контент
            preset: 'twirl#blueStretchyIcon'
        });
        myMap.geoObjects.add(myPlacemark);
        <?endforeach;?>
    }

</script>
