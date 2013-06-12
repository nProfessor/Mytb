<div id="breadcrumb">
    <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
        "START_FROM" => "1",
        "PATH" => "",
        "SITE_ID" => SITE_ID
    ),
    false
);?>
</div>
<h1><?=$arResult["ARTICLE"]['NAME']?></h1>
<?=$arResult["ARTICLE"]['DETAIL_TEXT']?>

<div class="friends_add">
<script type="text/javascript" src="//yandex.st/share/share.js"
charset="utf-8"></script>
<div class="yashare-auto-init" data-yashareL10n="ru"
 data-yashareType="link" data-yashareQuickServices="vkontakte,facebook,odnoklassniki,moimir,lj,gplus"></div>
</div>

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