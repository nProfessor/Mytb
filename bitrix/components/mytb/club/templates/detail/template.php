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

<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="//vk.com/js/api/openapi.js?75"></script>

<script type="text/javascript">
    VK.init({apiId: <?=SOC_API_ID_VK?>, onlyWidgets: true});
</script>


<input type="hidden" value="<?=$clubInfo['ID']?>" id="clubID">

<div class="club_info">
    <div class="m_left w2  options">
        <div class="img-polaroid w2">
            <div  style="height: 200px;width: 200px;background: #fff url('<?=$clubInfo["PREVIEW_PICTURE"]?>') no-repeat center center" title="<?=$name?>"></div>
            </div>
        <?global $USER;?>

        <?if (in_array(GROUP_CONTEN,$USER->GetUserGroupArray()) || isset($_SESSION["ADMIN"])) { ?>
        <?$APPLICATION->AddHeadScript('/jslibs/script/admin.js');?>
        <div class="manager_panel">
            <a href="#" class="admin_club" data-id="<?=$clubInfo['ID']?>">Войти как клуб <?=$name?></a>
            <?if (isset($_SESSION["ADMIN"])&&!in_array(GROUP_MANAGER,$USER->GetUserGroupArray())): ?>
            <a href="#" class="admin" data-id="<?=$clubInfo['ID']?>">Вернуться как клуб администратор</a>
            <? endif;?>
        </div>
        <? }?>
</div>
    <div class="m_left w4 options">
        <div style="padding-left:30px">
        <h1><?=$name?></h1>

        <?if(!empty($clubInfo["PROPERTY_TIME_WORKING_VALUE"])):?>
        <dl>
            <dt>Часы работы</dt>
            <dd><?=str_replace(array(",",";"), "<br>", $clubInfo["PROPERTY_TIME_WORKING_VALUE"]);?></dd>
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
            <div class="clear_both"></div>
        </div>

<div class="margin_t_b_5">
    <?
    $APPLICATION->IncludeComponent("mytb:subscribe.button",
        "",
        array(
            "CLUB_ID"=> intval($clubInfo["ID"]),
            "CLUB_NAME"=> $clubInfo["NAME"],
        ), false);
    ?>
</div>


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
<div class="block_info" id="b_descr">
    <?=$clubInfo["~DETAIL_TEXT"]?>
</div>
<div class="block_info" id="b_news">
        <?
        $APPLICATION->IncludeComponent("mytb:club.list.news",
            "",
            array(
                "CLUB_ID"=> intval($clubInfo["ID"])
            ), false);
        ?>
</div>
<div class="block_info" id="b_event">
           <?
       $APPLICATION->IncludeComponent("mytb:club.list.event",
            "",
            array(
                "CLUB_ID"=> intval($clubInfo["ID"])
            ), false);
        ?>
</div>
<div class="block_info" id="b_photo">
    <noindex>
        На данный момент нет фото и видео
    </noindex>
</div>

        <div class="block_info" id="b_stock">
<?
            $APPLICATION->IncludeComponent("mytb:club.list.stock",
            "",
            array(
            "CLUB_ID"=> intval($clubInfo["ID"])
            ), false);
            ?>
        </div>

<div class="block_info" id="b_map">
    <?
    $APPLICATION->IncludeComponent("mytb:club.map",
        "",
        array(
            "CLUB_ID"=> intval($clubInfo["ID"]),
            "NAME"=> $name,
        ), false);
    ?>
</div>

<div class="block_info" id="b_reviews">
<?
    $APPLICATION->IncludeComponent(
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
    ));
?>

</div>
