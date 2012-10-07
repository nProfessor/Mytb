<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.08.12
 * Time: 22:24
 * To change this template use File | Settings | File Templates.
 */
class Table
{
    private $tableID;
    private $clubID;


    function __construct($clubID)
    {
        $this->clubID = $clubID;
    }


    /**
     * Возвращаем данные столика
     * @param $arData
     * @return CDBResult|CIBlockResult|string
     */
    function getInfo($tableID, $arData = false, $GetNext = false)
    {
        $arFilterTable = array("IBLOCK_ID" => IB_TABLE_ID, "PROPERTY_CLUB" => $this->clubID, "ID" => intval($tableID));
        $arSelectTable = array("ID");
        $arOrderTable = array();

        $arOrder = is_array($arData["arSelect"]) ? array_merge($arData["arOrder"], $arOrderTable) : $arOrderTable;
        $arGroupBy = is_array($arData["arGroupBy"]) ? array_merge($arData["arGroupBy"], array()) : false;
        $arNavStartParams = is_array($arData["arGroupBy"]) ? array_merge($arData["arNavStartParams"], array()) : false;
        $arSelect = is_array($arData["arSelect"]) ? array_merge($arData["arSelect"], $arSelectTable) : $arSelectTable;
        $arFilter = is_array($arData["arFilter"]) ? array_merge($arData["arFilter"], $arFilterTable) : $arFilterTable;

        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        if (!$res) {
            return Errors::run("404");
        }

        $ob = $GetNext ? $res->GetNext() : $res->Fetch();

        if (isset($ob["PREVIEW_PICTURE"])) {
            $arFile = CFile::GetFileArray($ob["PREVIEW_PICTURE"]);
            $ob["PREVIEW_PICTURE"] = $arFile["SRC"];
        }

        if (isset($ob["PROPERTY_PRICE_GROUP_VALUE"])) {
            $res = CIBlockElement::GetList(array(), array("ID"=>intval($ob["PROPERTY_PRICE_GROUP_VALUE"]),"IBLOCK_ID" => IB_PRICE_GROUP), false, false, array("NAME","ID","PROPERTY_PRICE"));
            $ob["PROPERTY_PRICE_GROUP"] = $res->Fetch();
        }

        return $ob;

    }




}
