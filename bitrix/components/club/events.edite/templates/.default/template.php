<?

$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");
$APPLICATION->AddHeadScript("/jslibs/tiny_mce/tiny_mce.js");
//$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");



$clubInfo=$arResult['club'];
$event=$arResult["EVENT"];

$event['ACTIVE_FROM']=$event['ACTIVE_FROM']=="01.01.1970"?"":$event['ACTIVE_FROM'];
$event['ACTIVE_TO']=$event['ACTIVE_TO']=="01.01.1970"?"":$event['ACTIVE_TO'];
?>

<style type="text/css">
    @import '/jslibs/jqueryui/css/ui-lightness/jquery-ui-1.8.21.custom.css'
</style>
<div class="right w8">
    <div class="padding_lr_10">
        <b>Редактируем:</b> <a href="/personal/club/list-events/">Все события</a> >> <?=$event['NAME']?>
<br/>
<br/>
<form action="" method="post">
    <input type="hidden" id="eventID" value="<?=$event['ID']?>">
<table class="table">
    <tr>
        <th>Дата начала</th>
        <td>
            <input type="text" class="w6 date_time" value="<?=empty($event['ACTIVE_FROM'])?date("d.m.Y"):$event['ACTIVE_FROM'];?>" name="ACTIVE_FROM">
        </td>
    </tr>
    <tr>
        <th>Дата окончания</th>
        <td>
            <input type="text" class="w6 date_time" value="<?=empty($event['ACTIVE_TO'])?date("d.m.Y"):$event['ACTIVE_TO'];?>" name="ACTIVE_TO">
        </td>
    </tr>
    <tr>
        <th>Название</th>
        <td><input type="text" class="w6" value="<?=$event['NAME']?>" name="NAME"></td>
    </tr>
    <tr>
        <th>Описание</th>
        <td><textarea class="w6" rows="15" name="DETAIL_TEXT"><?=$event['DETAIL_TEXT']?></textarea></td>
    </tr>
    <tr>
        <th>
            Фотография
        </th>
        <td>
            <div class="img_stock">
                <img src="<?=$event["DETAIL_PICTURE"]?>" >
                <br>
                <br>
                <div class="button_upload_2">
                    <a id="upload_avatar_button_2" style="width: 86%" class="btn btn-inverse upload_avatar_2" href="#">Загрузить</a>
                </div>
            </div>
        </td>
    </tr>
</table>
<div class="form-actions">

    <button type="submit" class="btn btn-primary" name="EDITE_STOCK">Сохранить</button>
    <?if($event["PROPERTY_PUBLIC_ENUM_ID"]!=PROP_EVENT_PUBLIC):?>
    <a hfer="#" data-id="<?=$event["ID"]?>" class="btn public">Опубликовать</a>
    <?endif;?>
</div>
</form>
    </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("club:menu.left", "", array(
        "CLUB_ID" => $clubInfo['ID'],
    ),
    false
);?>
</div>