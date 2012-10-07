<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактор клуба");
?>

<?$APPLICATION->IncludeComponent(
    "mytb:club.edite",
    "temp",
    Array(
        "LIST_TABLE" => "Y",
        "APP_ID" => "3009096",
        "ALLOW_GRAFFITI" => "Y",
        "ALLOW_PHOTOS" => "Y",
        "ALLOW_VIDEOS" => "Y",
        "ALLOW_AUDIO" => "Y",
        "ALLOW_LINKS" => "Y",
        "WIDTH" => "720"
    )
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>