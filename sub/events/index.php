<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список ночных клубов, баров, ресторанов и кафе у которых в билжайшее время состоятся события");
?>


<?$APPLICATION->IncludeComponent("mytb:sub.events", "", array(
        "CACHE_TIME"=>600
    ),
    false
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>