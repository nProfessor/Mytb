<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");
$APPLICATION->AddHeadScript("/jslibs/tiny_mce/tiny_mce.js");
$clubInfo = $arResult['arFields'];
$rating = intval($arResult['arFields']['PROPERTY_RATING_VALUE']);
$subStocs = $arResult['subs']['stock'];
$subEvent = $arResult['subs']['event'];
?>

<div class="right w8">
    <div class="padding_l_25">
        <h1>Редактируем <?=$clubInfo['NAME']?></h1>

            <input type="hidden" value="<?=$clubInfo['ID']?>" id="club_id">
            <table class="table">
                <tr>
                    <td width="200px">Название:</td>
                    <td><input type="text" name="NAME" value="<?=$clubInfo['NAME']?>" class="span8"></td>
                </tr>
                <tr>
                    <td>Ссылка на сайт:</td>
                    <td><input type="text" value="<?=$clubInfo['PROPERTY_SITE_VALUE']?>" name="SITE" class="span8"></td>
                </tr>
                <tr>
                    <td>Email менеджера:</td>
                    <td><input type="text" name="EMAIL_MANAGER" value="<?=$clubInfo['PROPERTY_EMAIL_MANAGER_VALUE']?>" class="span8"></td>
                </tr>
                <tr>
                    <td>Средний чек:</td>
                    <td><input type="text" name="AVERAGE_CHECK" value="<?=$clubInfo['PROPERTY_AVERAGE_CHECK_VALUE']?>" class="span8"></td>
                </tr>
                <tr>
                    <td>Время работы:</td>
                    <td><textarea name="TIME_WORKING"  class="span8" row="4"><?=$clubInfo['PROPERTY_TIME_WORKING_VALUE']?></textarea></td>
                </tr>
                <tr>
                    <td>Тип заведения:</td>
                    <td>
                        <?foreach($arResult["KIND_CLUB_LIST"] as $var):?>
                        <div class="checkbox_list span3">
                            <label><input type="checkbox" name="KIND_CLUB[]" value="<?=$var["ID"]?>" <?if(isset($clubInfo['PROPERTY_TYPE_FACILITY_VALUE'][$var["ID"]])){echo 'checked="checked"';}?>> <?=$var["VALUE"]?></label>
                        </div>
                        <?endforeach;?>

                    </td>
                </tr>
                <tr>
                    <td>Музыка:</td>
                    <td>
                        <?foreach($arResult["MUSIC_LIST"] as $var):?>
                        <div class="checkbox_list span3">
                            <label><input type="checkbox"  name="MUSIC[]" value="<?=$var["ID"]?>" <?if(isset($clubInfo['PROPERTY_MUSIC_VALUE'][$var["ID"]])) {echo 'checked="checked"';}?>> <?=$var["VALUE"]?></label>
                        </div>

                        <?endforeach;?>

                    </td>
                </tr>
                <tr>
                    <td>Описание клуба:</td>
                    <td><textarea name="DESCR"  class="span8" style="height: 200px" id="elm1"><?=str_replace("\n","<br>",$clubInfo['~DETAIL_TEXT'])?></textarea></td>
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
                    <?foreach($arResult['ADDRESS'] as $ADDRESS):?>
                        <div class="club_address_edite" id="club_address_<?=$ADDRESS['ID']?>">
                            Город:
                            <select name="SITY" class="span3" >
                                <option value="1" <?if($ADDRESS['SITY_ID']==1){echo 'selected="selected"';};?>>Москва</option>
                                <option value="2" <?if($ADDRESS['SITY_ID']==2){echo 'selected="selected"';};?>>Санкт-Петербург</option>
                            </select>
                            <input type="text" value="<?=$ADDRESS['ADDRESS']?>" class="addres_text span8" ><br/>
                            <div>
                                Телефоны через запятую +7 (495) 22-35-356,+7 (495) 22-35-356
                                <textarea  class="span8 phone" style="height: 100px"><?foreach($ADDRESS['PHONE'] as $PHONE):?><?=$PHONE."\n"?><?endforeach;?></textarea>
                            </div>
                        </div>
                    <?endforeach;?>
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
                <div id="message_errors" class="message_errors">

                </div>
            </div>

    </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("club:menu.left", "", array(
        "CLUB_ID" => $clubInfo['ID'],
    ),
    false
);?>
</div>