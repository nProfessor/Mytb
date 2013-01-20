<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 28.09.12
 * Time: 21:47
 * To change this template use File | Settings | File Templates.
 */


AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserAddHandler");

function OnAfterUserAddHandler(&$arFields)
{
    if (intval($arFields["ID"]) > 0) {
        global $USER;

        $noticeDefault = array(
            "day"=> array(1,2,3,4,5,6,7),
            "metod" => array("sms","email"),
        );

        $PROP['USER'] = $arFields["ID"];
        $PROP["NOTICE"]        = serialize($noticeDefault);

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(),
            "IBLOCK_ID"      => IB_USER_PROPS,
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => $arFields["LOGIN"],
            "ACTIVE"         => "Y"
        );


        $el = new CIBlockElement();
        $el->Add($arLoadProductArray);
        return true;
    } else {
        return TRUE;
    }
}
