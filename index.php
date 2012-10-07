<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "клуб, бар, ресторан, кафе");
$APPLICATION->SetPageProperty("description", "MyTb.ru - Первыми узнаем об акциях, событиях и новостях ваших любимых заведений, клубах и барах.");
$APPLICATION->SetTitle("Все о клубах");
?>

<?$APPLICATION->IncludeComponent("mytb:club.list", "home", array(),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>