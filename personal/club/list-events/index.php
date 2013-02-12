<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список событий");
?>
<?$APPLICATION->IncludeComponent(
    "club:events.list",
    "",
    Array()
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>