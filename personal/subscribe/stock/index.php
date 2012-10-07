<?
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) :
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    $APPLICATION->IncludeComponent("mytb:subscribe.list.stock", "ajax", array("AJAX"=>"Y"), FALSE); else:

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    $APPLICATION->SetTitle("Редактирование профайла");

    $APPLICATION->IncludeComponent("mytb:subscribe.list.stock", "", array(), FALSE);
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
endif;
?>
