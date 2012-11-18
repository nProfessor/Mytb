<?php
/**
 * Отправляем уведомления пользователям об акциях
 * User: Tabota Oleg (sForge.ru)
 * Date: 18.11.12 16:20
 * File name: send_message_event.php
 */


function send_message_event()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    global $DB;


    $clubListID = array();
    $userListID = array();

    $stokAll=array();
    $stokAllUser=array();


    $resStoskListNoSend=$DB->Query("SELECT * FROM a_send_notice");

    while($row=$resStoskListNoSend->Fetch()){
        $stokAll=array_merge((array)$stokAll,(array)explode("|",$row['EVENT_ID']));
        $stokAllUser[]=$row['USER_ID'];
    }


    if(!count($stokAll)){
        return "send_message_event();";
    }

    // собираем все действующие акции всех клубов
    $resStock = CIBlockElement::GetList(Array("SORT" => "DESC"), array("IBLOCK_ID" => IB_SUB_STOCK_ID, ">=DATE_ACTIVE_TO" => date("d.m.Y"),"ID"=>$stokAll), false, false, array("ID", "PROPERTY_CLUB_ID"));


    $idl=array();
    while ($obj = $resStock->Fetch()) {
        $clubID = intval($obj['PROPERTY_CLUB_ID_VALUE']);
        $clubListID[$clubID] = $clubID;
        $idl[]=$obj['ID'];
    }


    // Собираем всех пользователей которые подписанны на действующие акции
    $res = CIBlockElement::GetList(Array("SORT" => "DESC"), array("IBLOCK_ID" => IB_USER_PROPS, "PROPERTY_LINK_STOK" => $clubListID,"PROPERTY_USER"=>$stokAllUser), false, false, array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_NEWS", "PROPERTY_LINK_EVENT", "PROPERTY_NOTICE", "PROPERTY_DATE_SEND"));

    while ($obj = $res->Fetch()) {
        $PROPERTY_NOTICE_VALUE = unserialize($obj['PROPERTY_NOTICE_VALUE']);

        $date_send = strtotime($obj['PROPERTY_DATE_SEND_VALUE']);
        $count = intval($PROPERTY_NOTICE_VALUE['stock']["count"]);
        $date_send_period = strtotime("-{$count} day");


        if ($date_send_period >= $date_send ) { //то отправляем уведомления

            $usersProps[] = array(
                "SMS" => intval($PROPERTY_NOTICE_VALUE['stock']["sms"]),
                "EMAIL" => intval($PROPERTY_NOTICE_VALUE['stock']["email"]),
                "USER_ID" => $obj['PROPERTY_USER_VALUE'],
                "PROPS_ID" => $obj['ID'],
            );

            $userListID[] = intval($obj['PROPERTY_USER_VALUE']);
        }
    }

    if (count($userListID)) {
        $arUser = User::getList($userListID);
        $sms = new Smsc();
        foreach ($usersProps as $var) { //отсылаем всем уведомления
            $send=false;
            if ($var["SMS"] == 1) {
                $user = $arUser[$var['USER_ID']];
                $sms->send_sms($user["PERSONAL_PHONE"], "Новые акции в заведениях, на которые вы подписаны!\nС уважением mytb.ru");
                $send=true;
            }


            if ($var["EMAIL"] == 1) {
                $user = $arUser[$var['USER_ID']];
                $arEventFields = array(
                    "NAME" => $user["NAME"] . " " . $user["LAST_NAME"],
                    "EMAIL" => $user["EMAIL"]
                );
                CEvent::Send("SEND_MESSGE_EVENT", "s1", $arEventFields);

                $send=true;
            }

            if($send){
                CIBlockElement::SetPropertyValueCode(intval($var["PROPS_ID"]), "DATE_SEND", date("d.m.Y H:i:s") );
                $DB->Query("DELETE FROM a_send_notice WHERE USER_ID='{$var['USER_ID']}'");
            }

        }
    }


    return "send_message_event();";
}