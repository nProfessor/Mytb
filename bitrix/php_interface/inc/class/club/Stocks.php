<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 24.10.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
class Stocks
{
    private $clubID;

    function __construct($clubID)
    {
        if(is_array($clubID)){
            $ar=array();
            foreach($clubID as $var){
                $ar[]=intval($var);
            }
            $this->clubID=$ar;
        }else{
            $this->clubID=$clubID;
        }

    }


    /**
     * Возвращаем колличество акций для клубов
     * @return mixed
     */
    function getCount($clubID=false){
        $arFilter = array(
            "IBLOCK_ID"       => IB_SUB_STOCK_ID,
            ">DATE_ACTIVE_TO" => date("d.m.Y h:i:s"));
            if (is_array($clubID)) {
                $arFilter["PROPERTY_CLUB_ID"]=$clubID;
            }

        $ob = CIBlockElement::GetList(
            array("IBLOCK_ID" => "ASC"),
            $arFilter,
            array("PROPERTY_CLUB_ID"),
            FALSE,
            array());
        while ($row = $ob->Fetch()) {
            $clubListID[$row['PROPERTY_CLUB_ID_VALUE']] = intval($row['CNT']);
        }

        return $clubListID;
    }



    function add($data){

        global $USER;

        $el = new CIBlockElement;


        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID"      => IB_SUB_STOCK_ID,
            "PROPERTY_VALUES"=> array('CLUB_ID'=>$this->clubID),
            "ACTIVE_TO"      => date("d.m.Y",strtotime($data['ACTIVE_TO'])),
            "ACTIVE_FROM"      => date("d.m.Y",strtotime($data['ACTIVE_FROM'])),
            "NAME"           => trim(strip_tags($data['NAME'])),
            "ACTIVE"         => "Y",// активен
            "DETAIL_TEXT"    => trim($data['DETAIL_TEXT']),
        );


        return $el->Add($arLoadProductArray);
    }

    /**
     * Возвращаем информацию о новости
     * @param $newsID
     * @return mixed
     */
    function getInfo($newsID,$getNext=false){
        $obj=$this->getList(array("ID"=>$newsID));
        return $getNext?$obj->GetNext():$obj->Fetch();
    }

    function update($newsID,$data){
        global $USER;

        $el = new CIBlockElement;

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID"      => IB_SUB_STOCK_ID,
            "ACTIVE_TO"      => date("d.m.Y",strtotime($data['ACTIVE_TO'])),
            "ACTIVE_FROM"      => date("d.m.Y",strtotime($data['ACTIVE_FROM'])),
            "NAME"           => trim(strip_tags($data['NAME'])),
            "DETAIL_TEXT"    => trim($data['DETAIL_TEXT']),
        );


        return $el->update($newsID,$arLoadProductArray);
    }

    /**
     * возвращаем ID всех клубов у которых есть акции
     * @return mixed
     */
    static  function getListHaveStocks(){
        $filter['IBLOCK_ID']=IB_SUB_STOCK_ID;
        $filter['>=DATE_ACTIVE_TO']=date("d.m.Y");

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            $filter,
            array("PROPERTY_CLUB_ID"),
            FALSE,
            array("PROPERTY_CLUB_ID"));
        $result=array();
        while($row=$ob->Fetch()){
            $result[]=intval($row["PROPERTY_CLUB_ID_VALUE"]);
        }
        return $result;
    }

    /**
     * Воззвращаем список акций клуба
     * @return mixed
     */
    function getList($filter=array()){
        $filter['IBLOCK_ID']=IB_SUB_STOCK_ID;
        $filter['PROPERTY_CLUB_ID']=$this->clubID;

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            $filter,
            false,
            FALSE,
            array(
                "ID",
                "NAME",
                "ACTIVE_FROM",
                "ACTIVE_TO",
                "DETAIL_TEXT",
                "PREVIEW_PICTURE"
            ));

        return $ob;
    }


    /**
     * Воззвращаем список акций клуба
     * @return mixed
     */
    function getListArray($filter=array()){
        $filter['IBLOCK_ID']=IB_SUB_STOCK_ID;
        $filter['PROPERTY_CLUB_ID']=$this->clubID;

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            $filter,
            false,
            FALSE,
            array(
                "ID",
                "NAME",
                "DATE_ACTIVE_FROM",
                "DATE_ACTIVE_TO",
                "ACTIVE_TO",
                "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_CLUB_ID",
                "DATE_ACTIVE_TO",
                "PROPERTY_URL",
                "PROPERTY_CLUB_ID",
                "PROPERTY_PRICECOUPON"
            ));
        $result=array();
        while($row=$ob->Fetch()){
            $result[]=$row;
        }

        return $result;
    }

}
