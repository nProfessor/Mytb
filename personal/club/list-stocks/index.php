<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список акций");
?>

<?$APPLICATION->IncludeComponent(
    "club:stocks.list",
    "",
    Array()
);?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>