<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

function dump_post_var($vname, $vvalue, $var_stack=array())
{
	if(is_array($vvalue))
	{
		$str = "";
		foreach($vvalue as $key=>$value)
			$str .= ($str == "" ? '' : '&').dump_post_var($key, $value, array_merge($var_stack ,array($vname)));
		return $str;
	}
	else
	{
		if(count($var_stack)>0)
		{
			$var_name=$var_stack[0];
			for($i=1; $i<count($var_stack);$i++)
				$var_name.="[".$var_stack[$i]."]";
			$var_name.="[".$vname."]";
		}
		else
			$var_name=$vname;

		return urlencode($var_name).'='.urlencode($vvalue);
	}
}

if (isset($_REQUEST['bxsender']))
{
	if ($_REQUEST['bxsender'] != 'core_autosave')
		require('wrapper_popup.php');

	return;
}

if ($arAuthResult && defined('ADMIN_SECTION_LOAD_AUTH') && ADMIN_SECTION_LOAD_AUTH === 1 || $_REQUEST['AUTH_FORM'])
{
	$APPLICATION->RestartBuffer();
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/auth/wrapper_auth_result.php");
	die();
}

$post_data = '';
foreach($_POST as $vname=>$vvalue)
{
	if($vname=="USER_LOGIN" || $vname=="USER_PASSWORD")
		continue;
	$post_data .= ($post_data == '' ? '' : '&').dump_post_var($vname, $vvalue);
}

if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
{
	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys()))
	{
		$sec->SetKeys($arKeys);
		$sec->AddToForm('form_auth', array('USER_PASSWORD', 'USER_CONFIRM_PASSWORD'));
		$bSecure = true;
	}
}

$sDocPath = $APPLICATION->GetCurPage();
?>
<form name="form_auth" method="post" target="auth_frame" class="bx-admin-auth-form" action="" novalidate>
	<div class="login-popup-alignment">
		<div class="login-popup-alignment-2" id="popup_alignment">
			<input type="hidden" name="AUTH_FORM" value="Y">

			<div id="auth_form_wrapper"></div>

			<?=bitrix_sessid_post()?>

		</div>
	</div>
</form>
<iframe name="auth_frame" src="" style="display:none;"></iframe>
<script type="text/javascript">
BX.message({
	'admin_authorize_error': '<?=GetMessageJS("admin_authorize_error")?>',
	'admin_authorize_info': '<?=GetMessageJS("admin_authorize_info")?>'
});

new BX.adminLogin({
	form: 'form_auth',
	start_form: '<?=CUtil::JSEscape($inc_file)?>',
	post_data: '<?=CUtil::JSEscape($post_data)?>',
	popup_alignment: 'popup_alignment',
	login_wrapper: 'login_wrapper',
	window_wrapper: 'window_wrapper',
	auth_form_wrapper: 'auth_form_wrapper',
	login_variants: 'login_variants',
	url: '<?echo CUtil::JSEscape($sDocPath.(($s=DeleteParam(array("logout", "login"))) == ""? "":"?".$s));?>'
});
</script>

<div id="login_variants" style="display: none;">
<?
require('authorize.php');
require('forgot_password.php');
require('change_password.php');
?>

</div>
<?
if ($arAuthResult)
{
	$bOnHit = true;
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/auth/wrapper_auth_result.php");
}
?>