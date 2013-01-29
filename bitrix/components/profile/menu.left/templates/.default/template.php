<?
$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");
$userInfo = $arResult['userinfo'];
?>

<div id="ava">
    <img  src="<?=imgurl(empty($userInfo["PERSONAL_PHOTO"]['ORIGINAL_NAME'])?DEFAULT_USER_PHOTO_PATH:"/upload/profile/{$userInfo["ID"]}/{$userInfo["PERSONAL_PHOTO"]['ORIGINAL_NAME']}",array("w"=>200))?>">
</div>
<ul class="menu">
    <li><a href="#" id="upload_avatar_button" class="upload_avatar">Изменить фотографию</a></li>
    <li><a href="/personal/settings/profile-edit/">Редактировать профайл</a></li>
    <li><a href="/personal/settings/subs-settings/">Настроить подписку</a></li>
</ul>