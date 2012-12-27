<?php


function parse_vigoda()
{
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("mytb");

    $kupon = new Vigoda();
    $kupon->parse();

    return "parse_vigoda();";
}

