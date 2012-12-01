<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$aGlobalOpt = CUserOptions::GetOption("global", "settings", array());
$bShowSecurity = (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/install/index.php") && $aGlobalOpt['messages']['security'] <> 'N');

if (!$bShowSecurity)
	return false;

?><style type="text/css">
.bx-gadgets-colourful.bx-gadgets-security {border: 0;}
.bx-gadgets-colourful.bx-gadgets-security .bx-gadgets-content {background:url("/bitrix/gadgets/bitrix/admin_security/images/blue_back.png") repeat-x left bottom #4A95CC; position: relative;}
.bx-gadgets-security .bx-gadget-shield { width: 69px; height: 78px; position: absolute; bottom: 30px; right: 30px; background:url("/bitrix/gadgets/bitrix/admin_security/images/shield.png") no-repeat left top transparent; }
.bx-gadgets-security .bx-gadgets-side div { background:url("/bitrix/gadgets/bitrix/admin_security/images/stitch.png") repeat-y left top transparent; }

.bx-gadgets-security .bx-gadget-button {background: url("/bitrix/gadgets/bitrix/admin_security/images/button_back.png") repeat-x left top transparent;}
.bx-gadgets-security .bx-gadget-button-active {
	background: #176396!important;
	-moz-box-shadow: 0 1px 0 0 rgba(230,238,198,0.6),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
	-webkit-box-shadow: 0 1px 0 0 rgba(230,238,198,0.6),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
	box-shadow: 0 1px 0 0 rgba(152,205,237,0.76),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
}
.bx-gadgets-security .bx-gadget-button-lamp { background:url("/bitrix/gadgets/bitrix/admin_security/images/lamp.png") no-repeat left top transparent; }
.bx-gadgets-security .bx-gadget-button-active .bx-gadget-button-lamp { background:url("/bitrix/gadgets/bitrix/admin_security/images/lamp_active.png") no-repeat left top transparent; }
.bx-gadgets-security .bx-gadget-button-text {text-shadow: 0 -1px 0 rgba(8,32,58,0.77);}
.bx-gadgets-security .bx-gadgets-title {text-shadow: 0 1px 0 rgba(148,210,249,0.73);}
.bx-gadgets-security .bx-gadget-events {
	color: #0A3A68;
	text-shadow: 0 1px 0 #7FB6DF;
	display: inline-block;
	vertical-align: middle;
	margin-right: 10px;
	font-size: 50px;
	line-height: 47px;	
	font-family: Helvetica,Arial,sans-serif;
	font-weight: bold;

}
.bx-gadgets-security .bx-gadgets-title2 {color: #98cded; text-shadow: 0 -1px 0 rgba(10,34,57,0.58);}
</style><?

$bSecModuleInstalled = CModule::IncludeModule("security");
if($bSecModuleInstalled):
	$bSecurityFilter = CSecurityFilter::IsActive();
	if($bSecurityFilter):
		$lamp_class = " bx-gadgets-info";
		$text2_class = "green";
		$securityEventsCount = CSecurityFilter::GetEventsCount();
		if($securityEventsCount > 0)
			$text2 = GetMessage("GD_SECURITY_EVENT_COUNT");
		else
			$text2 = GetMessage("GD_SECURITY_EVENT_COUNT_EMPTY");
		if($securityEventsCount > 999)
			$securityEventsCount = round($securityEventsCount/1000,1).'K';

		$text3 = GetMessage("GD_SECURITY_LEVEL", array("#LANGUAGE_ID#"=>LANGUAGE_ID));
	else:
		$lamp_class = " bx-gadgets-note";	
		$text2_class = "red";
		$text2 = GetMessage("GD_SECURITY_FILTER_OFF_DESC");
		$text3 = '<p>'.GetMessage("GD_SECURITY_FILTER_DESC").'</p><form method="get" action="security_filter.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="submit" name="" value="'.GetMessage("GD_SECURITY_FILTER_TURN_ON").'"'.($GLOBALS["APPLICATION"]->GetGroupRight("security")<"W" ? " disabled" : "").'></form>';
	endif;
else:
	$lamp_class = "";
	$text2_class = "red";
	$text2 = GetMessage("GD_SECURITY_MODULE");
	$text3 = '<p>'.GetMessage("GD_SECURITY_MODULE_DESC").'</p><form method="get" action="module_admin.php"><input type="hidden" name="lang" value="'.LANGUAGE_ID.'"><input type="hidden" name="id" value="security">'.bitrix_sessid_post().'<input type="submit" name="install" value="'.GetMessage("GD_SECURITY_MODULE_INSTALL").'"'.(!$GLOBALS["USER"]->CanDoOperation('edit_other_settings') ? " disabled" : "").'></form>';
endif;

?><div class="bx-gadgets-title"><?=GetMessage("GD_SECURITY_TITLE")?></div><?
?><div class="bx-gadgets-title2">Web Application<br>Firewall</div><?

?><div class="bx-gadget-bottom-cont<?=((!$bSecModuleInstalled && $GLOBALS["USER"]->CanDoOperation('edit_other_settings')) || ($bSecModuleInstalled && $GLOBALS["APPLICATION"]->GetGroupRight("security") >= "W") ? " bx-gadget-bottom-button-cont" : "")?>"><?

	if (!$bSecModuleInstalled && $GLOBALS["USER"]->CanDoOperation('edit_other_settings'))
	{
		?><a class="bx-gadget-button bx-gadget-button-clickable" href="/bitrix/admin/module_admin.php?id=security&install=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>">
			<div class="bx-gadget-button-lamp"></div>
			<div class="bx-gadget-button-text"><?=GetMessage("GD_SECURITY_MODULE_INSTALL")?></div>
		</a><?
	}
	elseif ($bSecModuleInstalled && $GLOBALS["APPLICATION"]->GetGroupRight("security") >= "W")
	{
		?><a class="bx-gadget-button bx-gadget-button-clickable<?=($bSecurityFilter ? " bx-gadget-button-active" : "")?>" href="/bitrix/admin/security_filter.php?lang=<?=LANGUAGE_ID?>">
			<div class="bx-gadget-button-lamp"></div>
			<div class="bx-gadget-button-text"><?=($bSecurityFilter ? GetMessage("GD_SECURITY_FILTER_ON") : GetMessage("GD_SECURITY_FILTER_OFF"))?></div>
		</a><?
		if ($bSecurityFilter && $securityEventsCount > 0)
		{
			?><div class="bx-gadget-events"><?=$securityEventsCount?></div><?
		}
		?><div class="bx-gadget-desc"><?=$text2?></div><?
	}
?></div>
<div class="bx-gadget-shield"></div>