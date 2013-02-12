<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 11.02.13 22:30
 * File name: OnAfterUserAuthorize.php
 */

// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("main", "OnAfterUserAuthorize", "OnAfterUserAuthorizeHandler");


function OnAfterUserAuthorizeHandler($arUser)
{
    global $USER;
    if (in_array(GROUP_CONTEN,$USER->GetUserGroupArray())){
        $_SESSION["ADMIN"]=$USER::GetID();
    }
}
