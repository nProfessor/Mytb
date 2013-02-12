<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список акций");
?>

<?$APPLICATION->IncludeComponent(
    "club:events.edite",
    "",
    Array()
);?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>