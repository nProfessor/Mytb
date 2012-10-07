<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 20.08.12
 * Time: 22:32
 * To change this template use File | Settings | File Templates.
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$cow = array( //разрешенные для записи поля
    "NAME"              => "",
    "LAST_NAME"         => "",
    "SECOND_NAME"       => "",
    "PERSONAL_PHONE"    => "",
    "EMAIL"             => "",
    "PERSONAL_BIRTHDAY" => "",
    "PERSONAL_GENDER"   => "",
    "PERSONAL_NOTES"    => "",
);

$mandatory = array("EMAIL" => "","PERSONAL_PHONE"=>"");

$errors = array();
global $USER;
$userID = $USER::GetID();


if (!preg_match("#[0-9]{2}\.[0-9]{2}\.[0-9]{4}#i", $_POST['PERSONAL_BIRTHDAY'])) {
    $errors['PERSONAL_BIRTHDAY'] = "Не правильно введена дата рождения";
}


if (!preg_match("#^[a-z0-9_\.-]+@[a-z0-9_\.-]+[a-z0-9_\.-]{1,4}$#i", $_POST['EMAIL'])) {
    $errors['EMAIL'] = "Не верный формат Email";
}

foreach ($cow as $val => $var) {
    if (isset($_POST[$val])) {
        if (!empty($_POST[$val])) {
            $cow[$val] = $_POST[$val];
        } elseif (isset($mandatory[$val])) {
            $errors[$val] = "Поле обязательно к заполнению";
        }
    } elseif (isset($mandatory[$val])) {
        $errors[$val] = "Поле обязательно к заполнению";
    }
}

if(count($errors)){
    die(json_encode(array("errors"=>$errors)));
}
$user   = new CUser;
$fields = Array(
    "NAME"              => $cow["NAME"],
    "LAST_NAME"         => $cow["LAST_NAME"],
    "EMAIL"             => $cow["EMAIL"],
    "SECOND_NAME"       => $cow["SECOND_NAME"],
    "PERSONAL_PHONE"    => $cow["PERSONAL_PHONE"],
    "PERSONAL_BIRTHDAY" => $cow["PERSONAL_BIRTHDAY"],
    "PERSONAL_GENDER"   => $cow["PERSONAL_GENDER"],
    "PERSONAL_NOTES"    => $cow["PERSONAL_NOTES"],
);
$user->Update($userID, $fields);

die(json_encode(array("message"=>"Информация успешно сохранена")));