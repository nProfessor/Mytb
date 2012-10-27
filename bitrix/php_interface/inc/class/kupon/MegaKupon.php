<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 27.10.12
 * Time: 14:36
 * To change this template use File | Settings | File Templates.
 */

class MegaKupon extends Kupon
{
    public $xml="http://www.megakupon.ru/deals/xml";
    public $tags="megakupon";


    function  getData(){
        $data=array();

        $svg = new SimpleXMLElement(file_get_contents($this->xml));

        foreach ($svg->deals->deal as $var) {
            $id       = $var->id;
            $url      = preg_replace("#http://#i", "", $var->vendor_website_url);
            $url      = preg_replace("#^([^/]+)/.*#i", "\\1", $url);
            $url      = str_replace("www.", "", $url);
            $arSelect = Array("ID", "NAME");


            if ($this->filterSite($url)) {
                $arFilter = Array(
                    "IBLOCK_ID"    => IB_CLUB_ID,
                    "PROPERTY_SITE"=> "%" . $url . "%");

                if ($res = CIBlockElement::GetList(Array("SORT"=> "DESC"), $arFilter, FALSE, FALSE, $arSelect)->Fetch()) {

                    if (!$resStock = CIBlockElement::GetList(Array("SORT"=> "DESC"), array(
                        "CODE"=> $id,
                        "TAGS"=> $this->tags
                    ), FALSE, FALSE, $arSelect)->Fetch()
                    ) {
                        $PROP            = array();
                        $PROP["URL"]     = $var->deal_url; // свойству с кодом 12 присваиваем значение "Белый"
                        $PROP["CLUB_ID"] = $res["ID"]; // свойству с кодом 3 присваиваем значение 38
                        $PROP["PRICE"] = intval($var->value); // свойству с кодом 3 присваиваем значение 38
                        $PROP["DISCOUNT"] = intval($var->discount_percent); // свойству с кодом 3 присваиваем значение 38
                        $PROP["DISCOUNTPRICE"] = intval($var->discount_amount); // свойству с кодом 3 присваиваем значение 38
                        $PROP["PRICECOUPON"] = intval($var->price); // свойству с кодом 3 присваиваем значение 38


                        $arLoadProductArray = Array(
                            "IBLOCK_ID"             => IB_SUB_STOCK_ID,
                            "PROPERTY_VALUES"       => $PROP,
                            "NAME"                  => $var->title,
                            "ACTIVE_FROM"           => date("d.m.Y H:m:s", intval($var->start_timestamp)),
                            "ACTIVE_TO"             => date("d.m.Y H:m:s", intval($var->end_timestamp)),
                            "CODE"                  => $id,
                            "TAGS"                  =>  $this->tags,
                            "ACTIVE"                => "Y", // активен
                            "PREVIEW_TEXT"          => strip_tags($var->conditions),
                            "DETAIL_PICTURE"        => CFile::MakeFileArray($var->large_image_url)
                        );

                        $data[]=$arLoadProductArray;

                    }

                }
            }
        }

        return count($data)?$data:false;

    }

}


