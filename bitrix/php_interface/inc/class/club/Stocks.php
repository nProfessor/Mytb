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
     * TODO нужно в фильтр вставить айдишники клубов
     * @return mixed
     */
    function getCount(){

        $ob = CIBlockElement::GetList(
            array("IBLOCK_ID" => "ASC"),
            array("IBLOCK_ID"               => IB_SUB_STOCK_ID,
                    ">DATE_ACTIVE_TO"         => date("d.m.Y h:i:s")),
            array("PROPERTY_CLUB_ID"),
            FALSE,
            array());
        while ($row = $ob->Fetch()) {
            $clubListID[$row['PROPERTY_CLUB_ID_VALUE']] = intval($row['CNT']);
        }

        return $clubListID;
    }


}
