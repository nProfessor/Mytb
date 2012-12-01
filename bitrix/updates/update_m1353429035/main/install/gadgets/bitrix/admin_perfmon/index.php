<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><style type="text/css">
.bx-gadgets-colourful.bx-gadgets-perfmon {border: 0;}
.bx-gadgets-colourful.bx-gadgets-perfmon .bx-gadgets-content {background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/green_back.png") repeat-x left bottom #A5BF3C; position: relative;}
.bx-gadgets-perfmon .bx-gadgets-side div { background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/stitch.png") repeat-y left top transparent; }
.bx-gadgets-perfmon .bx-gadget-shield { width: 91px; height: 84px; position: absolute; bottom: 30px; right: 20px; background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/shield.png") no-repeat left top transparent; }
.bx-gadgets-perfmon .bx-gadget-button {background: url("/bitrix/gadgets/bitrix/admin_perfmon/images/button_back.png") repeat-x left top transparent;}
.bx-gadgets-perfmon .bx-gadget-button-active {
	background: #5D7113!important;
	-moz-box-shadow: 0 1px 0 0 rgba(230,238,198,0.6),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
	-webkit-box-shadow: 0 1px 0 0 rgba(230,238,198,0.6),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
	box-shadow: 0 1px 0 0 rgba(230,238,198,0.6),inset 0 2px 2px 0 rgba(0,0,0,0.4)!important;
}
.bx-gadgets-perfmon .bx-gadget-button-lamp { background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/lamp.png") no-repeat left top transparent; }
.bx-gadgets-perfmon .bx-gadget-button-active .bx-gadget-button-lamp { background:url("/bitrix/gadgets/bitrix/admin_perfmon/images/lamp_active.png") no-repeat left top transparent; }
.bx-gadgets-perfmon .bx-gadget-mark {color: #3b4a00; text-shadow: 0 1px 0 rgba(243,244,188,0.73);}
.bx-gadgets-perfmon .bx-gadget-button-text {text-shadow: 0 1px 0 rgba(221,231,233,0.44);}

.bx-gadgets-perfmon .bx-gadgets-title {text-shadow: 0 1px 0 rgba(243,244,188,0.73);}
</style><?

$bPerfmonModuleInstalled = IsModuleInstalled("perfmon");
if($bPerfmonModuleInstalled):
	$mark_value = str_replace(".", ",", (string)(double)COption::GetOptionString("perfmon", "mark_php_page_rate", ""));
	if($mark_value > 0):
		$text2 = GetMessage("GD_PERFMON_CUR");
	else:
		$text2 = str_replace(array("#STARTLINK#", "#ENDLINK#"), ($GLOBALS["APPLICATION"]->GetGroupRight("perfmon") >= "W" ? array('<a href="/bitrix/admin/perfmon_panel.php?lang='.LANGUAGE_ID.'">', '</a>') : array('', '')), GetMessage("GD_PERFMON_NO_RES"));
	endif;
else:
	$text2 = GetMessage("GD_PERFMON_NO_MODULE_INST");
endif;

?><div class="bx-gadgets-title"><?=GetMessage("GD_PERFMON")?></div><?
?><div class="bx-gadget-bottom-cont<?=(
	(
		!$bPerfmonModuleInstalled 
		&& $GLOBALS["USER"]->CanDoOperation('edit_other_settings') 
	)
	|| (
		$bPerfmonModuleInstalled 
		&& (
			$GLOBALS["APPLICATION"]->GetGroupRight("perfmon") >= "W"
			|| $mark_value > 0
		)
	)
		? " bx-gadget-bottom-button-cont" 
		: ""
)?><?=($mark_value > 0 ? " bx-gadget-mark-cont" : "")?>"><?
	if (!$bPerfmonModuleInstalled)
	{
		if ($GLOBALS["USER"]->CanDoOperation('edit_other_settings'))
		{
			?><a class="bx-gadget-button bx-gadget-button-clickable" href="/bitrix/admin/module_admin.php?id=perfmon&install=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>">
				<div class="bx-gadget-button-lamp"></div>
				<div class="bx-gadget-button-text"><?=GetMessage("GD_PERFMON_ON")?></div>
			</a><?
		}
	}
	else
	{
		if ($GLOBALS["APPLICATION"]->GetGroupRight("perfmon") >= "W")
		{
			?><a class="bx-gadget-button bx-gadget-button-clickable<?=($mark_value > 0 ? " bx-gadget-button-active" : "")?>" href="/bitrix/admin/perfmon_panel.php?lang=<?=LANGUAGE_ID?>">
				<div class="bx-gadget-button-lamp"></div>
				<div class="bx-gadget-button-text"><?=GetMessage(($mark_value > 0 ? "GD_PERFMON_TESTED" : "GD_PERFMON_TEST"))?></div>
			</a><?
		}
		elseif($mark_value > 0)
		{
			?><div class="bx-gadget-button bx-gadget-button-active">
				<div class="bx-gadget-button-lamp"></div>
				<div class="bx-gadget-button-text"><?=GetMessage("GD_PERFMON_TESTED")?></div>
			</div><?
		}

		if ($mark_value > 0)
		{
			?><div class="bx-gadget-mark"><?=$mark_value?></div><?
		}
	}
	?><div class="bx-gadget-desc<?=($mark_value > 0 ? " bx-gadget-desc-wmark" : "")?>"><?=$text2?></div><?
?></div>
<div class="bx-gadget-shield"></div>