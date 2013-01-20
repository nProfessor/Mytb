<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 23.06.12
 * Time: 18:57
 * To change this template use File | Settings | File Templates.
 */
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/tiny_mce/tiny_mce.js");
$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");
$clubInfo=$arResult['arFields'];

?>
<h2>Редактируем информацию о «<?=$clubInfo["NAME"]?>»</h2>
<br>
<form class="form-horizontal" method="post">
    <div class="control-group">
        <label class="control-label">Назание</label>
        <div class="controls">
            <input type="text" placeholder="Название заведения" value="<?=$clubInfo["NAME"]?>"  name="CLUB[NAME]" class="span5">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Описание</label>
        <div class="controls">
            <textarea rows="14" class="span5" name="CLUB[DETAIL_TEXT]"><?=$clubInfo["~DETAIL_TEXT"]?></textarea>
        </div>
    </div>


    <div class="control-group">
        <label class="control-label">Логотип</label>
        <div class="controls">
            <div class="thumbnail span4">
                <img src="<?=$clubInfo["PREVIEW_PICTURE"]?>" width="200px">
                <br>
                <br>
                <div class="button_upload">
                    <a id="upload_avatar_button" style="width: 86%" class="btn btn-inverse upload_avatar" href="#">Изменить</a>
                </div>
            </div>

        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Сайт</label>
        <div class="controls">
            <input type="text" placeholder="Сайт" value="<?=$clubInfo["PROPERTY_SITE_VALUE"]?>" class="span5" name="CLUB[SITE]">
        </div>
    </div>


    <div class="control-group">
        <label class="control-label">Время работы</label>
        <div class="controls">
            <textarea rows="6" class="span5"  name="CLUB[TIME_WORKING]"><?=$clubInfo["PROPERTY_TIME_WORKING_VALUE"]?></textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Цена коктейля</label>
        <div class="controls">
            <input type="text" placeholder="Цена коктейля" value="<?=$clubInfo['PROPERTY_PRICE_COCKTAIL_VALUE']?>"  name="CLUB[PRICE_COCKTAIL]" class="span4"> руб.
        </div>
    </div>
    <?if(0):?>
    <div class="control-group">
        <label class="control-label">Средний чек</label>
        <div class="controls">
            <input type="text" placeholder="Средний чек" value="<?=$clubInfo['PROPERTY_AVERAGE_TICKET_VALUE']?>"   name="CLUB[AVERAGE_TICKET]" class="span5"> руб.
        </div>
    </div>
    <?endif;?>
    <div class="control-group">
        <label class="control-label">Телефон</label>
        <div class="controls">
            <input type="text" value="<?=$clubInfo['PROPERTY_PHONE_VALUE']?>"  placeholder="Телефон"    name="CLUB[PHONE]" class="span5">
        </div>
    </div>

    <?if(0):?>
    <div class="control-group">
        <label class="control-label">Тип заведения</label>
        <div class="controls">
            <select multiple="multiple"  class="span5">
                <?foreach($clubInfo['PROPERTY_TYPE_FACILITY_VALUE'] as $var):?>
                <option>1</option>
                <?endforeach;?>
            </select>
        </div>
    </div>

    <?endif;?>


    <div class="control-group">
        <label class="control-label">Метро</label>
        <div class="controls">
            <input type="text" value="<?=$clubInfo['PROPERTY_METRO_VALUE']?>"   name="CLUB[METRO]" class="span5">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Адрес</label>
        <div class="controls">
            <input type="text" value="<?=$clubInfo['PROPERTY_ADDRESS_VALUE']?>"    name="CLUB[ADDRESS]" class="span5">
        </div>
    </div>


    <?if(0):?>
    <div class="control-group">
        <label class="control-label">Музыка</label>
        <div class="controls">
            <select multiple="multiple">
                <?foreach($clubInfo["PROPS"]["MUSIC"]['VALUE'] as $var):?>
                <option>1</option>
                <?endforeach;?>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Фейсконтроль</label>
        <div class="controls">
            <input type="text" value="<?=$clubInfo["PROPS"]["FACE_CONTROL"]['VALUE']?>"  placeholder="Фейсконтроль"  class="span5">
        </div>
    </div>


    <div class="control-group">
        <label class="control-label">Дресс-код</label>
        <div class="controls">
            <input type="text" value="<?=$clubInfo["PROPS"]["DRESS_CODE"]['VALUE']?>"  placeholder="Дресс-код"  class="span5">
        </div>
    </div>
    <?endif;?>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" name="SAVE_PROFILE_CLUB">Сохранить</button>
    </div>
</form>
