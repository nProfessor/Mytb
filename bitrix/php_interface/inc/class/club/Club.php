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
     * ВОзвращаем пользователя который управляет этим клубом.
     * @return int
     */
    function getUser()
    {
        $ob = CIBlockElement::GetList(Array(), array("PROPERTY_CLUB" => $this->clubID,
            "IBLOCK_ID" => IB_USER_PROPS), FALSE, FALSE, array("PROPERTY_USER"))->Fetch();

        die(json_encode(array("clubID" => $this->clubID, "obj" => $ob)));
        return intval($ob['PROPERTY_USER_VALUE']);
    }

    /**
     * Получаем колличество подписчиков на акции у завадения
     */
    function getCountSubStocks()
    {

        $arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_LINK_STOK");
        $arFilter = Array("IBLOCK_ID" => IB_CLUB_USER_ID, "ACTIVE" => "Y", "PROPERTY_LINK_STOK" => $this->clubID);
        $res = CIBlockElement::GetList(Array(), $arFilter, array("IBLOCK_ID"), FALSE, $arSelect);
        $arField = $res->Fetch();

        return intval($arField["CNT"]);
    }


    /**
     * Получаем колличество подписчиков на события у завадения
     */
    function getCountSubEvent()
    {

        $arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_LINK_STOK");
        $arFilter = Array("IBLOCK_ID" => IB_CLUB_USER_ID, "ACTIVE" => "Y", "PROPERTY_LINK_EVENT" => $this->clubID);
        $res = CIBlockElement::GetList(Array(), $arFilter, array("IBLOCK_ID"), FALSE, $arSelect);
        $arField = $res->Fetch();

        return intval($arField["CNT"]);
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
    static function getListHaveStocks()
    {
        return Stocks::getListHaveStocks();
    }

    /**
     * Возвращаем ID клубов у которых есть акции
     */
    static function getListHaveEvent()
    {
        return Event::getListHaveEvents();
    }

    /**
     * Возвращаем ID клубов у которых есть новости
     */
    static function getListHaveNews()
    {
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
                "PROPERTY_PRICECOUPON",
                "TAGS"
            ));
        return $ob;
    }

    function getListEvent()
    {
        $ob = CIBlockElement::GetList(
            array("DATE_ACTIVE_TO" => "ASC"),
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

    static function getAddressAll($clubID)
    {

        $res = MyTbCore::GetList(array(), array("CLUB_ID" => $clubID), false, false, array("*"), "address");

        $result = array();
        while ($row = $res->Fetch()) {
            $row["PHONE"] = unserialize($row["PHONE"]);
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Обновляем логотип клуба
     * @param $logo
     */
    function updateLogo($logo)
    {
        $el = new CIBlockElement;
        $el->Update($this->clubID, array("PREVIEW_PICTURE" => $logo));

    }

    /**
     * Обнавляем информацию о клубе
     * @param $info
     */
    function update($info)
    {
        $list = array();

        $el = new CIBlockElement;

        $el->Update($this->clubID, array("NAME" => $info['NAME'], "DETAIL_TEXT" => $info['DETAIL_TEXT']));

        return CIBlockElement::SetPropertyValues($this->clubID, IB_CLUB_ID, array(
            "SITE" => $info['SITE'],
            "EMAIL_MANAGER" => $info['EMAIL_MANAGER'],
            "AVERAGE_CHECK" => $info['AVERAGE_CHECK'],
            "TIME_WORKING" => $info['TIME_WORKING'],
            "TYPE_FACILITY" => $info['TYPE_FACILITY'],
            "MUSIC" => $info['MUSIC']
        ));
        ;

    }

    /**
     * Возвращаем список фотографий заведений
     */
    function getPhotoList()
    {
        $ob = MyTbCore::GetList(
            array(),
            array("CLUB_ID" => $this->clubID),
            FALSE,
            false,
            array(), "club_photo");
        $arResult = array();
        while ($row = $ob->Fetch()) {
            $arResult[] = $row;
        }

        return $arResult;
    }

    /**
     * Возвращаем список популярных клубов
     */
    function getListPopular()
    {
        $clubListID=array();
        $clubListData=array();


        $obj=MyTbCore::GetList(array(),array(),false, array("nTopCount"=>21),array(),"popular");


         while ($row = $obj->Fetch()) {
             $clubListID[]=$row['CLUB_ID'];
             $clubListData[$row['CLUB_ID']]['SORT']=$row['SORT'];
             $clubListData[$row['CLUB_ID']]['SUBS']=$row['SUBS'];
         }

        if(count($clubListID)){
        $ob = CIBlockElement::GetList(array(),
            array("ID" => $clubListID, "IBLOCK_ID" => IB_CLUB_ID, "ACTIVE" => "Y"),
            false,  array("nTopCount"=>21),
            array("ID",
                "DETAIL_TEXT",
                "NAME",
                "DATE_ACTIVE_FROM",
                "PROPERTY_ADDRESS",
                "PROPERTY_TIME_WORKING",
                "PROPERTY_PRICE_COCKTAIL",
                "PROPERTY_PHONE",
                "PROPERTY_RATING",
                "PROPERTY_MUSIC",
                "PROPERTY_FACE",
                "PROPERTY_DRESS_CODE",
                "PROPERTY_SITE",
                "PROPERTY_METRO",
                "PROPERTY_PLAN",
                "PREVIEW_PICTURE",
                "PROPERTY_TYPE_FACILITY"
            ));
        }


        while ($row = $ob->Fetch()) {
            $row["SORT_RATING"]=$clubListData[$row["ID"]]['SORT'];
            $row["SUBS"]=$clubListData[$row["ID"]]['SUBS'];

            $result["s_".$row["ID"]]=$row;
        }

        uasort($result,function($a,$b){

            if($a['SORT_RATING']<$b['SORT_RATING'])
                return true;
            return false;
        });
        return $result;
    }

    /**
     * Возвращаем список акций для менеджера
     * @return CIBlockResult|string
     */
    function getListStokManager($active, $arNavStartParams = false)
    {

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            array("PROPERTY_CLUB_ID" => $this->clubID,
                "IBLOCK_ID" => IB_SUB_STOCK_ID,
                "ACTIVE" => $active),
            FALSE,
            $arNavStartParams,
            array(
                "ID",
                "NAME",
                "ACTIVE",
                "DATE_ACTIVE_FROM",
                "ACTIVE_FROM",
                "DATE_ACTIVE_TO",
                "ACTIVE_TO",
                "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_CLUB_ID",
                "DATE_ACTIVE_TO",
                "PROPERTY_URL",
                "PROPERTY_CLUB_ID",
                "PROPERTY_PRICECOUPON",
                "PROPERTY_PUBLIC",
                "TAGS"
            ));
        return $ob;
    }

    /**
     * Обновляем список популярных клубов
     *
     * Популярность - количество подписчиков(большой приоритет) и колличество лайков в Vkontakt
     */
    function UpdatePopular()
    {

        MyTbCore::DeleteWhere("popular");

        $ob = CIBlockElement::GetList(
            Array(),
            array("IBLOCK_ID" => IB_USER_PROPS,
                array(
                    "LOGIC" => "OR",
                    "!PROPERTY_LINK_NEWS" => false,
                    "!PROPERTY_LINK_EVENT" => false,
                    "!PROPERTY_LINK_STOK" => false,
                )
            ),
            FALSE,
            FALSE,
            array("ID",
                "PROPERTY_USER",
                "PROPERTY_LINK_NEWS",
                "PROPERTY_LINK_EVENT",
                "PROPERTY_LINK_STOK"));

        $ClubList = array();
        while ($row = $ob->Fetch()) {
            foreach ($row['PROPERTY_LINK_NEWS_VALUE'] as $var) {
                $ClubList[$var]["SORT"] += 10;
                $ClubList[$var]["SUBS"]++;
            }
            foreach ($row['PROPERTY_LINK_EVENT_VALUE'] as $var) {
                $ClubList[$var]["SORT"] += 10;
                $ClubList[$var]["SUBS"]++;
            }
            foreach ($row['PROPERTY_LINK_STOK_VALUE'] as $var) {
                $ClubList[$var]["SORT"] += 10;
                $ClubList[$var]["SUBS"]++;
            }
        }


        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            array("IBLOCK_ID" => IB_CLUB_ID, "ACTIVE" => "Y", ">PROPERTY_RATING" => 0),
            FALSE,
            FALSE,
            array(
                "ID",
                "PROPERTY_RATING",
            ));
        while ($row = $ob->Fetch()) {
            $ClubList[$row["ID"]]["SORT"] += intval($row['PROPERTY_RATING_VALUE']);
        }


        if (count($ClubList)) {
            foreach ($ClubList as $clubID => $popular) {
                MyTbCore::Add(array("CLUB_ID" => $clubID, "SORT" => $popular['SORT'], "SUBS" => $popular['SUBS']), "popular");
            }
        }

        return false;


    }


    /**
     * Возвращаем список событий для менеджера
     * @return CIBlockResult|string
     */
    function getListEventManager($active, $arNavStartParams = false)
    {

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            array("PROPERTY_CLUB_ID" => $this->clubID,
                "IBLOCK_ID" => IB_SUB_EVENT_ID,
                "ACTIVE" => $active),
            FALSE,
            $arNavStartParams,
            array(
                "ID",
                "NAME",
                "ACTIVE",
                "DATE_ACTIVE_FROM",
                "ACTIVE_FROM",
                "DATE_ACTIVE_TO",
                "ACTIVE_TO",
                "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_CLUB_ID",
                "DATE_ACTIVE_TO",
                "PROPERTY_URL",
                "PROPERTY_CLUB_ID",
                "PROPERTY_PRICECOUPON",
                "PROPERTY_PUBLIC",
                "TAGS"
            ));
        return $ob;
    }
}

