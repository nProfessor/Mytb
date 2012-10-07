<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Брони");
?>

<?$APPLICATION->IncludeComponent("mytb:booking.list", ".default", array(),false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>