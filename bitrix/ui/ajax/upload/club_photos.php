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
CModule::IncludeModule("mytb");


global $USER;

$userID = intval($USER::GetID());
$clubID = Club::getClubID($userID);



if ($userID <= 0) {
    die();
}


$filename = translate(basename($_FILES['qqfile']['name']));

if (!preg_match("#^.*\.(jpg|png|gif|jpeg)$#i", $filename)) {
    die();
}

$uploaddir = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/" . $userID . "/";
if (!file_exists($uploaddir)) {
    @mkdir($uploaddir);
}


$uploadfile = $uploaddir . $filename;


move_uploaded_file($_FILES['qqfile']['tmp_name'], $uploadfile);


$BX_image = CFile::MakeFileArray($uploadfile);
$fid = CFile::SaveFile($BX_image, "club_photo");
$arFile = CFile::GetFileArray($fid);


$el = new MyTbCore;
$res = $el->Add(array(
                    "CLUB_ID"=>$clubID,
                    "PATH"=>$arFile['SRC'],
                    "FILE_ID"=>$fid),
                    "club_photo");







echo json_encode(array(
    "success"=>true,
//    "error"=>"Никакой ошибки нет",
    "src"=>imgurl($arFile['SRC'], array("w"=> 100, "h"=> 100)),
    "src2"=>imgurl($arFile['SRC'], array("w"=> 800, "h"=> 600)),
));