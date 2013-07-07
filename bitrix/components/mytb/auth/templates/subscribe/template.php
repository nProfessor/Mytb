<? if (!$USER->IsAuthorized()): ?>



<div class="modal hide fade" id="modal_auth_subscribe">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <span class="h6">Авторизация на сайте www.MyTB.ru</span>
    </div>
    <div class="modal-body">
        <div style="padding:0px 24px 20px 24px;    color: gray;
    font-size: 16px;
    line-height: 22px;
    padding: 0 24px 20px;">
        Для управлением подпиской необходим аккаунт.<br>
        Пожалуйста войди или зарегистрируйся.
        </div>
        <table width="100%" class="auth_table">
            <tr>
                <td>
                    <div id="bx_auth_serv_Facebook"><a class="bx-button facebook" href="<?=$arResult['URL_FB']?>"><span>Войти через Facebook</span></a></div>
                    <div id="bx_auth_serv_VKontakte" style="text-align: center;">
                        <a class="bx-button vkontakte" id="auth_vk" href="<?=$arResult['URL_VK']?>"><span>Войти через Вконтакте</span></a></div>
                </td>
                <td width="50%">

                   <table class="right">
                        <tr>
                            <td>
                                <div class="input-prepend input-append">
                                    <span class="add-on">@</span><input class="span3" placeholder="Email" size="16" name="AUTH[EMAIL]"
                                                                        type="text">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-prepend input-append">
                                    <span class="add-on"><i class="icon-lock"></i></span><input class="span3" name="AUTH[PASSWORD]"
                                                                                                placeholder="Пароль"
                                                                                                id="" size="16"
                                                                                                type="password">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Зарегистрировать меня <input type="checkbox" value="1" name="AUTH[REG]"></td>
                        </tr>
                   </table>
<!--            <tr>-->
<!--                <td>-->

<!--                </td>-->
<!--            </tr>-->



                </td>
            </tr>
        </table>
    </div>

    <div class="modal-footer">
        <button type="button" class="button right margin_l_10" id="login">Войти</button>
        <div class="hide padding_tb_4 red" id="errors_auth">
            <div id="error_text"></div>
        </div>
    </div>
</div>
<? endif; ?>