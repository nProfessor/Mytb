<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 27.10.12
 * Time: 14:35
 * To change this template use File | Settings | File Templates.
 */

function megakupon()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new MegaKupon();
    $kupon->setStoks();

    return "megakupon();";
}