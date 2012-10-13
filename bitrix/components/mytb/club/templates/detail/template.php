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
    <div class="span3 options">


        <?if(!empty($clubInfo["PREVIEW_PICTURE"])):?>
        <img src="<?=$clubInfo["PREVIEW_PICTURE"]?>" width="200px">
        <?endif;?>



        <?if(!empty($clubInfo["PROPERTY_TIME_WORKING_VALUE"])):?>
        <dl>
            <dt>Часы работы</dt>
            <dd><?=str_replace(",", "<br>", $clubInfo["PROPERTY_TIME_WORKING_VALUE"]);?></dd>
        </dl>
        <?endif;?>

        <?if(!empty($clubInfo["PROPERTY_PRICE_COCKTAIL_VALUE"])):?>
        <dl>
            <dt>Цена коктейля</dt>
            <dd><?=$clubInfo["PROPERTY_PRICE_COCKTAIL_VALUE"];?></dd>
        </dl>
        <?endif;?>

        <?if(!empty($clubInfo["PROPERTY_PHONE_VALUE"])):?>
        <dl>
            <dt>Телефон</dt>
            <dd>
                <?if (is_array($clubInfo["PROPERTY_PHONE_VALUE"]) && count($clubInfo["PROPERTY_PHONE_VALUE"])): ?>
                <? foreach ($clubInfo["PROPERTY_PHONE_VALUE"] as $phone): ?>
                    <?= $phone ?><br/>
                    <? endforeach; ?>
                <? else: ?>
                <?=$clubInfo["PROPERTY_PHONE_VALUE"];?>
                <?endif;?>
            </dd>
        </dl>
        <?endif;?>


        <?if(!empty($clubInfo["PROPERTY_MUSIC_VALUE"])):?>
        <dl>
            <dt>Музыка</dt>
            <dd>
                <?if (is_array($clubInfo["PROPERTY_MUSIC_VALUE"]) && count($clubInfo["PROPERTY_MUSIC_VALUE"])): ?>
                <?= implode(", ", $clubInfo["PROPERTY_MUSIC_VALUE"]) ?>
                <? else: ?>
                <?=$clubInfo["PROPERTY_MUSIC_VALUE"]; ?>
                <?endif;?>
            </dd>
        </dl>
        <?endif;?>



        <?if(!empty($clubInfo["PROPERTY_FACE_CONTROL_VALUE"])):?>
        <dl>
            <dt>Фейсконтроль</dt>
            <dd><?=$clubInfo["PROPERTY_FACE_CONTROL_VALUE"];?></dd>
        </dl>
        <?endif;?>


        <?if(!empty($clubInfo["PROPERTY_DRESS_CODE_VALUE"])):?>
        <dl>
            <dt>Дресс-код</dt>
            <dd><?=$clubInfo["PROPERTY_DRESS_CODE_VALUE"];?></dd>
        </dl>
        <?endif;?>

        <?if ($USER->IsAuthorized() && 0): ?>
        <br>
        <a href="/club/booking/<?=$clubInfo["ID"]?>" class="btn btn-success btn-large" style="width: 90%;"
           target="_blank">
            Забронировать столик
        </a>
        <? endif?>


    </div>
    <div class="span4">
        <div class="club_info_text">

            <!--        <a href="/club/booking/--><?//=$clubInfo["ID"]?><!--" class="btn btn-primary">-->
            <!--            Подписаться-->
            <!--        </a>-->

            <div class="btn-group">
                <button class="btn btn-danger  btn-large subsribe-ok" style="width:100%" data-toggle="modal" data-original-title="Вы сможете моментально узнавать о появлении акций проводимых в  <b>«<?=$clubInfo['NAME']?>»</b>" id="subs_ok" data-auth="<?if ($USER->IsAuthorized()) {
                    echo "yes";
                } else {
                    echo "no";
                }?>">Подписаться на акции<br/>«<?=$clubInfo['NAME']?>»</button>
<!--                <button class="btn btn-info btn-large" id="subs_event" data-toggle="modal">События</button>-->
                <!--                <button class="btn btn-info btn-large" id="subs_news" data-toggle="modal">Новости</button>-->
            </div>
            <!--            <div class="clear_both"></div>-->

            <a href="/club/<?=$clubInfo['ID']?>/stock/"  id="subs_stock">смотреть акции клуба</a>
            <div class="club_info_content">
                <p>
                <?=$clubInfo["~DETAIL_TEXT"]?>
                </p>
                <div>
                    смотреть полностью
                </div>
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

    <div style="clear:both;"></div>
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
        <h6>Подписка на акции, события и новости клуба «<?=$clubInfo["NAME"]?>»</h6>
    </div>
    <div class="modal-body">
        <b>Хотите узнавать об акциях, событиях и новостях клуба «<?=$clubInfo["NAME"]?>»?</b>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="no_subs">Не сейчас</a>
        <a href="#" class="btn btn-primary"  id="subs_ok_modal" data-auth="<?if ($USER->IsAuthorized()) {
            echo "yes";
        } else {
            echo "no";
        }?>">Да, хочу</a>
    </div>
</div>

<input id="redirect" type="hidden" value="/club/<?=$clubInfo["ID"]?>/stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array("AUTH_URL"=>"/stock/?subscribe=ok&login=yes"),false); ?>