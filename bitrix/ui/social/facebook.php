<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


CModule::IncludeModule("socialservices");

global $USER;
$USER_ID = $USER::GetID();
$FBAppId = CSocServFacebook::GetOption("facebook_appid");
$FBKey = CSocServFacebook::GetOption("facebook_appsecret");
$fb_token = false;

//var_dump($_SERVER);exit;

if (!empty($_GET['code'])) {


    $FBCode = $_GET['code'];

    if (!$fb_token) {
        // получим токен
        $sUrl = "https://graph.facebook.com/oauth/access_token?client_id=$FBAppId&client_secret=$FBKey&code=$FBCode&redirect_uri=" . urlencode("http://" . MAINSERVER . "/auth/facebook/".$_GET["r"]);
        $oResponce = file_get_contents($sUrl);

        $arToken = array();
        parse_str($oResponce, $arToken);
        //Пишем токен в базу
        $fb_token = $_SESSION['fb_access_token'] = $fb_token ? $fb_token : $arToken['access_token'];
    }

    //Получаем Id пользователя Фэйсбука
    $uUrl = "https://graph.facebook.com/me?access_token=$fb_token";

    $uResponse = file_get_contents($uUrl);

    $UserData = json_decode($uResponse);

    $UserDataSerialized = serialize($UserData);
    $arFileds = (array)unserialize($UserDataSerialized);

//    gender] => male
//    graph.facebook.com/tabotaOG/picture?type=large
//    printAr($arFileds);
    if (!$USER_ID) {
        $rsUser = CUser::GetByLogin($arFileds["email"]);
        if ($arUser = $rsUser->Fetch()) {
            //Если найден, то вот он наш пользователь авторизуем, если нет, то ищем по ID Facebook
            $ID = $arUser["ID"];

        } else {
            //Пользователь с логином не найден!';
            if ($userFromFB_ID = User::findFromFB($arFileds["id"])) { // ищем по ID Facebook

                $ID = $userFromFB_ID;
            } else { //тогда создаем нового пользователя

                $user = new CUser;
                $password = randString(7);
                $arFields = Array(
                    "NAME" => $arFileds["first_name"],
                    "LAST_NAME" => $arFileds["last_name"],
                    "EMAIL" => $arFileds["email"],
                    "LOGIN" => $arFileds["email"],
                    "PERSONAL_BIRTHDAY"=>str_replace("/",".",$arFileds["birthday"]),
                    "LID" => "ru",
                    "ACTIVE" => "Y",
                    "PASSWORD" => $password,
                    "CONFIRM_PASSWORD" => $password,
                    "PERSONAL_PHOTO"=>CFile::MakeFileArray("http://graph.facebook.com/".$arFileds["id"]."/picture?type=large")
                );

                $ID = $user->Add($arFields);

            }
        }

        // Тут пользователь должен быть полюбому
        // Авторизуем его
        $userRes = new CUser;
        $userRes->Authorize($ID);
    }



    //Получаем друзей пользователя Фэйсбука
    $fUrl = "https://graph.facebook.com/me/friends?access_token=$fb_token";
    $fResponse = file_get_contents($fUrl);
    $FriendsData = json_decode($fResponse);
    $FriendsDataSerialized = serialize($FriendsData);


    $ob = CIBlockElement::GetList(
        Array("SORT" => "ASC"),
        array("PROPERTY_USER" => $USER::GetId(), "IBLOCK_ID" => IB_USER_PROPS),
        FALSE,
        FALSE,
        array("ID",
            "PROPERTY_USER",
            "PROPERTY_LINK_NEWS",
            "PROPERTY_LINK_EVENT",
            "PROPERTY_LINK_STOK"))->Fetch();


    if ($ob) {
        CIBlockElement::SetPropertyValuesEx(intval($ob["ID"]), IB_USER_PROPS,
            array(
                "TOKEN_FACEBOOK" => $fb_token,
                "FRIENDS_FACEBOOK" => $FriendsDataSerialized,
                "ID_FACEBOOK" => $arFileds["id"]));



        if ($_GET['r']) {
            $path = base64_decode($_GET['r']);
            header('Location: ' . $path);
            exit;
        }
    }


    LocalRedirect("/personal/");
}

if ($_GET['error']) {
    LocalRedirect($_SESSION['SOC_SERV_ATTACH_REDIRECT_PAGE']);
}

//LocalRedirect("/personal/safety/?vk=ok");
