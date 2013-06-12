<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

?>
<p><?$APPLICATION->IncludeComponent("mytb:article.list", "",array(),
	false
);?></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>