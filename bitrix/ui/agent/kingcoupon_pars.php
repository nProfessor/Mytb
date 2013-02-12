<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 01.10.12
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */

function agent_kingcoupon_pars()
{


    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("mytb");
    $content = file_get_contents("http://kingcoupon.ru/offer/category/Food/");
    preg_match_all("#kingcoupon\.ru/offer/([0-9]+)/#is", $content, $arr);

    $xml = file_get_contents("http://kingcoupon.ru/offer/export/?refId=100132540459877345");

    $svg = new SimpleXMLElement($xml);

    $clubListID=array();
    foreach($arr[1] as $var){
        $clubListID[$var]=$var;
    }
    foreach ($svg->offers->offer as $var) {
        $R = array();
        if (in_array($var->id, $arr[1])) {

            $R['clubName'] = (string)$var->supplier->name;
            foreach ((array)$var->supplier->addresses as $address) {
                if(is_array($address)){
                foreach ((array)$address as $var1) {
                    $R['clubAdress'][] = (string)$var1->name;
                }}else{
                    $R['clubAdress'][] = $address->name;
                }
            }

            foreach ((array)$var->supplier->tel as $tel) {
                $R['clubPhone'][] = $tel;
            }


            $id = $var->id;
            $url = preg_replace("#http://#i", "", $var->supplier->url);
            $url = preg_replace("#^([^/]+)/.*#i", "\\1", $url);
            $url = str_replace("www.", "", $url);


            $R['url'] = $url;


            $arSelect = Array(
                "ID",
                "NAME"
            );


            $arFilter = Array(
                "IBLOCK_ID" => IB_CLUB_ID,
                "PROPERTY_SITE" => "%" . $url . "%");

            $res = CIBlockElement::GetList(Array("SORT" => "DESC"), $arFilter, FALSE, FALSE, $arSelect);
            ;
            if (!$res->Fetch()) {

                $PROP = array();
//                $PROP["PHONE"]   = $R['clubPhone'];
//                $PROP["ADDRESS"] = $R['clubAdress'];
                $PROP["SITE"] = $R['url'];
                $PROP["LIST"] = array(54);


                $el = new CIBlockElement();

                $arLoadProductArray = Array(
                    "IBLOCK_ID" => IB_CLUB_ID,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $R['clubName'],
                    "TAGS" => "kingcoupon",
                    "ACTIVE" => "Y",
                    "SORT" => "0"
                );

                if ($PRODUCT_ID = $el->Add($arLoadProductArray)){
                    foreach($R['clubAdress'] as $addressItem){
                        MyTbCore::Add(array(
                            "CLUB_ID"=>$PRODUCT_ID,
                            "SITY_ID"=>1,
                            "ADDRESS"=>$addressItem,
                            "PHONE"=>serialize($R['clubPhone'])
                        ),"address");

                        printAr($addressItem);
                    }
                }else{
                    echo "Error: " . $el->LAST_ERROR;
                }

            }
        }


    }


    return "agent_kingcoupon_pars();";
}

