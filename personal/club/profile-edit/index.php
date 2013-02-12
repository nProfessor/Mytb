<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Кабинет менеджера");
?>

<?$APPLICATION->IncludeComponent("club:profile.edite", ".default", array(),false);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>