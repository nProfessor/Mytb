<?php
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("socialservices");
/**
 * Created by JetBrains PhpStorm.
 * User: vchebanenko
 * Date: 25.10.11
 * Time: 11:58
 * To change this template use File | Settings | File Templates.
 */
global $USER;
$USER_ID = $USER::GetID();
$vkontakteApplicationId = CSocServFacebook::GetOption("vkontakte_appid");
;
$vkontakteKey = CSocServFacebook::GetOption("vkontakte_appsecret");

// ID юзера, к которому должно подключаться приложение

if (!empty($_GET['code'])) {

    // вконтакт присылает нам код
    $vkontakteCode = $_GET['code'];
    // получим токен
    $sUrl = "https://api.vkontakte.ru/oauth/access_token?client_id=$vkontakteApplicationId&client_secret=$vkontakteKey&code=$vkontakteCode";

    // создадим объект, содержащий ответ сервера Вконтакте, который приходит в формате JSON
    $oResponce = json_decode(file_get_contents($sUrl));
    //var_dump($oResponce);


    //Пишем токен в базу и в сессию пользователя
    $vk_token = $_SESSION['vk_access_token'] = $oResponce->access_token;
    $vk_userid = $oResponce->user_id;

    // строка запроса к серверу Вконтакте
    //Получаем друзей пользователя
    $fRequest = "https://api.vkontakte.ru/method/friends.get?uid=$vk_userid=&access_token=$vk_token&fields=photo,first_name";

    //$sRequest = "https://api.vkontakte.ru/method/friends.get?uid=$vkontakteUserId=&access_token=$vk_token&fields=photo,first_name";

    // ответ от Вконтакте
    $fResponce = json_decode(file_get_contents($fRequest));


    /*
     * Если пользователя нет в базе или он не авторизован, создаем нового http://vk.com/id37851474
     */
    if (!$USER_ID && $vk_token) {


        $pUrl = "https://api.vk.com/method/getProfiles?uid=$vk_userid&access_token=$vk_token&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,photo_big,contacts"; //&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,photo,contacts
        $pResp = json_decode(file_get_contents($pUrl));

        $vk_res = is_array($pResp->response) ? $pResp->response[0] : false;


        /*
         * получаем местоположение пользователя
         */
        $us_phone = $vk_res->mobile_phone ? $vk_res->mobile_phone : ($vk_res->home_phone ? $vk_res->home_phone : false);
        /*
         * формируем массив данных пользователя
         */
        $vk_ses["user"]["id"] = $vk_res->uid;
        $vk_ses["user"]["first_name"] = $vk_res->first_name;
        $vk_ses["user"]["last_name"] = $vk_res->last_name;

        $_REQUEST["vk_session"] = $vk_ses;



        /*
         * Получаем ID нового пользователя или старого
         */
        $ID = false;
        if ($userFromVK_ID = User::findFromVK($vk_res->uid)) {
            $ID = $userFromVK_ID;
            printAr(1);
        } else { //создаем нового


            $user = new CUser;
            $password = randString(7);
            $arFields = Array(
                "NAME" => $vk_res->first_name,
                "LAST_NAME" => $vk_res->last_name,
                "LOGIN" => "uid" . $vk_res->uid,
                "PERSONAL_BIRTHDAY" => $vk_res->bdate,
                "EXTERNAL_AUTH_ID" => "VKontakte",
                "LID" => "ru",
                "ACTIVE" => "Y",
                "PERSONAL_MOBILE" => $vk_res->home_phone,
                "PASSWORD" => $password,
                "CONFIRM_PASSWORD" => $password,
                "PERSONAL_PHOTO" => CFile::MakeFileArray($vk_res->photo_big)
            );

            $ID = $user->Add($arFields);

        }

        if ($ID) {
            // Тут пользователь должен быть полюбому
            // Авторизуем его
            $userRes = new CUser;
            $userRes->Authorize($ID);

            $ob = CIBlockElement::GetList(
                Array("SORT" => "ASC"),
                array("PROPERTY_USER" => $ID, "IBLOCK_ID" => IB_USER_PROPS),
                FALSE,
                FALSE,
                array("ID", "PROPERTY_USER"))->Fetch();


            if ($ob) {
                CIBlockElement::SetPropertyValuesEx(intval($ob["ID"]), IB_USER_PROPS,
                    array(
                        "TOKEN_VKONTAKTE" => $vk_token,
                        "FRIENDS_VKONTAKTE" => serialize($fResponce->response),
                        "ID_VKONTAKTE" => $vk_userid));


                if ($_GET['r']) {
                    $path = base64_decode($_GET['r']);
                    header('Location: ' . $path);
                    exit;
                }

            }

        }

    }

}
LocalRedirect("/auth/");
if ($_GET['error']) {
    LocalRedirect($_SESSION['SOC_SERV_ATTACH_REDIRECT_PAGE']);
}


//LocalRedirect("/personal/safety/?vk=ok");