<?php
/**
 * User:        Олег
 * Data:        11.06.12 21:57
 * Site: http://sForge.ru
 */


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новости компании");
?>
<p><?$APPLICATION->IncludeComponent("mytb:profile", ".default", array(
),
    false
);?></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>