<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($_REQUEST['bxsender'] != 'core_window_cauthdialog')
{
?>
<script type="text/javascript" bxrunfirst="true">top.BX.WindowManager.Get().Authorize(<?=CUtil::PhpToJsObject($arAuthResult)?>)</script>
<?
}
else
{
	$store_password = COption::GetOptionString("main", "store_password", "Y");
	$bNeedCaptcha = $APPLICATION->NeedCAPTHAForLogin($last_login);

	ob_start();
?>
<form name="form_auth" method="post" class="bx-admin-auth-form" action="" novalidate>
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="AUTH">

	<div class="bx-core-popup-auth-field">
		<div class="bx-core-popup-auth-field-caption"><?=GetMessage("AUTH_LOGIN")?></div>
		<div class="bx-core-popup-auth-field"><input type="text" name="USER_LOGIN" value="<?echo htmlspecialcharsbx($last_login)?>"></div>
	</div>
	<div class="bx-core-popup-auth-field">
		<div class="bx-core-popup-auth-field-caption"><?=GetMessage("AUTH_PASSWORD")?></div>
		<div class="bx-core-popup-auth-field"><input type="password" name="USER_PASSWORD"></div>
	</div>

<?
if($store_password=="Y"):
?>
	<div class="bx-core-popup-auth-field">
		<input type="checkbox" class="adm-designed-checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y">
		<label for="USER_REMEMBER" class="adm-designed-checkbox-label"></label><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label>
	</div>
<?
endif;

$CAPTCHA_CODE = '';
if($bNeedCaptcha):
	$CAPTCHA_CODE = $APPLICATION->CaptchaGetCode();
?>
	<input type="hidden" name="captcha_sid" value="<?=$CAPTCHA_CODE?>" />
	<div class="bx-core-popup-auth-field">
		<div class="bx-core-popup-auth-field-caption">
			<div><?=GetMessage("AUTH_CAPTCHA_PROMT")?></div>
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$CAPTCHA_CODE?>" width="180" height="40" alt="CAPTCHA" />
		</div>
		<div class="bx-core-popup-auth-field"><input type="text" name="captcha_word"></div>
	</div>
<?
endif;
?>
</form>
<?
	$form = ob_get_contents();
	ob_end_clean();
?>
<script type="text/javascript">
var authWnd = top.BX.WindowManager.Get();
authWnd.SetTitle('<?=GetMessageJS('AUTH_TITLE')?>');
authWnd.SetContent('<?=CUtil::JSEscape($form)?>');
authWnd.SetError(<?=CUtil::PhpToJsObject($arAuthResult)?>);
authWnd.adjustSizeEx();
</script>
<?
	if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
	{
		$sec = new CRsaSecurity();
		if(($arKeys = $sec->LoadKeys()))
		{
			$sec->SetKeys($arKeys);
			$sec->AddToForm('form_auth', array('USER_PASSWORD'));
		}
	}
}
?>