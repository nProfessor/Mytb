<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Подписка на клубы");
?>

<?$APPLICATION->IncludeComponent("mytb:subscribe.list.clubs", "", array(),
    FALSE
);?>



<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>