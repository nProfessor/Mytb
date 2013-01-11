<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 24.12.12 19:26
 * File name: parse.skidkacoupon.php
 */


function parse_skidka_coupon()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("mytb");

    $kupon = new SkidkaCoupon();
    $kupon->parse();

    return "parse_skidka_coupon();";
}