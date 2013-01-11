<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 20.12.12 18:54
 * File name: getinfo.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");


$club_id = intval($_POST["club_id"]);
$clubRes = new Club($club_id);

$list = array();

$el = new CIBlockElement;
$el->Update($club_id, array("ACTIVE" => "Y"));
CIBlockElement::SetPropertyValues($this->clubID,IB_CLUB_ID,array("LIST"=>array()));