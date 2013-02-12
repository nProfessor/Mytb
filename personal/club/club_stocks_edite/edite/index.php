<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактируем профайл клуба");
?>

<?$APPLICATION->IncludeComponent(
    "mytb:club.stock.edite",
    "",
    Array(
        "APP_ID" => "3009096",
        "COUNT" => "20",
        "ALLOW_GRAFFITI" => "Y",
        "ALLOW_PHOTOS" => "Y",
        "ALLOW_VIDEOS" => "Y",
        "ALLOW_AUDIO" => "Y",
        "ALLOW_LINKS" => "Y",
        "WIDTH" => "720"
    )
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>