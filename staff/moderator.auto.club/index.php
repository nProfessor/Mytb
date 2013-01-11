<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 19.12.12 20:47
 * File name: index.php
 */


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Модерация клубов");
?>
<?$APPLICATION->IncludeComponent("staff:moderator.auto.club", "", array(
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>