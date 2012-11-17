<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/tiny_mce/tiny_mce.js");
$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");
$clubInfo=$arResult['arFields'];
$news=$arResult['NEWS'];
?>
<h2>Редактируем новость  «<?=$news['NAME']?>» </h2>
    <br/>

<form action="" method="post">
    <input type="hidden" id="newsID" value="<?=$news['ID']?>">
<table class="table">
    <tr>
        <th>Дата</th>
        <td>
            <input type="text" class="span10" value="<?=$news['ACTIVE_FROM']?>" name="ACTIVE_FROM">
        </td>
    </tr>
    <tr>
        <th>Название</th>
        <td><input type="text" class="span10" value="<?=$news['NAME']?>" name="NAME"></td>
    </tr>
    <tr>
        <th>Описание</th>
        <td><textarea class="span10" rows="15" name="DETAIL_TEXT"><?=$news['~DETAIL_TEXT']?></textarea></td>
    </tr>
    <tr>
        <th>
            Фотография
        </th>
        <td>
            <div class="thumbnail span4">
                <img src="<?=$news["PREVIEW_PICTURE"]?>" width="200px">
                <br>
                <br>
                <div class="button_upload">
                    <a id="upload_avatar_button" style="width: 86%" class="btn btn-inverse upload_avatar" href="#">Загрузить</a>
                </div>
            </div>
        </td>
    </tr>
</table>
<div class="form-actions">
    <button type="submit" class="btn btn-primary pull-right" name="EDITE_NEWS">Сохранить</button>
</div>
</form>