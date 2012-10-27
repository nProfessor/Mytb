<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>


<?$APPLICATION->IncludeComponent("mytb:profile", "", array(
        "USER_ID"=>$USER::GetID(),
        "CACHE_TIME"=>600
    ),
    false
);?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>