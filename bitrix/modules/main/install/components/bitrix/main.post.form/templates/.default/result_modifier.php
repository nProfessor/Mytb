<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
 ********************************************************************/
/***************** BASE ********************************************/
$arParams["IS_BLOG"] = ($arParams["IS_BLOG"] === true);

$arParams["FORM_ID"] = (!empty($arParams["FORM_ID"]) ? $arParams["FORM_ID"] : "POST_FORM");
$arParams["JS_OBJECT_NAME"] = "PlEditor".$arParams["FORM_ID"];
$arParams["LHE"] = (is_array($arParams['LHE']) ? $arParams['LHE'] : array());
$arParams["LHE"]["id"] = (empty($arParams["LHE"]["id"]) ? "idLHE_".$arParams["FORM_ID"] : $arParams["LHE"]["id"]);
$arParams["LHE"]["jsObjName"] = (empty($arParams["LHE"]["jsObjName"]) ? "oLHE".$arParams["FORM_ID"] : $arParams["LHE"]["jsObjName"]);

$arParams["PARSER"] = array_unique(is_array($arParams["PARSER"]) ? $arParams["PARSER"] : array());
$arParams["BUTTONS"] = is_array($arParams["BUTTONS"]) ? $arParams["BUTTONS"] : array();
$arParams["BUTTONS"] = (in_array("MentionUser", $arParams["BUTTONS"]) && !IsModuleInstalled("socialnetwork") ?
	array_diff($arParams["BUTTONS"], array("MentionUser")) : $arParams["BUTTONS"]);
$arParams["BUTTONS"] = array_values($arParams["BUTTONS"]);
$arParams["BUTTONS_HTML"] = is_array($arParams["BUTTONS_HTML"]) ? $arParams["BUTTONS_HTML"] : array();

$arParams["TEXT"] = (is_array($arParams["~TEXT"]) ? $arParams["~TEXT"] : array());
$arParams["TEXT"]["ID"] = (!empty($arParams["TEXT"]["ID"]) ? $arParams["TEXT"]["ID"] : "POST_MESSAGE");
$arParams["TEXT"]["NAME"] = (!empty($arParams["TEXT"]["NAME"]) ? $arParams["TEXT"]["NAME"] : "POST_MESSAGE");

$arParams["ADDITIONAL"] = (is_array($arParams["~ADDITIONAL"]) ? $arParams["~ADDITIONAL"] : array());
$arParams["ADDITIONAL"][] =
	"{ text : '".GetMessage("MPF_EDITOR")."', onclick : function() {window['".$arParams["JS_OBJECT_NAME"]."'].showPanelEditor(); this.popupWindow.close();}, className: 'blog-post-popup-menu', id: 'bx-html'}";

//$arParams["HTML_BEFORE_TEXTAREA"] = "";
//$arParams["HTML_AFTER_TEXTAREA"] = "";

$arParams["UPLOAD_FILE"] = (is_array($arParams["UPLOAD_FILE"]) ? $arParams["UPLOAD_FILE"] : array());
$arRes = array();
if (!empty($arParams["UPLOAD_FILE"]["INPUT_VALUE"]) && is_array($arParams["UPLOAD_FILE"]["INPUT_VALUE"])):
	foreach ($arParams["UPLOAD_FILE"]["INPUT_VALUE"] as $key => $arFile):
		if (!is_array($arFile))
			$arFile = CFile::GetFileArray($arFile);
		$arRes[$arFile["ID"]] = $arFile;
	endforeach;
	$arParams["UPLOAD_FILE"]["INPUT_VALUE"] = array_keys($arRes);
endif;
$arParams["UPLOAD_WEBDAV_ELEMENT"] = (is_array($arParams["UPLOAD_WEBDAV_ELEMENT"]) ? $arParams["UPLOAD_WEBDAV_ELEMENT"] : array());

$arParams["FILES"] = (is_array($arParams["FILES"]) ? $arParams["FILES"] : array());
$arParams["FILES"]["VALUE"] = (is_array($arParams["FILES"]["VALUE"]) ? $arParams["FILES"]["VALUE"] : array());
if (empty($arParams["FILES"]["VALUE"]) && !empty($arRes)):
	$arParams["FILES"]["VALUE"] = $arRes;
	$arParams["FILES"]["SHOW"] = "N";
endif;
$arParams["FILES"]["POSTFIX"] = trim($arParams["FILES"]["POSTFIX"]);
$arParams["FILES"]["VALUE_JS"] = array();
$arParams["FILES"]["VALUE_HTML"] = array();
$arParams["FILES"]["DEL_LINK"] = trim($arParams["FILES"]["DEL_LINK"]);

$arParams["DESTINATION"] = (is_array($arParams["DESTINATION"]) && IsModuleInstalled("socialnetwork") ? $arParams["DESTINATION"] : array());
$arParams["DESTINATION_SHOW"] = (array_key_exists("SHOW", $arParams["DESTINATION"]) ? $arParams["DESTINATION"]["SHOW"] : $arParams["DESTINATION_SHOW"]);
$arParams["DESTINATION_SHOW"] = ($arParams["DESTINATION_SHOW"] == "Y" ? "Y" : "N");
$arParams["DESTINATION"] = (array_key_exists("VALUE", $arParams["DESTINATION"]) ? $arParams["DESTINATION"]["VALUE"] : $arParams["DESTINATION"]);

$arParams["TAGS"] = (is_array($arParams["TAGS"]) ? $arParams["TAGS"] : array());
if (!empty($arParams["TAGS"]))
	$arParams["TAGS"]["VALUE"] = (is_array($arParams["TAGS"]["VALUE"]) ? $arParams["TAGS"]["VALUE"] : array());

$arParams["SMILES_COUNT"] = intVal($arParams["SMILES_COUNT"]);
$arParams["SMILES"] = (is_array($arParams["SMILES"]) ? $arParams["SMILES"] : array());
if (!empty($arParams["SMILES"]) && !in_array("SmileList", $arParams["PARSER"]))
	$arParams["PARSER"][] = "SmileList";

$arParams["CUSTOM_TEXT"] = (is_array($arParams["CUSTOM_TEXT"]) ? $arParams["CUSTOM_TEXT"] : array());
$arParams["CUSTOM_TEXT_HASH"] = (!empty($arParams["CUSTOM_TEXT"]) ? md5(implode("", $arParams["CUSTOM_TEXT"])) : "");

$arParams["IMAGE_THUMB"] = array("WIDTH" => 90, "HEIGHT" => 90);
$arParams["IMAGE"] = array("WIDTH" => 90, "HEIGHT" => 90);
/********************************************************************
				/Input params
 ********************************************************************/

foreach($arParams["FILES"]["VALUE"] as $arFile)
{
	ob_start();
	?><span class="feed-add-photo-block">
		<span class="feed-add-img-wrap"<?=((in_array("UploadImage", $arParams["PARSER"]) || in_array("UploadFile", $arParams["PARSER"])) ?
			' title="'.GetMessage("MPF_INSERT_FILE").'" onclick="'.$arParams["JS_OBJECT_NAME"].'.insertFile(\''.$arFile["ID"].'\');"' :
			'')?>>
			<img src="<?=$arFile["src"]?>" border="0" width="<?=$arFile["width"]?>" height="<?=$arFile["height"]?>" />
		</span>
		<span class="feed-add-img-title"<?=((in_array("UploadImage", $arParams["PARSER"]) || in_array("UploadFile", $arParams["PARSER"])) ?
			' title="'.GetMessage("MPF_INSERT_FILE").'" onclick="'.$arParams["JS_OBJECT_NAME"].'.insertFile(\''.$arFile["ID"].'\');"' :
			'')?>><?=$arFile["NAME"]?></span>
		<?=(empty($arFile["DEL_URL"]) ? '' : '<span class="feed-add-post-del-but" onclick="'.$arParams["JS_OBJECT_NAME"].'.deleteFile(\''.$arFile["ID"].'\', \''.CUtil::JSEscape($arFile["DEL_URL"]).'\', this); "></span>')?>
	</span><?
	$arParams["FILES"]["VALUE_HTML"][intVal($arFile["ID"])] = ob_get_clean();
	$arParams["FILES"]["VALUE_JS"][strVal($arFile["ID"])] = array(
		"id" => $arFile["ID"],
		"name" => $arFile["FILE_NAME"],
		"size" => $arFile["FILE_SIZE"],
		"url" => $arFile["URL"],
		"type" => $arFile["CONTENT_TYPE"],
		"src" => $arFile["SRC"],
		"thumbnail" => $arFile["THUMBNAIL"],
		"isImage" => (substr($arFile["CONTENT_TYPE"], 0, 6) == "image/")
	);
}
?>