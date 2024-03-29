<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div id="forgot_password" class="login-popup-wrap login-popup-request-wrap">
	<input type="hidden" name="TYPE" value="SEND_PWD">
	<div class="login-popup">
		<div class="login-popup-title"><?=GetMessage('AUTH_FORGOT_PASSWORD')?></div>
		<div class="login-popup-title-description"><?=GetMessage("AUTH_GET_CHECK_STRING")?></div>
		<div class="login-popup-request-fields-wrap" id="forgot_password_fields">
			<div class="login-popup-field">
				<div class="login-popup-field-title"><?=GetMessage("AUTH_LOGIN")?></div>
				<div class="login-input-wrap">
					<input type="text" onfocus="BX.addClass(this.parentNode, 'login-input-active')" onblur="BX.removeClass(this.parentNode, 'login-input-active')" class="login-input"  name="USER_LOGIN" value="<?echo htmlspecialcharsbx($last_login)?>">
					<div class="login-inp-border"></div>
				</div>
			</div>
			<div class="login-popup-either"><?=GetMessage("AUTH_OR")?></div>
			<div class="login-popup-field">
				<div class="login-popup-field-title">E-mail</div>
				<div class="login-input-wrap">
					<input type="text" onfocus="BX.addClass(this.parentNode, 'login-input-active')" onblur="BX.removeClass(this.parentNode, 'login-input-active')" class="login-input" name="USER_EMAIL">
					<div class="login-inp-border"></div>
				</div>
			</div>
		</div>
		<div class="login-btn-wrap" id="forgot_password_message_button"><input type="submit" value="<?=GetMessage("AUTH_SEND")?>" class="login-btn" name="send_account_info"></div>
		<div class="login-popup-request-text" id="forgot_password_note">
			<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?><br>
			<a href="javascript:void(0)" onclick="BX.adminLogin.toggleAuthForm('authorize')"><?echo GetMessage("AUTH_GOTO_AUTH_FORM")?></a>
		</div>
	</div>
</div>

<div id="forgot_password_message" class="login-popup-wrap login-popup-ifo-wrap">
	<div class="login-popup">
		<div class="login-popup-title"><?=GetMessage('AUTH_FORGOT_PASSWORD')?></div>
		<div class="login-popup-title-description"><?=GetMessage("AUTH_GET_CHECK_STRING_SENT")?></div>
		<div class="login-popup-message-wrap">
			<div class="adm-info-message-wrap adm-info-message-green">
				<div class="adm-info-message" id="forgot_password_message_inner"></div>
			</div>
		</div>
		<a class="login-popup-forget-pas" href="javascript:void(0)" onclick="BX.adminLogin.toggleAuthForm('change_password')"><?=GetMessage('AUTH_GOTO_CHANGE_FORM')?></a>
	</div>
</div>

<script type="text/javascript">
var obForgMsg = new BX.authFormForgotPasswordMessage('forgot_password_message', {url:''}),
	obForg = new BX.authFormForgotPassword('forgot_password', {
		url: '<?echo CUtil::JSEscape($sDocPath."?forgot_password=yes".(($s=DeleteParam(array("forgot_password"))) == ""? "":"&".$s))?>',
		message: obForgMsg
});
BX.adminLogin.registerForm(obForg);
BX.adminLogin.registerForm(obForgMsg);
</script>
