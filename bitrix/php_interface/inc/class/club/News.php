<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 24.10.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
class News
{
    private $clubID;

    function __construct($clubID)
    {
        if (is_array($clubID)) {
            $ar = array();
            foreach ($clubID as $var) {
                $ar[] = intval($var);
            }
            $this->clubID = $ar;
        } else {
            $this->clubID[0] = $clubID;
        }

    }

    function add($data)
    {

        global $USER;

        $el = new CIBlockElement;


        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID" => IB_SUB_NEWS_ID,
            "PROPERTY_VALUES" => array('CLUB_ID' => $this->clubID),
            "DATE_ACTIVE_FROM" => date("d.m.Y", strtotime($data['ACTIVE_FROM'])),
            "NAME" => trim(strip_tags($data['NAME'])),
            "ACTIVE" => "Y", // активен
            "DETAIL_TEXT" => trim($data['DETAIL_TEXT']),
        );


        return $el->Add($arLoadProductArray);
    }

    /**
     * Возвращаем информацию о новости
     * @param $newsID
     * @return mixed
     */
    function getInfo($newsID, $getNext = false)
    {
        $obj = $this->getList(array("ID" => $newsID));
        return $getNext ? $obj->GetNext() : $obj->Fetch();
    }

    function update($newsID, $data)
    {
        global $USER;

        $el = new CIBlockElement;

        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID" => IB_SUB_NEWS_ID,
            "DATE_ACTIVE_FROM" => $data['ACTIVE_FROM'],
            "NAME" => trim(strip_tags($data['NAME'])),
            "DETAIL_TEXT" => trim($data['DETAIL_TEXT']),
        );


        return $el->update($newsID, $arLoadProductArray);
    }

    /**
     * Воззвращаем список новостей клуба
     * @return mixed
     */
    function getList($filter = array())
    {
        $filter['IBLOCK_ID'] = IB_SUB_NEWS_ID;
        $filter['PROPERTY_CLUB_ID'] = $this->clubID;

        $ob = CIBlockElement::GetList(
            array("ACTIVE_FROM" => "DESC"),
            $filter,
            false,
            FALSE,
            array(
                "ID",
                "NAME",
                "ACTIVE_FROM",
                "DETAIL_TEXT",
                "PREVIEW_PICTURE"
            ));

        return $ob;
    }


}
