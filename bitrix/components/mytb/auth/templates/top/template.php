<? if (!$USER->IsAuthorized()): ?>
<input type="hidden" value="<?=$arResult['LOGIN_TOP_REDIRECT']?>" id="login_top_redirect">
<div class="modal hide" id="auth_block_top">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <span class="h6">Авторизация на сайте www.MyTB.ru</span>
    </div>
    <div class="modal-body">

    <table width="100%" class="auth_table">
        <tr>
            <td>
                <div class="v_line_gray">
                    <h4>Зачем регистрироваться на сайте?</h4>
                    <ol>
                        <li>При регистрации создается Ваш личный аккаунт;</li>
                        <li>Вы сможете подписываться на <b>акции</b>, <b>новости</b> и <b>события</b> любимых заведений; </li>
                        <li>Вы вы всегда сможете найти ту информацию, на которую подписаны;</li>
                        <li>Вы сможете управлять подписками;</li>
                    </ol>
                </div>
            </td>
            <td width="50%">
                <div id="bx_auth_serv_Facebook"><a class="bx-button facebook" href="<?=$arResult['URL_FB']?>"><span>Войти через Facebook</span></a></div>
                <div id="bx_auth_serv_VKontakte" style="text-align: center;">
                    <a class="bx-button vkontakte" id="auth_vk" href="<?=$arResult['URL_VK']?>"><span>Войти через VKontakte</span></a></div>
                <br/>

                <table style="margin-left:20px;">
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
                    <tr>
                        <td>
                            <div class="alert hide errors_auth"  style="float: left; width: 110px">
                                <div class="error_text"></div>
                            </div>
                            <button type="button" class="btn btn-primary right btn-large login">Войти</button></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
</div>
<? endif; ?>