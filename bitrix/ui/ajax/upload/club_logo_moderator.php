<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.09.12
 * Time: 21:15
 * To change this template use File | Settings | File Templates.
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");


global $USER;

$clubID = intval($_POST['club_id']);

if ($clubID <= 0) {
    die();
}


$filename = translate(basename($_FILES['myfile']['name']));

if (!preg_match("#^.*\.(jpg|png|gif)$#i", $filename)) {
    die();
}

$uploaddir = $_SERVER['DOCUMENT_ROOT'] . "upload/club/" . $clubID . "/";


if (!file_exists($uploaddir)) {
    @mkdir($uploaddir,"0777");
}


$uploadfile = $uploaddir . $filename;


move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile);


$BX_image = CFile::MakeFileArray($uploadfile);

$el = new CIBlockElement;
$arLoadProductArray = Array(
    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
    "PREVIEW_PICTURE" => $BX_image
);


$res = $el->Update($clubID, $arLoadProductArray);

$club =new Club($clubID);
$pic=$club->getInfo(array("arSelect"=>array("PREVIEW_PICTURE")));
$arFile = CFile::GetFileArray($pic["PREVIEW_PICTURE"]);


echo imgurl($arFile["SRC"], array("w"=> intval($_POST["w"]),"h"=> intval($_POST["h"])));