<? if (!$USER->IsAuthorized()): ?>
<div class="modal hide" id="auth_stocks">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Авторизация на сайте www.MyTB.ru</h6>
    </div>
    <div class="modal-body">
        <div>
            <div style="padding:0px 20px 20px 20px; font-size: 14px;">
                <img src="" id="img_club_logo" style="float:left; margin:0px 32px 5px 0px;"/>
                Вы хотите посмотреть информацию об <b>акции в «<span id="club_name_show"></span>»</b>.<br/>
                Информация доступна только зарегистрированным пользователям.<br/>
                Пожалуйста, зарегистрируйтесь или авторизуйтесь.<br/>
            </div>

        </div>
    </div>
        <div class="modal-footer">
            <table width="100%">
                <tr>
                    <td>
                        <div id="bx_auth_serv_Facebook"><a class="bx-button facebook"
                                                           href="<?=$arResult['URL_FB']?>"><span>Войти через Facebook.com</span></a>
                        </div>
                        <div id="bx_auth_serv_VKontakte" style="text-align: center;"><a class="bx-button vkontakte"
                                                                                        id="auth_vk"
                                                                                        href="<?=$arResult['URL_VK']?>"><span>Войти через VKontakte.ru</span></a>
                        </div>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <div class="input-prepend input-append">
                                        <span class="add-on">@</span><input class="span3" placeholder="Email" size="16"
                                                                            name="AUTH[EMAIL]"
                                                                            type="text">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-prepend input-append">
                                        <span class="add-on"><i class="icon-lock"></i></span><input class="span3"
                                                                                                    name="AUTH[PASSWORD]"
                                                                                                    placeholder="Пароль"
                                                                                                    id="" size="16"
                                                                                                    type="password">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">  <button type="button" class="btn btn-primary right login">Войти</button>
                                    Зарегистрировать меня <input type="checkbox" value="1" name="AUTH[REG]">
                                        <div class="error_text"></div>


                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

</div>
<? endif; ?>