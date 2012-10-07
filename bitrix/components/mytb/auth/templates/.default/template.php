<? if (!$USER->IsAuthorized()): ?>

<div class="modal hide fade" id="modal_auth">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6>Авторизация на сайте www.MyTB.ru</h6>
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
                    <div class="alert hide" id="errors_auth">
                        <div id="error_text"></div>
                    </div>
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
                        <tr>
                            <td><button type="button" class="btn btn-primary right" id="login">Войти</button></td>
                        </tr>
                    </table>


                </td>
            </tr>
        </table>
    </div>
</div>
<? endif; ?>