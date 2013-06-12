<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent("mytb:article", "", array(
    "ARTICLE_ID"=>$_GET['ARTICLE_ID']
),false);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>