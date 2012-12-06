<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 24.10.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
class Event
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
            $this->clubID[0]=$clubID;
        }

    }

    function add($data){

        global $USER;

        $el = new CIBlockElement;


        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID"      => IB_SUB_EVENT_ID,
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


    /**
     * Возвращаем колличество акций для клубов
     * TODO нужно в фильтр вставить айдишники клубов
     * @return mixed
     */
    function getCount(){

        $ob = CIBlockElement::GetList(
            array("IBLOCK_ID" => "ASC"),
            array("IBLOCK_ID"               => IB_SUB_EVENT_ID,
                ">DATE_ACTIVE_TO"         => date("d.m.Y h:i:s")),
            array("PROPERTY_CLUB_ID"),
            FALSE,
            array());
        while ($row = $ob->Fetch()) {
            $clubListID[$row['PROPERTY_CLUB_ID_VALUE']] = intval($row['CNT']);
        }

        return $clubListID;
    }

    function update($newsID,$data){
        global $USER;

        $el = new CIBlockElement;

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID"      => IB_SUB_EVENT_ID,
            "ACTIVE_TO"      => date("d.m.Y",strtotime($data['ACTIVE_TO'])),
            "ACTIVE_FROM"      => date("d.m.Y",strtotime($data['ACTIVE_FROM'])),
            "NAME"           => trim(strip_tags($data['NAME'])),
            "DETAIL_TEXT"    => trim($data['DETAIL_TEXT']),
        );


        return $el->update($newsID,$arLoadProductArray);
    }

    /**
     * Воззвращаем список новостей клуба
     * @return mixed
     */
    function getList($filter=array()){
        $filter['IBLOCK_ID']=IB_SUB_EVENT_ID;
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
     * Воззвращаем список новостей клуба
     * @return mixed
     */
    function getListArray($filter=array()){
        $filter['IBLOCK_ID']=IB_SUB_EVENT_ID;
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
            ));

        $result=array();
        while($row=$ob->Fetch()){
            $result[]=$row;
        }

        return $result;
    }



}
