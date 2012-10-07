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

$userID = intval($USER::GetID());

if ($userID <= 0) {
    die();
}

$filename = translate(basename($_FILES['myfile']['name']));

if (!preg_match("#^.*\.(jpg|png|gif)$#i", $filename)) {
    die();
}

$uploaddir = $_SERVER['DOCUMENT_ROOT'] . "/upload/profile/" . $userID . "/";
if (!file_exists($uploaddir)) {
    @mkdir($uploaddir);
}


$uploadfile = $uploaddir . $filename;

move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile);


$BX_image = CFile::MakeFileArray($uploadfile);

$user   = new CUser;
$fields = Array(
    "PERSONAL_PHOTO"              => $BX_image
);
$user->Update($userID, $fields);

echo imgurl("/upload/profile/{$userID}/{$filename}", array("w"=> intval($_POST["w"]),
                                                                                         "h"=> intval($_POST["h"])));