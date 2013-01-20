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


    // Выбираем все акции по кторым нужно разослать уведомления
    $resStoskListNoSend=$DB->Query("SELECT * FROM a_send_notice");

    /**
     * Собираем список событий по которым отправляем уведомления
     * И список пользователей
     */
    while($row=$resStoskListNoSend->Fetch()){
        $stokAll=array_merge((array)$stokAll,(array)explode("|",$row['EVENT_ID']));
        $stokAllUser[$row['USER_ID']]=$row['USER_ID'];
    }

    if(!count($stokAll)){
        return "send_message_event();";
    }

    // собираем все акции всех клубов которые активны на данный момент и которые нужно разослать
    // TODO нужно еще и события рассылать
    $resStock = CIBlockElement::GetList(Array("SORT" => "DESC"), array("IBLOCK_ID" => IB_SUB_STOCK_ID, ">=DATE_ACTIVE_TO" => date("d.m.Y"),"ID"=>$stokAll), false, false, array("ID", "PROPERTY_CLUB_ID"));

    $idl=array();
    while ($obj = $resStock->Fetch()) {
        $clubID = intval($obj['PROPERTY_CLUB_ID_VALUE']);
        $clubListID[$clubID] = $clubID;
        $idl[]=$obj['ID'];
    }


    // Собираем всех пользователей которые подписанны на действующие акции и которым нужно разослать сегодня
    $res = CIBlockElement::GetList(Array("SORT" => "DESC"), array("IBLOCK_ID" => IB_USER_PROPS, "PROPERTY_LINK_STOK" => $clubListID,"PROPERTY_USER"=>$stokAllUser), false, false, array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_NEWS", "PROPERTY_LINK_EVENT", "PROPERTY_NOTICE", "PROPERTY_DATE_SEND"));

    $day_now=date('w');
    $day_now=$day_now==0?7:$day_now;

    while ($obj = $res->Fetch()) {
        $PROPERTY_NOTICE_VALUE = unserialize($obj['PROPERTY_NOTICE_VALUE']);

        $day_send = $PROPERTY_NOTICE_VALUE['day'];
        $metod = intval($PROPERTY_NOTICE_VALUE['stock']["count"]);



        if (in_array($day_now,$day_send)) { // если должны отправить сегодня, то отправляем уведомления

            $usersProps[] = array(
                "MEOD" =>$metod,
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
            if (in_array("sms",$var["MEOD"])) {
                $user = $arUser[$var['USER_ID']];
                $sms->send_sms($user["PERSONAL_PHONE"], "Новые акции в заведениях, на которые вы подписаны!\nС уважением MyTb.ru");
                $send=true;
            }


            if (in_array("email",$var["MEOD"])) {
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