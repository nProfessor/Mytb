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
            "stock"=> array(
                "sms"  => 1,
                "email"=> 1,
                "count"=> 0
            ),
            "news" => array(
                "sms"  => 0,
                "email"=> 1,
                "count"=> 7
            ),
            "event"=> array(
                "sms"  => 1,
                "email"=> 1,
                "count"=> 3
            )
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
