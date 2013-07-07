<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU&onload=init"
        type="text/javascript"></script>


<div style="float:left;height: 520px;overflow-y: auto;" class="w3 address_list">
    <?foreach ($arResult['ADDRESS'] as $val => $address): ?>
    <div class="address_item"><i><?=($val + 1)?></i>

        <div class="content_addres" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <span><?=$address['ADDRESS']?></span>
            <br>
            <b>Телефон:</b><br>
            <ul>
                <?foreach ($address['PHONE'] as $phone): ?>
                <li itemprop="telephone"><?=$phone?></li>
                <? endforeach;?>
            </ul>
        </div>
    </div>
    <? endforeach;?>
    <div style="clear:both;"></div>
</div>
<div class="w7 right">
    <div id="YMapsID" style="height: 500px"></div>
</div>
<div class="clear_both"></div>

<?if ($arResult["ADDRESS"][0]['LAT'] == 0):
    foreach ($arResult['ADDRESS'] as $address) {
        $obj = json_decode(file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=" . urlencode(trim($address["ADDRESS"])) . "&format=json"));

        list($LAT, $LON) = explode(" ", $obj->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

        $res = new MyTbCore;
        $res->update(intval($address["ID"]), array(
            "LON" => $LON,
            "LAT" => $LAT,
        ), "address");
    }

endif;?>
<script type="text/javascript">

    $(document).ready(function () {
        $("#a_map").click(function () {
            console.log();
            myMap.container.fitToViewport();
        });
    });

    var myMap;


    function init() {
        myMap = new ymaps.Map("YMapsID", {
            center:[<?=$arResult["ADDRESS"][0]['LON']?>, <?=$arResult["ADDRESS"][0]['LAT']?>],
            zoom:12
        });

        myMap.controls
            // Кнопка изменения масштаба
                .add('zoomControl')
            // Кнопка изменения масштаба - компактный вариант
            // Расположим её справа
                .add('smallZoomControl', { right:5, top:75 })
            // Стандартный набор кнопок
                .add('mapTools');


    <?foreach ($arResult["ADDRESS"] as $val => $var): ?>
        myPlacemark = new ymaps.Placemark([<?=$var['LON']?>, <?=$var['LAT']?>], {
            iconContent:<?=($val + 1)?>
        }, {
            // Опции
            // Иконка метки будет растягиваться под ее контент
            preset:'twirl#blueStretchyIcon'
        });
        myMap.geoObjects.add(myPlacemark);

        <? endforeach;?>
    }

</script>
