<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 09.11.12
 * Time: 21:14
 * To change this template use File | Settings | File Templates.
 */
class Test extends Kupon
{
    public $xml = "http://zoombonus.ru/xml/kuponator";
    public $tags = "zoombonus";

    function getData()
    {

        $PROP = array();
        $PROP["URL"] = "test"; // свойству с кодом 12 присваиваем значение "Белый"
        $PROP["CLUB_ID"] = 1365; // свойству с кодом 3 присваиваем значение 38
        $PROP["PRICE"] = 123123123123; // свойству с кодом 3 присваиваем значение 38
        $PROP["DISCOUNT"] = "@13123123"; // свойству с кодом 3 присваиваем значение 38
        $PROP["DISCOUNTPRICE"] = 2132132; // свойству с кодом 3 присваиваем значение 38
        $PROP["PRICECOUPON"] = 123123213; // свойству с кодом 3 присваиваем значение 38


        $arLoadProductArray = Array(
            "IBLOCK_ID" => IB_SUB_STOCK_ID,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => "акция тест",
            "ACTIVE_FROM" => date("d.m.Y H:m:s"),
            "ACTIVE_TO" => date("d.m.Y H:m:s", strtotime("+1 day")),
            "TAGS" => "test",
            "ACTIVE" => "Y", // активен
            "PREVIEW_TEXT" => "sdafewqfewqfew"
        );

        $data[] = $arLoadProductArray;

        return $data;
    }

}