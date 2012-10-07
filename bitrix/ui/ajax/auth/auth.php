<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 22.09.12
 * Time: 17:26
 * To change this template use File | Settings | File Templates.
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


$email    = trim($_POST["email"]);
$reg      = intval($_POST["reg"]);
$password = trim($_POST["password"]);

if ($email == "" || $password == "") {
    die(json_encode(array("status"=> "errors", "message"=> "Пустой логин или пароль", "input"=> array("email", "password"))));
}

global $USER;
$user = new CUser;
$arAuthResult = $user->Login($email, $password, "Y");

if ($arAuthResult['TYPE'] != "ERROR") {
    die(json_encode(array("status"=> "ok")));
} elseif ($reg == 1) {

  if (CUser::GetByLogin($email)->Fetch() == NULL) {

        $arFields = Array(
            "EMAIL"             => $email,
            "LOGIN"             => $email,
            "ACTIVE"            => "Y",
            "PASSWORD"          => $password,
            "CONFIRM_PASSWORD"  => $password
        );

        $ID = $user->Add($arFields);
        if (intval($ID) > 0) {
            if ($user->Authorize($ID)) {
                die(json_encode(array("status"=> "ok")));
            } else {
                die(json_encode(array("status"=> "ok", "message"=> "Не получилось авторизировать")));
            }
        } else {
            die(json_encode(array("status"=> "errors", "message"=> $user->LAST_ERROR)));
        }
    } else {
        die(json_encode(array("status"=> "errors", "message"=> "Логин уже занят")));
    }

} else {
    die(json_encode(array("status"=> "errors", "message"=> "Неверный пароль", "input"=> array("email", "password"))));
}





