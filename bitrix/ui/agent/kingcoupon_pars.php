<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 01.10.12
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
function agent_kingcoupon_pars()
{


    $content = file_get_contents("http://kingcoupon.ru/offer/category/Food/");
    preg_match_all("#kingcoupon\.ru/offer/([0-9]+)/#is", $content, $arr);

    $xml = file_get_contents("http://kingcoupon.ru/offer/export/?refId=100132540459877345");

    $svg = new SimpleXMLElement($xml);

    foreach ($svg->offers->offer as $var) {
        if (in_array($var->id, $arr[1])) {
            echo $var->id;
            /**
             * добавл€ем информацию о клубе
             */

            $R['clubName'] = (string)$var->supplier->name;
            foreach ((array)$var->supplier->addresses as $address) {
                foreach ((array)$address as $var1) {
                    $R['clubAdress'][] = (string)$var1->name;
                }
            }

            foreach ((array)$var->supplier->tel as $tel) {
                $R['clubPhone'][] = $tel;
            }


            /**
             * ƒобавл€ем информацию об акции
             */
            $id  = $var->id;
            $url = preg_replace("#http://#i", "", $var->supplier->url);
            $url = preg_replace("#^([^/]+)/.*#i", "\\1", $url);
            $url = str_replace("www.", "", $url);


            $R['url'] = $url;


            $arSelect = Array(
                "ID",
                "NAME"
            );


            $arFilter = Array(
                "IBLOCK_ID"    => IB_CLUB_ID,
                "PROPERTY_SITE"=> "%" . $url . "%");

            $res = CIBlockElement::GetList(Array("SORT"=> "DESC"), $arFilter, FALSE, FALSE, $arSelect);
            ;
            if (!$res->Fetch()) {

                $PROP            = array();
                $PROP["PHONE"]   = $R['clubPhone']; // свойству с кодом 12 присваиваем значение "Ѕелый"
                $PROP["ADDRESS"] = $R['clubAdress']; // свойству с кодом 3 присваиваем значение 38
                $PROP["SITE"]    = $R['url']; // свойству с кодом 3 присваиваем значение 38


                $el = new CIBlockElement();

                $arLoadProductArray = Array(
                    "IBLOCK_ID"             => IB_CLUB_ID,
                    "PROPERTY_VALUES"                  => $PROP,
                    "NAME"                  => $R['clubName'],
                    "TAGS"                  => "kingcoupon",
                    "ACTIVE"                => "Y"
                );



                                                if ($PRODUCT_ID = $el->Add($arLoadProductArray))
                                                    echo "New ID: " . $PRODUCT_ID;
                                                else
                                                    echo "Error: " . $el->LAST_ERROR;

            }
        }


    }
}

agent_kingcoupon_pars();