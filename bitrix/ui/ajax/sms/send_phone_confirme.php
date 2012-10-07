<?php
/**
 * отправляем пользователю код для подтверждения пароля
 *
 * User: oleg
 * Date: 22.09.12
 * Time: 12:48
 *
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

global $USER;


$phone=str_replace("#[^0-9]*#i","",trim($_POST["phone"]));
$phone=str_replace("#$(8|7).*#i","+7",$phone);


$sms=new Smsc();

$code=generate_password(5,"easy");

$obUser=new User($USER::GetID());

$obUser->setCodePhoneConfirm($code);

$sms->send_sms($phone,"Код для подтверждения телефона: {$code}\n\nС уважением www.mytb.ru");
