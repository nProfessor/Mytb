<?$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");?>
<div class="left_list_club">
    <ul>
    <?foreach($arResult['CLUB_LIST'] as $var):?>
    <li><a href="#<?=$var["ID"]?>" class="club_name" data-id="<?=$var["ID"]?>"><?=$var["NAME"]?></a></li>
    <?endforeach?>
    </ul>
</div>
    <div>
        Всего <?=count($arResult['CLUB_LIST'])?> новых заведений
    </div>
    <div class="content_club_edite">
        <input type="hidden" value="" id="club_id">
        <table class="table">
            <tr>
                <td width="200px">Название:</td>
                <td><input type="text" name="NAME" value="" class="span8"></td>
            </tr>
            <tr>
                <td>Логотип:</td>
                <td><div class="logo_edite"></div>
                    <div class="btn_content_all">
                        <div class="btn_content">
                        <input type="button" class="btn" value="Загрузить" >
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <td>Ссылка на сайт:</td>
                <td><input type="text" value="" name="SITE" class="span8"></td>
            </tr>
            <tr>
                <td>Email менеджера:</td>
                <td><input type="text" name="EMAIL_MANAGER" value="" class="span8"></td>
            </tr>
            <tr>
                <td>Средний чек:</td>
                <td><input type="text" name="AVERAGE_CHECK" value="" class="span8"></td>
            </tr>
            <tr>
                <td>Время работы:</td>
                <td><textarea name="TIME_WORKING"  class="span8" row="4"></textarea></td>
            </tr>
            <tr>
                <td>Тип заведения:</td>
                <td>

                    <?foreach($arResult["KIND_CLUB_LIST"] as $var):?>
                    <div class="checkbox_list span3">
                        <label><input type="checkbox" name="KIND_CLUB[]" value="<?=$var["ID"]?>"> <?=$var["VALUE"]?></label>
                    </div>

                    <?endforeach;?>

                </td>
            </tr>
            <tr>
                <td>Музыка:</td>
                <td>

                        <?foreach($arResult["MUSIC_LIST"] as $var):?>
                       <div class="checkbox_list span3">
                        <label><input type="checkbox"  name="MUSIC[]" value="<?=$var["ID"]?>"> <?=$var["VALUE"]?></label>
                       </div>

                        <?endforeach;?>

                </td>
            </tr>
            <tr>
                <td>Описание клуба:</td>
                <td><textarea name="DESCR"  class="span8" style="height: 100px"></textarea></td>
            </tr>

            <tr>
                <td>Адреса:</td>
                <td id="address_list">
                    <div class="club_address_edite" style="display:none" id="">
                        Город:
                                <select name="SITY" class="span3" >
                                <option value="1">Москва</option>
                                <option value="2">Санкт-Петербург</option>
                            </select>
                                <input type="text" value="" class="addres_text span8" ><br/>
                        <div>
                        Телефоны через запятую +7 (495) 22-35-356,+7 (495) 22-35-356
                        <textarea  class="span8 phone" style="height: 100px"></textarea>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td></td>
<td>
                    <button class="btn add_new_address">Добавить еще адрес</button>
                </td>
            </tr>
        </table>
        <div class="form-actions">
            <button type="button" class="btn btn-primary" id="save">Сохранить изменения</button>
            <button type="button" class="btn"  id="show">Опубликовать заведения</button>
        </div>
    </div>