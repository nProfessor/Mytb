<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 28.10.12
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

function bigcoupon()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new BigCoupon();
    $kupon->setStoks();

    return "bigcoupon();";
}