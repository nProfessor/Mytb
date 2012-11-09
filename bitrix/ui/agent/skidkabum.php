<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 09.11.12
 * Time: 21:07
 * To change this template use File | Settings | File Templates.
 */


function skidkabum()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new SkidkaBum();
    $kupon->setStoks();

    return "skidkabum();";
}