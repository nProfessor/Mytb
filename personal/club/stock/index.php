<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список акций");
?>

<?$APPLICATION->IncludeComponent(
    "club:stock.edite",
    "",
    Array()
);?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>