<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 11.08.12
 * Time: 18:01
 * To change this template use File | Settings | File Templates.
 */
class Club
{
    private $clubID;
    private $errors = array();


    function __construct($clubID)
    {
        //проверяем, естьли такой клуб
        //TODO нужно прееделать, так, что бы передавать массив ID клубов
        $arSelect = Array("ID");
        $arFilter = Array("IBLOCK_ID" => IB_CLUB_ID, "ID" => intval($clubID));
        $res = CIBlockElement::GetList(Array(), $arFilter, FALSE, FALSE, $arSelect);
        if ($res->SelectedRowsCount() > 0) {
            $this->clubID = intval($clubID);
        } else {
            $this->errors[] = "Такого клуба нет";
            $this->clubID = 0;
        }
    }

    /**
     * Объект новостей клуба
     * @return News
     */
    function news()
    {
        return new News($this->clubID);
    }

    /**
     * Объект событий клуба
     * @return Event
     */
    function event()
    {
        return new Event($this->clubID);
    }

    /**
     * Объект событий клуба
     * @return Event
     */
    function stock()
    {
        return new Stocks($this->clubID);
    }

    static function getOBonTheUserID($userID)
    {
        $clubID = self::getClubID($userID);

        if ($clubID) {
            return new self($clubID);
        } else {
            return Errors::run("404");
        }

    }

    static function getOBonTheClubID($clubID)
    {
        return new self($clubID);
    }


    /**
     * Возвращаем ID клуба, к которому принадлежит пользователь
     *
     * @param $userID
     */
    static function getClubID($userID)
    {
        $arSelect = Array("PROPERTY_CLUB");
        $arFilter = Array("IBLOCK_ID" => IB_CLUB_USER_ID, "ACTIVE" => "Y", "PROPERTY_USER" => $userID);
        $res = CIBlockElement::GetList(Array(), $arFilter, FALSE, FALSE, $arSelect);
        $arField = $res->Fetch();

        return intval($arField['PROPERTY_CLUB_VALUE']) == 0
            ? FALSE
            : intval($arField['PROPERTY_CLUB_VALUE']);
    }

    /**
     * Возвраща количество клубов
     * @return mixed
     */
    static function count()
    {
        $arSelect = Array("IBLOCK_ID");
        $arFilter = Array("IBLOCK_ID" => IB_CLUB_ID, "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, array("IBLOCK_ID"), FALSE, $arSelect);
        $arField = $res->Fetch();

        return $arField['CNT'];
    }
/**
 * Возвращаем ID клубов у которых есть акции
 */
    static function getListHaveStocks(){
        return Stocks::getListHaveStocks();
    }
    /**
     * Возвращаем ID клубов у которых есть акции
     */
    static function getListHaveEvent(){
        return Event::getListHaveEvents();
    }
    /**
     * Возвращаем ID клубов у которых есть новости
     */
    static function getListHaveNews(){
        return News::getListHaveNews();
    }


    /**
     * Возвращаем объект запроса к списку столиков
     * @param $arData
     *
     * @return CDBResult|CIBlockResult|string
     */
    function getResTableList($arData = FALSE)
    {

        $arFilterTable = array("IBLOCK_ID" => IB_TABLE_ID, "PROPERTY_CLUB" => $this->clubID);
        $arSelectTable = array("ID");
        $arOrderTable = array();

        $arOrder = is_array($arData["arSelect"])
            ? array_merge($arData["arOrder"], $arOrderTable)
            : $arOrderTable;
        $arGroupBy = is_array($arData["arGroupBy"])
            ? array_merge($arData["arGroupBy"], array())
            : FALSE;
        $arNavStartParams = is_array($arData["arGroupBy"])
            ? array_merge($arData["arNavStartParams"], array())
            : FALSE;
        $arSelect = is_array($arData["arSelect"])
            ? array_merge($arData["arSelect"], $arSelectTable)
            : $arSelectTable;
        $arFilter = is_array($arData["arFilter"])
            ? array_merge($arData["arFilter"], $arFilterTable)
            : $arFilterTable;


        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        return $res;
    }


    /**
     * Возвращаем объект результата выборки списка ценовых свойств
     *
     * @param bool $arData
     *
     * @return CDBResult|CIBlockResult|string
     *
     */
    function getPriceGroupList($arData = FALSE)
    {
        $arFilterTable = array("IBLOCK_ID" => IB_PRICE_GROUP, "PROPERTY_CLUB" => $this->clubID);
        $arSelectTable = array("ID");
        $arOrderTable = array();

        $arOrder = is_array($arData["arSelect"])
            ? array_merge($arData["arOrder"], $arOrderTable)
            : $arOrderTable;
        $arGroupBy = is_array($arData["arGroupBy"])
            ? array_merge($arData["arGroupBy"], array())
            : FALSE;
        $arNavStartParams = is_array($arData["arGroupBy"])
            ? array_merge($arData["arNavStartParams"], array())
            : FALSE;
        $arSelect = is_array($arData["arSelect"])
            ? array_merge($arData["arSelect"], $arSelectTable)
            : $arSelectTable;
        $arFilter = is_array($arData["arFilter"])
            ? array_merge($arData["arFilter"], $arFilterTable)
            : $arFilterTable;

        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        return $res;
    }

    /**
     * Возвращаем объект столика
     * @return Table
     */
    function table()
    {
        return new Table($this->clubID);
    }

    function getInfo($arData = FALSE, $GetNext = FALSE)
    {

        $arFilterClub = array("IBLOCK_ID" => IB_CLUB_ID, "ID" => $this->clubID);
        $arSelectClub = array("ID", "IBLOCK_ID");
        $arOrderClub = array();

        $arOrder = is_array($arData["arSelect"])
            ? array_merge((array)$arData["arOrder"], (array)$arOrderClub)
            : $arOrderClub;
        $arGroupBy = is_array($arData["arGroupBy"])
            ? array_merge((array)$arData["arGroupBy"], array())
            : FALSE;
        $arNavStartParams = is_array($arData["arGroupBy"])
            ? array_merge((array)$arData["arNavStartParams"], array())
            : FALSE;
        $arSelect = is_array($arData["arSelect"])
            ? array_merge((array)$arData["arSelect"], (array)$arSelectClub)
            : $arSelectClub;
        $arFilter = is_array($arData["arFilter"])
            ? array_merge((array)$arData["arFilter"], (array)$arFilterClub)
            : $arFilterClub;


        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        return $GetNext
            ? $res->GetNext()
            : $res->Fetch();
    }


    /**
     * Возвращаем объект запроса к списку броней
     * @param $arData
     *
     * @return CDBResult|CIBlockResult|string
     */
    function getResBookingList($arData = FALSE)
    {

        $arFilterTable = array("IBLOCK_ID" => IB_BOOKING_ID, "PROPERTY_CLUB" => $this->clubID);
        $arSelectTable = array("ID");
        $arOrderTable = array();

        $arOrder = is_array($arData["arSelect"])
            ? array_merge((array)$arData["arOrder"], $arOrderTable)
            : $arOrderTable;
        $arGroupBy = is_array($arData["arGroupBy"])
            ? array_merge((array)$arData["arGroupBy"], array())
            : FALSE;
        $arNavStartParams = is_array($arData["arGroupBy"])
            ? array_merge((array)$arData["arNavStartParams"], array())
            : FALSE;
        $arSelect = is_array($arData["arSelect"])
            ? array_merge((array)$arData["arSelect"], $arSelectTable)
            : $arSelectTable;
        $arFilter = is_array($arData["arFilter"])
            ? array_merge((array)$arData["arFilter"], $arFilterTable)
            : $arFilterTable;


        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        return $res;
    }

    /**
     * Записываем новое значение рейтинга клуба
     * @param $socset
     */
    function setNewRating($count, $socset = "VK")
    {
        CIBlockElement::SetPropertyValueCode($this->clubID, "RATING", intval($count));
    }

    static function getList($arOrder = Array("SORT" => "ASC"), $arFilter = Array(), $arGroupBy = FALSE, $arNavStartParams = FALSE, $arSelectFields = Array())
    {
        $arFilter["IBLOCK_ID"] = IB_CLUB_ID;

        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

        return $res;
    }


    function getListStok()
    {

        $ob = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            array("PROPERTY_CLUB_ID" => $this->clubID,
                "IBLOCK_ID" => IB_SUB_STOCK_ID,
                ">=DATE_ACTIVE_TO" => date("d.m.Y")),
            FALSE,
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
        return $ob;
    }

    function getListEvent()
    {
        $ob = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            array("PROPERTY_CLUB_ID" => $this->clubID,
                ">=DATE_ACTIVE_TO" => date("d.m.Y"),
                "IBLOCK_ID" => IB_SUB_EVENT_ID),
            FALSE,
            FALSE,
            array(
                "ID",
                "NAME",
                "ACTIVE_FROM",
                "ACTIVE_TO",
                "PREVIEW_TEXT",
                "DETAIL_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_CLUB_ID",
                "DATE_ACTIVE_TO"
            ));
        return $ob;

    }

    function getListNews()
    {
        $ob = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            array("PROPERTY_CLUB_ID" => $this->clubID,
                "IBLOCK_ID" => IB_SUB_NEWS_ID),
            FALSE,
            FALSE,
            array(
                "ID",
                "NAME",
                "DATE_ACTIVE_FROM",
                "DATE_ACTIVE_TO",
                "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_CLUB_ID",
                "DATE_ACTIVE_TO"
            ));
        return $ob;

    }


    /* Staff */
    /**
     * Возвращаем список клубов пока что не прошедших модерацию
     */
    static function getListModerator()
    {
        $list = array();
        $res = CIBlockElement::GetList(array(), array("PROPERTY_LIST" => PROP_CLUB_MODERATOR, "IBLOCK_ID" => IB_CLUB_ID), false, false, array("ID", "NAME", "PROPERTY_LIST"));

        while ($row = $res->Fetch()) {
            $list[] = $row;
        }

        return $list;

    }

    /**
     * Возвращает список адресов
     * @return array
     */

    function getAddress()
    {

        $res = MyTbCore::GetList(array(), array("CLUB_ID" => $this->clubID), false, false, array("*"), "address");

        $result = array();
        while ($row = $res->Fetch()) {
            $row["PHONE"] = unserialize($row["PHONE"]);
            $result[] = $row;
        }
        return $result;
    }


    /**
     * Обнавляем информацию о клубе
     * @param $info
     */
    function update($info){
        $list = array();

        $el = new CIBlockElement;


        $el->Update($this->clubID, array("NAME" =>$info['NAME']));





        return CIBlockElement::SetPropertyValues($this->clubID,IB_CLUB_ID,array(
            "SITE" =>$info['SITE'],
            "LIST"=>array(PROP_CLUB_MODERATOR),
            "EMAIL_MANAGER" =>$info['EMAIL_MANAGER'],
            "AVERAGE_CHECK" =>$info['AVERAGE_CHECK'],
            "TIME_WORKING" =>$info['TIME_WORKING'],
            "KIND_CLUB" =>$info['KIND_CLUB'],
            "MUSIC" =>$info['MUSIC'],
            "DESCR" =>$info['DESCR'],
        ));;

    }

}

