<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 05.09.12
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

define('AJAX_QUERY',strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_SERVER['HTTP_X_REQUESTED_WITH']));

if (!AJAX_QUERY){
    die(json_decode(array("status"=>"errors")));
}

CModule::IncludeModule("iblock");



$eventID = intval($_POST['ID']);
global $USER;

$userRes = new User($USER::GetID());
$props=$userRes->getProps(array("PROPERTY_CLUB"));

$clubID=$props['PROPERTY_CLUB_VALUE'];

$event = new Event();

$eventInfo = $event->getInfo($eventID);

if($eventInfo['PROPERTY_CLUB_ID_VALUE']==$clubID){
    $event->published($eventID);
    die(json_encode(array("status"=>"ok","class"=>$eventInfo['ACTIVE']=="N"?"Y":"N","text"=>$eventInfo['ACTIVE']=="N"?"Акция показываетя":"Акция не показывается.")));
}


die(json_encode(array("status"=>"error")));


