<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Брони");
?>

<?$APPLICATION->IncludeComponent("club:photo.edite", ".default", array(),false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>