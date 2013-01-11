<?
//$APPLICATION->AddHeadScript('http://api-maps.yandex.ru/2.0/?load=package.full&mode=debug&lang=ru-RU');
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
$ADDRESS = $arResult['ADDRESS'];
?>
<input type="hidden" value="<?=$clubInfo['ID']?>" id="clubID">

<div class="club_info">
    <div class="m_left w2  options">

        <?if(!empty($clubInfo["PREVIEW_PICTURE"])):?>
        <img src="<?=$clubInfo["PREVIEW_PICTURE"]?>" width="200px">
        <?endif;?>
</div>
    <div class="m_left w4 options">
        <h1><?=$name?></h1>

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


        <?if (!empty($clubInfo["PROPERTY_SITE_VALUE"])): ?>
        <dl>
            <dt>Сайт</dt>
            <dd><?=str_replace("http://", "", $clubInfo["~PROPERTY_SITE_VALUE"]);?></dd>
        </dl>

        <? endif;?>

    </div>


    <div class="m_left w4 content_padding_20">
        <div class="margin_t_b_5">


        <?$APPLICATION->IncludeComponent(
        "mytb:club.rating",
        "",
        Array(
            "CLUB_ID"  => $clubInfo['ID'],
        )
    );?>
        </div>


       <button class="button_subsribe subsribe-ok right margin_t_b_5"  data-toggle="modal" data-original-title="Вы сможете моментально узнавать о появлении акций проводимых в  <b>«<?=$clubInfo['NAME']?>»</b>" id="subs_ok" data-auth="<?=$USER->IsAuthorized()?"yes":"no";?>">Подписаться на акции<br/>«<?=$clubInfo['NAME']?>»</button>

        <table class="right margin_t_b_5">
            <tr>
                <td>рассказать друзьям:</td>
                <td>
                    <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
                    <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"
                         data-yashareQuickServices="yaru,vkontakte,facebook,twitter,moimir,lj,gplus"></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="clear_both"></div>
</div>

    <div>
        <ul class="w10 menu_club">
            <li><a href="#stock" data-block="stock" id="a_stock">Акции</a></li>
            <li><a href="#event" data-block="event" id="a_event">События</a></li>
            <li><a href="#news" data-block="news" id="a_news">Новости</a></li>
            <li><a href="#map" data-block="map" id="a_map">Схема проезда</a></li>
            <li><a href="#descr" data-block="descr" id="a_descr">Описание</a></li>
            <li><a href="#photo" data-block="photo" id="a_photo">Фото/видео</a></li>
            <li><a href="#reviews" data-block="reviews" id="a_reviews">Отзывы</a></li>
        </ul>
        <div class="clear_both"></div>
    </div>
<div class="block_info" id="news">
    Новости
</div>
<div class="block_info" id="event">
    События
</div>
<div class="block_info" id="photo">
    Фото/видео
</div>

        <div class="block_info" id="stock">
<?
            $APPLICATION->IncludeComponent("mytb:club.list.stock",
            "",
            array(
            "CLUB_ID"=> intval($clubInfo["ID"])
            ), false);
            ?>
        </div>
<div class="block_info" id="descr">
    <?=$clubInfo["~DETAIL_TEXT"]?>
</div>
<div class="block_info" id="map">
    <?
    $APPLICATION->IncludeComponent("mytb:club.map",
        "",
        array(
            "CLUB_ID"=> intval($clubInfo["ID"])
        ), false);
    ?>

    <script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU&onload=init"type="text/javascript"></script>

    <!--$arResult['arFields']-->

    <div id="YMapsID" style="height: 400px"></div>

    <script type="text/javascript">

        var myMap;
        function init() {




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
            },function (err) {
                console.log(err);
            });

        }

    </script>


</div>

<div class="block_info" id="reviews">
    <?$APPLICATION->IncludeComponent(
    "prmedia:vkontakte.comments",
    "",
    Array(
        "APP_ID"         => SOC_API_ID_VK,
        "COUNT"          => "20",
        "ALLOW_GRAFFITI" => "Y",
        "ALLOW_PHOTOS"   => "Y",
        "ALLOW_VIDEOS"   => "Y",
        "ALLOW_AUDIO"    => "Y",
        "ALLOW_LINKS"    => "Y",
        "WIDTH"          => "1000"
    )
);?>
</div>


<?if(0):?>
<div class="club_address">
    <span>Адрес:</span> <?=empty($clubInfo["PROPERTY_METRO_VALUE"])
    ? ""
    : "м. " . $clubInfo["PROPERTY_METRO_VALUE"];?> <?=empty($clubInfo["PROPERTY_ADDRESS_VALUE"])
    ? ""
    : $clubInfo["PROPERTY_ADDRESS_VALUE"];?>
</div>



<br/>

<?endif;?>

<div class="modal hide fade" id="modal_subs">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Подписка на акции в «<?=$clubInfo["NAME"]?>»</h6>
    </div>
    <div class="modal-body">
        <blockquote>
            <p>Хотите узнавать о новых акциях в <b>«<?=$clubInfo["NAME"]?>»</b>?</p>
            <small>Можно получать уведомления по СМС, Email, или просматривать их в личном кабинете.</small>
        </blockquote>

    </div>
    <div class="modal-footer">
        <a href="#" class="btn" id="no_subs">Не сейчас</a>
        <a href="#" class="btn btn-primary"  id="subs_ok_modal" data-auth="<?=$USER->IsAuthorized()?"yes":"no";?>">Да, хочу</a>
    </div>
</div>

<input id="redirect" type="hidden" value="/club/<?=$clubInfo["ID"]?>/stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array("AUTH_URL"=>"/club/".$clubInfo["ID"]."/stock/?subscribe=ok"),false); ?>

