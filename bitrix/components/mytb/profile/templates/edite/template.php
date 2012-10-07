<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery.maskedinput-1.3.min.js");
$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");


$userInfo = $arResult['userinfo'];


if (empty($userInfo["PERSONAL_MOBILE"]) && !empty($userInfo["PERSONAL_PHONE"])):
    $phoneConfirme = " warning";
elseif (empty($userInfo["PERSONAL_MOBILE"]) && empty($userInfo["PERSONAL_PHONE"])):
    $phoneConfirme = " error";
else:
    $phoneConfirme = " success";
endif;
?>
<div class="alert alert-success" id="save_ok" style="display: none;">
    <div class="content_text"></div>
</div>
<div class="form-horizontal">
    <table class="mytb_table">
        <tbody>
        <tr>
            <td>
                <fieldset>
                    <div class="control-group">
                        <label class="control-label">Логин:</label>

                        <div class="controls">
                            <input type="text" disabled="" value="<?=$userInfo["LOGIN"]?>" name="LOGIN"
                                   id="disabledInput" class="input-xlarge disabled" placeholder="Введите вашу фамилию">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Имя:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["NAME"]?>" name="NAME" class="span12"
                                   placeholder="Введите ваше имя">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Фамилия:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["LAST_NAME"]?>" name="LAST_NAME" class="span12"
                                   placeholder="Введите вашу фамилию">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Отчество:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["SECOND_NAME"]?>" name="SECOND_NAME" class="span12"
                                   placeholder="Введите ваше отчество">
                        </div>
                    </div>
                    <div class="control-group<?=$phoneConfirme?>" id="phone_number">
                        <label class="control-label">Телефон:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["PERSONAL_PHONE"]?>" name="PERSONAL_PHONE"
                                   class="span12" placeholder="Пример: +7 (111) 11-11-111">
                            <div id="message_text_phone">
                            <?if ($phoneConfirme == " warning"): ?>
                            <div id="phone_confirme_text">
                                <span class="help-inline">Телефон не подтвержден! <a href="#" title="подтвердить" id="phone_confirme">подтвердить</a></span>
                            </div>

                            <? elseif ($phoneConfirme == " success"): ?>
                            <span class="help-inline">Телефон подтвержден.</span>
                            <? endif;?>
                            </div>
                        </div>

                    </div>
                    <div id="phone_confirme_code"  class="alert alert-success" style="display: none;">

                        На номер <span id="namber" class="bold">+7 9265529608</span> был отпрален код. <a href="#" id="repeatedly_phone_confirme_code">выслать повторно</a>

                        <br/>
                        <br/>

                        <div class="input-append" id="code_input">
                            Введите его в поле <input class="span4" id="phone_confirme_code_val" size="16" type="text"><button class="btn" type="button" id="phone_confirme_ajax">Подтвердить</button>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Email:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["EMAIL"]?>" name="EMAIL" class="span12"
                                   placeholder="Введите ваш Email">
                        </div>
                    </div>
                    <div class="control-group ">
                        <label class="control-label">День рождения:</label>

                        <div class="controls">
                            <input type="text" value="<?=$userInfo["PERSONAL_BIRTHDAY"]?>" name="PERSONAL_BIRTHDAY"
                                   class="span12" placeholder="дд.мм.гг">
                        </div>
                    </div>
                    <div class="control-group ">
                        <label class="control-label">Пол:</label>

                        <div class="controls">
                            <select name="PERSONAL_GENDER" class="span12">
                                <option value="">На распутье</option>
                                <option value="M" <?if ($userInfo['PERSONAL_GENDER'] == "M"): ?>selected<? endif;?>>
                                    Мужской
                                </option>
                                <option value="F" <?if ($userInfo['PERSONAL_GENDER'] == "F"): ?>selected<? endif;?>>
                                    Женский
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group ">
                        <label class="control-label">Немного обо мне:</label>

                        <div class="controls">
                            <textarea class="span12" rows="8" placeholder="Расскажите немного о себе"
                                      name="PERSONAL_NOTES"><?=$userInfo['PERSONAL_NOTES']?></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary" type="button" id="save_profile" >Сохранить</button>
                    </div>
                </fieldset>

            </td>
            <td width="220px;">
                <a class="thumbnail" href="#">
                    <img  src="<?=imgurl("/upload/profile/{$userInfo["ID"]}/{$userInfo["PERSONAL_PHOTO"]['ORIGINAL_NAME']}",array("w"=>160,"h"=>120))?>">
                </a>
                <div class="button_upload">
                <a href="#"  class="btn btn-inverse upload_avatar" id="upload_avatar_button">Изменить</a>
                </div>
            </td>
        </tr>

        </tbody>
    </table>


</div>