<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 01.11.12
 * Time: 21:04
 * To change this template use File | Settings | File Templates.
 */

function skuponom()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new SKuponom();
    $kupon->setStoks();

    return "skuponom();";
}