<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/tiny_mce/tiny_mce.js");
$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");
$clubInfo=$arResult['arFields'];
$stock=$arResult['STOCK'];
?>
<h2>Редактируем событе  «<?=$stock['NAME']?>» </h2>
    <br/>
<a href="/kabinet-menedzhera/club_event_edite/">Все события</a>
<br/>
<br/>
<form action="" method="post">
    <input type="hidden" id="newsID" value="<?=$stock['ID']?>">
<table class="table">
    <tr>
        <th>Дата начала</th>
        <td>
            <input type="text" class="span10 date_time" value="<?=empty($stock['ACTIVE_FROM'])?date("d.m.Y"):$stock['ACTIVE_FROM'];?>" name="ACTIVE_FROM">
        </td>
    </tr>
    <tr>
        <th>Дата окончания</th>
        <td>
            <input type="text" class="span10 date_time" value="<?=empty($stock['ACTIVE_TO'])?date("d.m.Y"):$stock['ACTIVE_TO'];?>" name="ACTIVE_TO">
        </td>
    </tr>
    <tr>
        <th>Название</th>
        <td><input type="text" class="span10" value="<?=$stock['NAME']?>" name="NAME"></td>
    </tr>
    <tr>
        <th>Описание</th>
        <td><textarea class="span10" rows="15" name="DETAIL_TEXT"><?=$stock['~DETAIL_TEXT']?></textarea></td>
    </tr>
    <tr>
        <th>
            Фотография
        </th>
        <td>
            <div class="thumbnail span4">
                <img src="<?=$stock["PREVIEW_PICTURE"]?>" width="200px">
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
    <button type="submit" class="btn btn-primary pull-right" name="EDITE_STOCK">Сохранить</button>
</div>
</form>