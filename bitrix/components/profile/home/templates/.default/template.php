<?

$userInfo = $arResult['userinfo'];
?>

<div class="right w8">
    <div class="padding_l_25">
        <h1><?=$userInfo['SECOND_NAME']?></h1>
        <?$APPLICATION->IncludeComponent("profile:subscribe.list.clubs", "", array(
            "USER_ID"=>$userInfo['ID']
        ),
        false
    );?>


    <?$APPLICATION->IncludeComponent("profile:subscribe.list.stock", "", array(
        "USER_ID"=>$userInfo['ID']
    ),
    false
);?>
    </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("profile:menu.left", "", array(
        "USER_INFO"=>$userInfo
    ),
    false
);?>
</div>