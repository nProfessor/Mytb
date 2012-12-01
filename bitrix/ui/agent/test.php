<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 18.11.12 14:17
 * File name: test.php
 */

function test()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new Test();
    $kupon->setStoks();

    return "";
}