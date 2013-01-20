<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");?>

<?$APPLICATION->IncludeComponent("mytb:search.filter", "page", array(
        "GET"=>$_GET
    ),
    FALSE
);?>

<?$APPLICATION->IncludeComponent("mytb:search.filter.result", "", array(),
    FALSE
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>