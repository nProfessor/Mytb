<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 01.11.12
 * Time: 21:10
 * To change this template use File | Settings | File Templates.
 */


function skidka_coupon()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new SkidkaCoupon();
    $kupon->setStoks();

    return "skidka_coupon();";
}