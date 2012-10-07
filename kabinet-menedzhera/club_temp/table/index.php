<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Редактор клуба");
?>

<?$APPLICATION->IncludeComponent(
    "mytb:club.edite.table",
    "",
    Array(
        "TABLE_ID"=>$_GET["ID"]
    )
);?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>