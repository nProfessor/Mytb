<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 15.07.12
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent("mytb:booking.list", "list", array(),false);
?>
