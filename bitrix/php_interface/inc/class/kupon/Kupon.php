<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 24.10.12
 * Time: 18:58
 * To change this template use File | Settings | File Templates.
 */
class Kupon
{
    public $xml;
    public $tags;
    public $refLink=NULL;

    function __constructor(){

    }

    function  getData(){
        $data=array();

        $svg = new SimpleXMLElement(file_get_contents($this->xml));

        foreach ($svg->offers->offer as $var) {
            $id       = $var->id;
            $url      = preg_replace("#http://#i", "", $var->supplier->url);
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
                        $PROP["URL"]     = trim($var->url); // свойству с кодом 12 присваиваем значение "Белый"
                        $PROP["CLUB_ID"] = trim($res["ID"]); // свойству с кодом 3 присваиваем значение 38
                        $PROP["PRICE"] = intval($var->price); // свойству с кодом 3 присваиваем значение 38
                        $PROP["DISCOUNT"] = intval($var->discount); // свойству с кодом 3 присваиваем значение 38
                        $PROP["DISCOUNTPRICE"] = intval($var->discountprice); // свойству с кодом 3 присваиваем значение 38
                        $PROP["PRICECOUPON"] = intval($var->pricecoupon); // свойству с кодом 3 присваиваем значение 38


                        $arLoadProductArray = Array(
                            "IBLOCK_ID"             => IB_SUB_STOCK_ID,
                            "PROPERTY_VALUES"       => $PROP,
                            "NAME"                  => str_replace(array(' "','" '),array(" «","» "),trim($var->name)),
                            "ACTIVE_FROM"           => date("d.m.Y H:m:s", strtotime($var->beginsell)),
                            "ACTIVE_TO"             => date("d.m.Y H:m:s", strtotime($var->endsell)),
                            "CODE"                  => $id,
                            "TAGS"                  =>  trim($this->tags),
                            "ACTIVE"                => "Y", // активен
                            "PREVIEW_TEXT"          => trim(strip_tags($var->description)),
                            "DETAIL_PICTURE"        => CFile::MakeFileArray(trim($var->picture))
                        );

                       $data[]=$arLoadProductArray;

                    }

                }
            }
        }

        return count($data)?$data:false;

    }

    function setStoks(){
        $clubList = array();

        $data = $this->getData();

        if (!$data)
            return false;
        // Добавляем акции
        foreach ($data as $var) {
            $el = new CIBlockElement;
            $el->Add($var);
        }

//        $this->sendNotice($clubList);

    }

    /**
     * Посылаем уведомления подписчикам
     *
     * @param $clubList
     */
    public function sendNotice($clubList){
        if(!is_array($clubList)||!count($clubList)){
            return false;
        }

        $arUserList=array();
        $res = CIBlockElement::GetList(Array("SORT"=> "DESC"), array("PROPERTY_LINK_STOK"=>$clubList), false, false, array("PROPERTY_USER","PROPERTY_LINK_STOK","PROPERTY_NOTICE_VALUE"));

        /*
         * Заполняем пользователями массив
         * Всех этих людей нужно будет уведомить
         *
         */
        while ($res->Fetch()) {
            $arUserList[$res['PROPERTY_USER_VALUE']]["CLUB_LIST"]=$res['PROPERTY_LINK_STOK_VALUE'];// список клубов для рассылки
            $arUserList[$res['PROPERTY_USER_VALUE']]["SETTINGS"]=unserialize($res['PROPERTY_NOTICE_VALUE']); // настройки рассылки
        }


    }


    public function filterSite($url){
        if($url != "" && $url != "vkontakte.ru" && $url != "vk.com"&& $url != "facebook.ru"&&!preg_match("#(vkontakte|facebook)#is",$url)){
            return true;
        }else{
            return false;
        }

    }
}
