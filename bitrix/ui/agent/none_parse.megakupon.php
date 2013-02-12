<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 27.10.12
 * Time: 14:35
 * To change this template use File | Settings | File Templates.
 */

function parse_megakupon()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("mytb");

    $kupon = new MegaKupon();
    $kupon->parse();

    return "parse_megakupon();";
}