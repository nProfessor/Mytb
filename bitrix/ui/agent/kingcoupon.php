<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 01.10.12
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */

function agent_kingcoupon()
{
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $xml = file_get_contents("http://kingcoupon.ru/offer/export/?refId=100132540459877345");

    $svg = new SimpleXMLElement($xml);

    foreach ($svg->offers->offer as $var) {
        $id       = $var->id;
        $url      = preg_replace("#http://#i", "", $var->supplier->url);
        $url      = preg_replace("#^([^/]+)/.*#i", "\\1", $url);
        $url      = str_replace("www.", "", $url);
        $arSelect = Array(
            "ID",
            "NAME"
        );
        if ($url != "" && $url != "vkontakte.ru" && $url != "facebook.ru") {
            $arFilter = Array(
                "IBLOCK_ID"    => IB_CLUB_ID,
                "PROPERTY_SITE"=> "%" . $url . "%");
            if ($res = CIBlockElement::GetList(Array("SORT"=> "DESC"), $arFilter, FALSE, FALSE, $arSelect)->Fetch()) {
                if (!$resStock = CIBlockElement::GetList(Array("SORT"=> "DESC"), array("CODE"=> $id,
                                                                                       "TAGS"=> "kingcoupon"), FALSE, FALSE, $arSelect)->Fetch()
                ) {

                    $el = new CIBlockElement;

                    $PROP            = array();
                    $PROP["URL"]     = $var->url; // свойству с кодом 12 присваиваем значение "Белый"
                    $PROP["CLUB_ID"] = $res["ID"]; // свойству с кодом 3 присваиваем значение 38


                    $arLoadProductArray = Array(
                        "IBLOCK_ID"             => IB_SUB_STOCK_ID,
                        "PROPERTY_VALUES"       => $PROP,
                        "NAME"                  => $var->name,
                        "ACTIVE_FROM"           => date("d.m.Y H:m:s", strtotime($var->beginsell)),
                        "ACTIVE_TO"             => date("d.m.Y H:m:s", strtotime($var->endsell)),
                        "CODE"                  => $id,
                        "TAGS"                  => "kingcoupon",
                        "ACTIVE"                => "Y", // активен
                        "PREVIEW_TEXT"          => strip_tags($var->description),
                        "DETAIL_PICTURE"        => CFile::MakeFileArray($var->picture)
                    );

                    if ($PRODUCT_ID = $el->Add($arLoadProductArray))
                        echo "New ID: " . $PRODUCT_ID;
                    else
                        echo "Error: " . $el->LAST_ERROR;
                }

            }
        }
    }

    return "agent_kingcoupon();";
}