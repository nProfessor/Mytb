<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"FACEBOOK" => array(
			"NAME" => GetMessage("FACEBOOK"),
		),
		"VKONTAKTE" => array(
			"NAME" => GetMessage("VKONTAKTE"),
		),
		"MM_OK" => array(
			"NAME" => GetMessage("MM_OK"),
		),
		"GOOGLE" => array(
			"NAME" => GetMessage("GOOGLE"),
		),
		"TWEETTER" => array(
			"NAME" => GetMessage("TWEETTER"),
		),
	),
	"PARAMETERS" => array(	
		"MM_OK" => Array(
			"PARENT" => "MM_OK",
			"NAME" => GetMessage("SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => 'Y',
		),
		"FACEBOOK" => Array(
			"PARENT" => "FACEBOOK",
			"NAME" => GetMessage("SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => 'Y',
		),
		"VKONTAKTE" => Array(
			"PARENT" => "VKONTAKTE",
			"NAME" => GetMessage("SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => 'Y',
		),
		"GOOGLE" => Array(
			"PARENT" => "GOOGLE",
			"NAME" => GetMessage("SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => 'Y',
		),
		"TWEETTER" => Array(
			"PARENT" => "TWEETTER",
			"NAME" => GetMessage("SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => 'Y',
		),
		
	),
);


if($arCurrentValues["GOOGLE"]=="Y"):
	$arGoogleLang = array(
		"zh-CN" => GetMessage("zn"),
		"en-GB" => GetMessage("en"),
		"ru" => GetMessage("ru"),
	);
	$arComponentParameters["PARAMETERS"]["GOOGLE_LANG"] = array(
		"PARENT" => "GOOGLE",
		"NAME" => GetMessage("GOOGLE_LANG"),
		"TYPE" => "LIST",
		"VALUES" => $arGoogleLang,
		"DEFAULT" => 'ru'
	);
	$arComponentParameters["PARAMETERS"]["GOOGLE_ADD"] = array(
		"PARENT" => "GOOGLE",
		"NAME" => GetMessage("GOOGLE_ADD"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'Y'
	);
endif;
if($arCurrentValues["MM_OK"]=="Y"):
	$mm_ok_type = array(
		"button" => GetMessage("MM_OK_BUTTON"),
		"small" => GetMessage("MM_OK_SMALL"),
	);
	$mm_button = array(
		"1" => GetMessage("MM_NR"),
		"2" => GetMessage("MM_POD"),
		"3" => GetMessage("MM_REK"),
	);
	$ok_button = array(
		"1" => GetMessage("OK_KL"),
		"2" => GetMessage("MM_POD"),
		"3" => GetMessage("MM_NR"),
	);
	$arComponentParameters["PARAMETERS"]["MM_OK_TYPE"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("MM_OK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $mm_ok_type,
		"DEFAULT" => 'button'
	);
	$arComponentParameters["PARAMETERS"]["MM_NAME"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("MM_NAME"),
		"TYPE" => "LIST",
		"VALUES" => $mm_button,
		"DEFAULT" => '1'
	);
	$arComponentParameters["PARAMETERS"]["OK_NAME"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("OK_NAME"),
		"TYPE" => "LIST",
		"VALUES" => $ok_button,
		"DEFAULT" => '1'
	);
	$arComponentParameters["PARAMETERS"]["MM_OK_SH"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("MM_OK_SH"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'Y'
	);
	$arComponentParameters["PARAMETERS"]["MM_OK_TEXT"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("MM_OK_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'Y'
	);
	$arComponentParameters["PARAMETERS"]["MM_OK_WIDTH"] = array(
		"PARENT" => "MM_OK",
		"NAME" => GetMessage("MM_OK_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => '220'
	);
endif;
if($arCurrentValues["VKONTAKTE"]=="Y"):
$arVkontakteType = array(
	"full" => GetMessage("VK_FULL"),
	"button" => GetMessage("VK_BUTTON"),
	"mini" => GetMessage("VK_MINI"),
);
	$arComponentParameters["PARAMETERS"]["VKONTAKTE_APIID"] = array(
		"PARENT" => "VKONTAKTE",
		"NAME" => GetMessage("APIID"),
		"TYPE" => "STRING",
		"DEFAULT" => ''
	);
	$arComponentParameters["PARAMETERS"]["VKONTAKTE_TYPE"] = array(
		"PARENT" => "VKONTAKTE",
		"NAME" => GetMessage("VKONTAKTE_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arVkontakteType,
		"DEFAULT" => 'button'
	);
endif;
if($arCurrentValues["FACEBOOK"]=="Y"):
	$arFacebookType = array(
		"standard" => GetMessage("FB_STANDART"),
		"button_count" => GetMessage("FB_BUTTON_COUNT"),
		"box_count" => GetMessage("FB_BOX_COUNT"),
	);
	$arComponentParameters["PARAMETERS"]["FACEBOOK_TYPE"] = array(
		"PARENT" => "FACEBOOK",
		"NAME" => GetMessage("FACEBOOK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arFacebookType,
		"DEFAULT" => 'box_count'
	);
	$arComponentParameters["PARAMETERS"]["FACEBOOK_SEND_BUTTON"] = array(
		"PARENT" => "FACEBOOK",
		"NAME" => GetMessage("FACEBOOK_SEND_BUTTON"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'N'
	);
	$arComponentParameters["PARAMETERS"]["FACEBOOK_SHOW_FACE"] = array(
		"PARENT" => "FACEBOOK",
		"NAME" => GetMessage("FACEBOOK_SHOW_FACE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'N'
	);
	$arComponentParameters["PARAMETERS"]["FACEBOOK_WIDTH"] = array(
		"PARENT" => "FACEBOOK",
		"NAME" => GetMessage("FACEBOOK_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => '150'
	);
endif;
if($arCurrentValues["TWEETTER"]=="Y"):
$arTweetterType = array(
	"none" => GetMessage("TW_NONE"),
	"horizontal" => GetMessage("TW_HORIZONTAL"),
	"vertical" => GetMessage("TW_VERTICAL"),
);
	$arComponentParameters["PARAMETERS"]["TWEETTER_TYPE"] = array(
		"PARENT" => "TWEETTER",
		"NAME" => GetMessage("TWEETTER_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arTweetterType,
		"DEFAULT" => 'horizontal'
	);
	$arComponentParameters["PARAMETERS"]["TWEETTER_NAME"] = array(
		"PARENT" => "TWEETTER",
		"NAME" => GetMessage("TWEETTER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "TWEETTER_NAME",
	);
endif;
?>
