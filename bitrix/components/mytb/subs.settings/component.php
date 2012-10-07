<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

global $USER;

$obUser = new User($USER::GetID());

if (isset($_POST['save'])) {
    $obUser->saveNotice(array(
                             "stock"=> array(
                                 "sms"  => intval($_POST['stock']['sms']),
                                 "email"=> intval($_POST['stock']['email']),
                                 "count"=> intval($_POST['stock']['count'])
                             ),
                             "news" => array(
                                 "sms"  => intval($_POST['news']['sms']),
                                 "email"=> intval($_POST['news']['email']),
                                 "count"=> intval($_POST['news']['count'])
                             ),
                             "event"=> array(
                                 "sms"  => intval($_POST['event']['sms']),
                                 "email"=> intval($_POST['event']['email']),
                                 "count"=> intval($_POST['event']['count'])
                             )
                        ));
}


$arObj = $obUser->getProps(array("PROPERTY_NOTICE"));

$arResult["NOTICE"] = unserialize($arObj["PROPERTY_NOTICE_VALUE"]);



$this->IncludeComponentTemplate();