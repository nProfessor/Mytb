<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список ночных клубов, баров, ресторанов и кафе у которых есть действующие акции");
?>


<?$APPLICATION->IncludeComponent("mytb:sub.stocks", "", array(
        "CACHE_TIME"=>600
    ),
    false
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>