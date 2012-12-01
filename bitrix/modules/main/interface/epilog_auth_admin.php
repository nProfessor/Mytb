<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (isset($_REQUEST['bxsender']))
	return;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/lang_files.php");
?>

		</div><?//login-main-wrapper?>

	<div class="login-page-bg" id="login-page-bg"></div>
	</div><?//login_wrapper?>

	<div style="display: none;" id="window_wrapper"></div>

<script type="text/javascript">
BX.ready(BX.defer(function(){
	BX.addClass(document.body, 'login-animate');
	BX.addClass(document.body, 'login-animate-popup');

	//preload admin scripts
	setTimeout("BX.loadCSS(['/bitrix/panel/main/admin.css', '/bitrix/panel/main/admin-public.css', '/bitrix/panel/main/adminstyles_fixed.css', '/bitrix/themes/.default/modules.css']); BX.ajax.loadScriptAjax(['/bitrix/js/main/utils.js', '/bitrix/js/main/admin_tools.js', '/bitrix/js/main/popup_menu.js', '/bitrix/js/main/admin_search.js', '/bitrix/js/main/dd.js', '/bitrix/js/main/core/core_popup.js','/bitrix/js/main/core/core_date.js', '/bitrix/js/main/core/core_admin_interface.js', '/bitrix/js/main/core/core_autosave.js', '/bitrix/js/main/core/core_fx.js'], null, true);", 2000);
}));

new BX.COpener({DIV: 'login_lang_button', ACTIVE_CLASS: 'login-language-btn-active', MENU: <?=CUtil::PhpToJsObject($arLangButton['MENU'])?>});
</script>
</body>
</html>
