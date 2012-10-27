<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 01.10.12
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */

function kuponauktsion()
{
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $kupon = new KuponAuktsion();
    $kupon->setStoks();

    return "kuponauktsion();";
}



//"CODE"=>"kingcoupon"
//"PREVIEW_TEXT"=>"PREVIEW_TEXT"
//URL
//NAME