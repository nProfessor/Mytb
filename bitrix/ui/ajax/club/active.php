<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 05.09.12
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");


$stockID = intval($_POST['ID']);
global $USER;

$userRes = new User($USER::GetID());
$props=$userRes->getProps(array("PROPERTY_CLUB"));

$clubID=$props['PROPERTY_CLUB_VALUE'];

$stock = new Stocks();

$stockInfo = $stock->getInfo($stockID);

if($stockInfo['PROPERTY_CLUB_ID_VALUE']==$clubID){
    $stock->active($stockID, $stockInfo['ACTIVE']=="N"?"Y":"N");
    die(json_encode(array("status"=>"ok","class"=>$stockInfo['ACTIVE']=="N"?"Y":"N","text"=>$stockInfo['ACTIVE']=="N"?"Акция показываетя":"Акция не показывается.")));
}


die(json_encode(array("status"=>"error","eee"=>$USER::GetID())));


