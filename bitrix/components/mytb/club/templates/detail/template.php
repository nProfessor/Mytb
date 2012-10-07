<? $APPLICATION->AddHeadScript('http://api-maps.yandex.ru/2.0/?load=package.full&mode=debug&lang=ru-RU');
$APPLICATION->AddHeadScript('/jslibs/script/rating.js');
?>
<?
global $USER;
$clubInfo = $arResult['arFields'];
$address  = $arResult['arFields']["PROPERTY_ADDRESS_VALUE"];
$name     = html_entity_decode($arResult['arFields']['NAME']);
$searh    = empty($address)
    ? $name
    : $address;


$rating = empty($clubInfo["PROPERTY_RATING_VALUE"])
    ? 0
    : $clubInfo["PROPERTY_RATING_VALUE"];

$APPLICATION->SetPageProperty('description',strip_tags($clubInfo["~DETAIL_TEXT"]));

?>
<input type="hidden" value="<?=$clubInfo['ID']?>" id="clubID">
<div class="popover bottom" id="rating-like">
    <div class="arrow"></div>
    <h3 class="popover-title">Рейтинг клуба</h3>

    <div class="popover-content">
        <table>
            <tr>
                <td style="width: 100px;">Голосовать:</td>
                <td><?$APPLICATION->IncludeComponent(
                    "infocom:like",
                    "",
                    Array(
                         "VKONTAKTE"       => "Y",
                         //            "FACEBOOK"=>"Y",
                         //            "FACEBOOK_TYPE"=>"button_count",
                         "VKONTAKTE_TYPE"  => "mini ",
                         "VKONTAKTE_APIID" => "3009096",
                    )
                );?></td>
            </tr>
        </table>
    </div>
</div>
<div id="rating" data-placement="left">
    <a id="rating-a" class="btn btn-small" href="#"><i class="icon-star"></i>
        <?=$rating?> <?=declOfNum($rating, array("голос", "голоса", "голосов"))?>
    </a>
</div>
<h1>
    <?=$name?>
</h1>
<div class="club_info">
    <div class="span3">
        <table class="club_dop_info">
            <tr>
                <th>
                    Часы работы
                </th>
                <td>
                    <?=empty($clubInfo["PROPERTY_TIME_WORKING_VALUE"])
                    ? "—"
                    : str_replace(",", "<br>", $clubInfo["PROPERTY_TIME_WORKING_VALUE"]);?>
                </td>
            </tr>
            <tr>
                <th>
                    Цена коктейля
                </th>
                <td><?=empty($clubInfo["PROPERTY_PRICE_COCKTAIL_VALUE"])
                    ? "—"
                    : $clubInfo["PROPERTY_PRICE_COCKTAIL_VALUE"];?>
                </td>
            </tr>
            <tr>
                <th>
                    Телефон
                </th>
                <td>
                    <?if (is_array($clubInfo["PROPERTY_PHONE_VALUE"]) && count($clubInfo["PROPERTY_PHONE_VALUE"])): ?>
                    <? foreach ($clubInfo["PROPERTY_PHONE_VALUE"] as $phone): ?>
                        <?= $phone ?>
                        <? endforeach; ?>
                    <? else: ?>
                    <?=
                    empty($clubInfo["PROPERTY_PHONE_VALUE"])
                        ? "нет информации"
                        : $clubInfo["PROPERTY_PHONE_VALUE"]
                    ; ?>
                    <?endif;?>
                </td>
            </tr>
            <tr>
                <th>
                    Музыка
                </th>
                <td>
                    <?if (is_array($clubInfo["PROPERTY_MUSIC_VALUE"]) && count($clubInfo["PROPERTY_MUSIC_VALUE"])): ?>
                    <?= implode(", ", $clubInfo["PROPERTY_MUSIC_VALUE"]) ?>
                    <? else: ?>
                    <?=
                    empty($clubInfo["PROPERTY_MUSIC_VALUE"])
                        ? "нет информации"
                        : $clubInfo["PROPERTY_MUSIC_VALUE"]
                    ; ?>
                    <?endif;?>
                </td>
            </tr>
            <tr>
                <th>
                    Фейсконтроль
                </th>
                <td>
                    <?=empty($clubInfo["PROPERTY_FACE_CONTROL_VALUE"])
                    ? "нет информации"
                    : $clubInfo["PROPERTY_FACE_CONTROL_VALUE"];?>
                </td>
            </tr>
            <tr>
                <th>
                    Дресс-код
                </th>
                <td>
                    <?=empty($clubInfo["PROPERTY_DRESS_CODE_VALUE"])
                    ? "нет информации"
                    : $clubInfo["PROPERTY_DRESS_CODE_VALUE"];?>
                </td>
            </tr>
            <tr>
                <td colspan=2>
                    <?if ($USER->IsAuthorized() && 0): ?>
                    <br>
                    <a href="/club/booking/<?=$clubInfo["ID"]?>" class="btn btn-success btn-large" style="width: 90%;"
                       target="_blank">
                        Забронировать столик
                    </a>
                    <? endif?>
                </td>
            </tr>
        </table>
    </div>
    <div class="span4">
        <div class="club_info_text">

            <!--        <a href="/club/booking/--><?//=$clubInfo["ID"]?><!--" class="btn btn-primary">-->
            <!--            Подписаться-->
            <!--        </a>-->

            <div class="btn-group">
                <button class="btn btn-info btn-large" id="subs_stock" data-toggle="modal">Акции</button>
                <button class="btn btn-info btn-large" id="subs_event" data-toggle="modal">События</button>
                <!--                <button class="btn btn-info btn-large" id="subs_news" data-toggle="modal">Новости</button>-->
            </div>
            <!--            <div class="clear_both"></div>-->

            <div class="club_info">
                <?=$clubInfo["~DETAIL_TEXT"]?>
            </div>

            <?if (!empty($clubInfo["PROPERTY_SITE_VALUE"])): ?>
            <div>
                <b class="right">
                    <small>Сайт: <?=str_replace("http://", "", $clubInfo["~PROPERTY_SITE_VALUE"]);?></small>
                </b>
            </div>
            <? endif;?>
        </div>
        <div class="clear_both"></div>
    </div>
</div>
<br/>
<table>
    <tr>
        <td>рассказать друзьям:</td>
        <td>
            <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
            <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"
                 data-yashareQuickServices="yaru,vkontakte,facebook,twitter,moimir,lj,gplus"></div>
        </td>
    </tr>
</table>

<div class="club_address">
    <span>Адрес:</span> <?=empty($clubInfo["PROPERTY_METRO_VALUE"])
    ? ""
    : "м. " . $clubInfo["PROPERTY_METRO_VALUE"];?> <?=empty($clubInfo["PROPERTY_ADDRESS_VALUE"])
    ? ""
    : $clubInfo["PROPERTY_ADDRESS_VALUE"];?>
</div>
<script type="text/javascript">
    /* При успешной загрузке API выполняется
  соответствующая функция */
    ymaps.ready(function () {
        /* Создание экземпляра карты и его привязка
    к контейнеру с id="YMapsID" */
        // Поиск координат центра Нижнего Новгорода
        ymaps.geocode('<?=$searh?>', { results:1 }).then(function (res) {
            // Выбираем первый результат геокодирования
            var firstGeoObject = res.geoObjects.get(0);

            var myMap = new ymaps.Map("YMapsID", {
                        center:firstGeoObject.geometry.getCoordinates(),
                        zoom:16
                    }
            );
            myPlacemark = new ymaps.Placemark(firstGeoObject.geometry.getCoordinates(), {
                iconContent:'<?=$name?>'
            }, {
                // Опции
                // Иконка метки будет растягиваться под ее контент
                preset:'twirl#blueStretchyIcon'
            }),
                    myMap.geoObjects
                            .add(myPlacemark);
            myMap.controls
                // Кнопка изменения масштаба
                    .add('zoomControl')
                // Список типов карты
                    .add('typeSelector')
                // Стандартный набор кнопок
                    .add('mapTools');


        });

    })
    ;
</script>

<!--$arResult['arFields']-->

<div id="YMapsID" style="height: 400px">

</div>
<br/>
<h2>Обсуждение:</h2>
<?$APPLICATION->IncludeComponent(
    "prmedia:vkontakte.comments",
    "",
    Array(
         "APP_ID"         => "3009096",
         "COUNT"          => "20",
         "ALLOW_GRAFFITI" => "Y",
         "ALLOW_PHOTOS"   => "Y",
         "ALLOW_VIDEOS"   => "Y",
         "ALLOW_AUDIO"    => "Y",
         "ALLOW_LINKS"    => "Y",
         "WIDTH"          => "605"
    )
);?>


<div class="modal hide fade" id="modal_subs">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Подписка на акция, события и новости клуба «<?=$clubInfo["NAME"]?>»</h6>
    </div>
    <div class="modal-body">
        <b>Хотите узнавать об акция, событиях и новостях клуба «<?=$clubInfo["NAME"]?>»?</b>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="no_subs">Не сейчас</a>
        <a href="#" class="btn btn-primary" id="subs_ok" data-auth="<?if ($USER->IsAuthorized()) {
            echo "yes";
        } else {
            echo "no";
        }?>">Да, хочу</a>
    </div>
</div>

<input id="redirect" type="hidden" value="<?=$_SERVER['REDIRECT_URL']?>">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array(), FALSE); ?>