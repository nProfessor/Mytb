<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 11.02.13 22:34
 * File name: OnAfterUserLogout.php
 */

AddEventHandler("main", "OnAfterUserLogout", "OnAfterUserLogoutHandler");


function OnAfterUserLogoutHandler($arParams)
{
    unset($_SESSION["ADMIN"]);
}
