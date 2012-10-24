<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 14.07.12
 * Time: 16:13
 * To change this template use File | Settings | File Templates.
 */

function agent_sort()
{


    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    CModule::IncludeModule("iblock");

    $arSelect = Array(
        "ID",
        "NAME",
        "SORT",
        "PREVIEW_TEXT",
        "PREVIEW_PICTURE",
        'PROPERTY_RATING',
        'PROPERTY_METRO',
        'PROPERTY_TIME_WORKING',
        'PROPERTY_PRICE_COCKTAIL',
        'PROPERTY_CARDS',
    );
    $arFilter = Array(
        "IBLOCK_ID" => IB_CLUB_ID,
        //    "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
    );


    $ob = CIBlockElement::GetList(
        array("IBLOCK_ID" => "DESC"),
        array("IBLOCK_ID"               => IB_SUB_STOCK_ID,
              ">DATE_ACTIVE_TO"         => date("d.m.Y h:i:s"),
              "ACTIVE"=>"Y",
        ),
        array("PROPERTY_CLUB_ID"),
        FALSE,
        array(
             "PROPERTY_CLUB_ID"
        ));

    $ar = array();
    while ($row = $ob->Fetch()) {
        $ar[$row['PROPERTY_CLUB_ID_VALUE']] = intval($row["CNT"]);
    }


    $arFields = array();
    $res      = CIBlockElement::GetList(Array("PREVIEW_PICTURE" => "DESC"), $arFilter, FALSE, FALSE, $arSelect);
    while ($ob = $res->GetNext()) {

        if (!empty($ob["PREVIEW_PICTURE"])) { // если есть картинка
            $arFields[$ob["ID"]] += 30;
        }

        if (!empty($ob["PREVIEW_TEXT"])) { // если есть описание
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["NAME"])) { // если есть название
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["PROPERTY_METRO_VALUE"])) { // если есть название метро
            $arFields[$ob["ID"]] += 10;
        }

        if ($ob["PROPERTY_RATING_VALUE"] > 0) { // если есть рейтинг
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["PROPERTY_TIME_WORKING_VALUE"])) { // если есть время работы
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["PROPERTY_PRICE_COCKTAIL_VALUE"])) { // если есть стоимость коктеля
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["PROPERTY_CARDS_VALUE"])) { // если есть есть информация о приеме карточек
            $arFields[$ob["ID"]] += 10;
        }

        if (!empty($ob["PROPERTY_PLAN_VALUE"])) { // если план клуба
            //            $arFields[$ob["ID"]] += 50; TODO пока план есть только у тестовых объектов
        }


        if (!empty($ob["PROPERTY_ADDRESS_VALUE"])) { // если
            $arFields[$ob["ID"]] += 10;
        }


        if (!empty($ob["PROPERTY_PHONE_VALUE"])) { // если
            $arFields[$ob["ID"]] += 15;
        }

        if (isset($ar[$ob['ID']])) {
            $arFields[$ob["ID"]] += 300 * $ar[$ob['ID']];
        }


    }


    $el = new CIBlockElement;
    foreach ($arFields as $val => $var) {
        $val                = (int)$val;
        $arLoadProductArray = Array(
            "SORT" => $var, // элемент изменен текущим пользователем
        );


        $res = $el->Update($val, $arLoadProductArray);
    }

    return "agent_sort();";
}