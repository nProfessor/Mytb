<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 14.09.12
 * Time: 22:41
 * To change this template use File | Settings | File Templates.
 */
class User
{
    private $user;
    private $props;

    function __construct($userID)
    {
        $rsUser = CUser::GetByID(intval($userID));
        $this->user = $rsUser->Fetch();
    }

    /**
     * Возвращаем информацию о пользователе
     * @return array|bool|mixed
     */
    function getInfo()
    {
        return $this->user;
    }

    /**
     * возвращаем пользовательские свойства
     * @return array|bool|mixed
     */
    function getProps($arSelect = array())
    {
        $ob = CIBlockElement::GetList(Array("SORT" => "ASC"), array("PROPERTY_USER" => $this->getUserId(),
            "IBLOCK_ID" => IB_USER_PROPS), FALSE, FALSE, $arSelect);
        return $ob->Fetch();
    }

    /**
     * Добавляем инфоблок дополнительных свойств пользователя
     * @param $name
     */
    function addPropsBlock($name)
    {
        global $USER;

        $PROP['PROPERTY_USER'] = $this->getUserId();
        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_ID" => IB_USER_PROPS,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $name,
            "ACTIVE" => "Y"
        );

        return CIBlockElement::Add($arLoadProductArray);
    }

    /**
     * Возвращаем ID пользователя
     * @return mixed
     */
    function getUserId()
    {
        return $this->user["ID"];
    }

    /**
     *
     * Добавляем подписку
     *
     * @param $subscribe
     *
     * @return array|bool|mixed
     */
    function setSubscribe($clubID, $type)
    {

        $clubID = intval($clubID);

        $PROP = array();


        $ob = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            array("PROPERTY_USER" => $this->getUserId(), "IBLOCK_ID" => IB_USER_PROPS),
            FALSE,
            FALSE,
            array("ID",
                "PROPERTY_USER",
                "PROPERTY_LINK_NEWS",
                "PROPERTY_LINK_EVENT",
                "PROPERTY_LINK_STOK"))->Fetch();


        foreach ($type as $var) {
            if (!in_array($clubID, $ob["PROPERTY_LINK_NEWS_VALUE"]) && $var == "LINK_NEWS") {
                $ob["PROPERTY_LINK_NEWS_VALUE"][] = $clubID;

                $PROP['LINK_NEWS'] = $ob["PROPERTY_LINK_NEWS_VALUE"];
            }

            if (!in_array($clubID, $ob["PROPERTY_LINK_EVENT_VALUE"]) && $var == "LINK_EVENT") {
                $ob["PROPERTY_LINK_EVENT_VALUE"][] = $clubID;

                $PROP['LINK_EVENT'] = $ob["PROPERTY_LINK_EVENT_VALUE"];
            }

            if (!in_array($clubID, $ob["PROPERTY_LINK_STOK_VALUE"]) && $var == "LINK_STOK") {
                $ob["PROPERTY_LINK_STOK_VALUE"][] = $clubID;

                $PROP['LINK_STOK'] = $ob["PROPERTY_LINK_STOK_VALUE"];
            }
        }

        return CIBlockElement::SetPropertyValuesEx($ob["ID"], IB_USER_PROPS, $PROP);

    }

    /**
     * Сохраняем информацию о рассылках
     * @param $data
     */
    function saveNotice($data)
    {
        $ob = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            array("PROPERTY_USER" => $this->getUserId(), "IBLOCK_ID" => IB_USER_PROPS),
            FALSE,
            FALSE,
            array("ID"))->Fetch();

        return CIBlockElement::SetPropertyValuesEx($ob["ID"], IB_USER_PROPS, array("NOTICE" => serialize($data)));
    }

    /**
     * Подписка
     * @param $data
     */
    function setSubscribeData($data)
    {

        $ob = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            array("PROPERTY_USER" => $this->getUserId(), "IBLOCK_ID" => IB_USER_PROPS),
            FALSE,
            FALSE,
            array("ID",
                "PROPERTY_USER",
                "PROPERTY_LINK_NEWS",
                "PROPERTY_LINK_EVENT",
                "PROPERTY_LINK_STOK"))->Fetch();


        $data['LINK_NEWS'] = count($data['LINK_NEWS'])
            ? $data['LINK_NEWS']
            : array("");
        $data['LINK_EVENT'] = count($data['LINK_EVENT'])
            ? $data['LINK_EVENT']
            : array("");
        $data['LINK_STOK'] = count($data['LINK_STOK'])
            ? $data['LINK_STOK']
            : array("");

        CIBlockElement::SetPropertyValuesEx($ob["ID"], IB_USER_PROPS, array("LINK_NEWS" => $data['LINK_NEWS'],
            "LINK_EVENT" => $data['LINK_EVENT'],
            "LINK_STOK" => $data['LINK_STOK']));


        return TRUE;

    }


    /**
     * Устанавливаем код для подтверждения телефона
     *
     * @param $code
     *
     * @return array|bool|mixed
     */
    function setCodePhoneConfirm($code)
    {
        $ob = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            array("PROPERTY_USER" => $this->user["ID"]),
            FALSE,
            FALSE,
            array("ID"));

        $PropsUser = $ob->Fetch();
        if (!$PropsUser) {
            return FALSE;
        }

        CIBlockElement::SetPropertyValueCode(intval($PropsUser["ID"]), "CODE_PHONE", $code);

        return $ob->Fetch();
    }


    /**
     * Проверяет,
     * @param $code
     *
     * @return array|bool|mixed
     */
    function checkVerificationCode($code)
    {
        $ob = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            array("PROPERTY_USER" => $this->user["ID"]),
            FALSE,
            FALSE,
            array("ID", "PROPERTY_CODE_PHONE"));

        $PropsUser = $ob->Fetch();
        if (!$PropsUser) {
            return FALSE;
        }

        if ($PropsUser['PROPERTY_CODE_PHONE_VALUE'] == $code) {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Сохраняем телефон как мобильный
     * @param $code
     *
     * @return array|bool|mixed
     */
    function saveMobilePhone()
    {

        $userInfo = CUser::GetByID($this->user["ID"])->Fetch();

        $user = new CUser;
        $fields = Array(
            "PERSONAL_MOBILE" => $userInfo["PERSONAL_PHONE"]
        );
        $user->Update($this->user["ID"], $fields);

        return FALSE;
    }



    static function getList($lisstID)
    {
        $filter = Array
        (
            "ID" => implode(" | ", $lisstID),

        );
        $rsUsers = CUser::GetList(($by = "personal_country"), ($order = "desc"), $filter); // выбираем пользователей
        $arUser = array();

        while ($user = $rsUsers->Fetch()) {
            $arUser[$user["ID"]] = $user;
        }
        return $arUser;

    /**
     * Ищем пользователя по ID фейсбука.
     * @param $id
     * @return int  ID пользователя
     */
    static function findFromFB($id){


        $ob = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            array("PROPERTY_ID_FACEBOOK" => intval($id), "IBLOCK_ID"=> IB_USER_PROPS),
            FALSE,
            FALSE,
            array("ID","PROPERTY_USER"))->Fetch();

        return $ob?intval($ob['PROPERTY_USER_VALUE']):false;

    }
    /**
     * Ищем пользователя по ID контакта.
     * @param $id
     * @return int  ID пользователя
     */
    static function findFromVK($id){


        $ob = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            array("PROPERTY_ID_VKONTAKTE" => intval($id), "IBLOCK_ID"=> IB_USER_PROPS),
            FALSE,
            FALSE,
            array("ID","PROPERTY_USER"))->Fetch();

        return $ob?intval($ob['PROPERTY_USER_VALUE']):false;


    }

}

