<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");


global $USER;
$arGroups = $USER->GetUserGroupArray();

if(in_array(GROUP_MANAGER,$arGroups)):

    $APPLICATION->IncludeComponent("club:profile", "", array(
            "USER_ID"=>$USER::GetID()
        ),
        false
    );
    else:
        $APPLICATION->IncludeComponent("profile:home", "", array(
                "USER_ID"=>$USER::GetID()
            ),
            false
        );
    endif;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>