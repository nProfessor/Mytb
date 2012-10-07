<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("fileman"))
	return;
if(!function_exists('CustomizeLightEditor'))
{
	function CustomizeLightEditor($id = false, $templateFolder = "")
	{
		static $bCalled = array();
		static $sTemplateFolder = "";
		if ($templateFolder != "")
			$sTemplateFolder = $templateFolder;

		if ($id && $bCalled[$id] !== true)
		{
?>
<script>
	CustomizeLightEditor('<?=$id?>', {
		path: '<?= CUtil::JSEscape($sTemplateFolder)?>',
		imageLinkText: '<?=GetMessage("MPF_IMAGE_LINK")?>',
		spoilerText: '<?=GetMessage("MPF_SPOILER")?>',
		videoText: '<?=GetMessage("FPF_VIDEO")?>',
		videoUploadText: '<?= GetMessage('BPC_VIDEO_P')?>',
		videoUploadText1: '<?= GetMessage('BPC_VIDEO_PATH_EXAMPLE')?>',
		videoUploadText2: '<?= GetMessage('FPF_VIDEO')?>',
		videoUploadText3: '<?=GetMessage("MPF_VIDEO_SIZE")?>'

	});
</script>
		<?
			$bCalled[$id] = true;
		}
	}
	CustomizeLightEditor(false, $templateFolder);
}
AddEventHandler("fileman", "OnIncludeLightEditorScript", "CustomizeLightEditor");
if ($arParams["LHE"]['ctrlEnterHandler'] === true || !empty($arParams["LHE"]['ctrlEnterHandler']))
{
?>
<script type="text/javascript">
window.__ctrlEnterHandler<?=$arParams["FORM_ID"]?> = function(e)
{
	window['<?=$arParams["LHE"]["jsObjName"]?>'].SaveContent();
	<?if ($arParams["LHE"]['ctrlEnterHandler'] !== true ):?>
	if (typeof window['<?=$arParams["LHE"]['ctrlEnterHandler']?>'] == 'function')
		window['<?=$arParams["LHE"]['ctrlEnterHandler']?>']();
	<?else:?>
	BX.submit(BX('<?=$arParams["FORM_ID"]?>'));
	<?endif;?>
}
</script>
<?
}
?>
<div id="edit-post-text"><?
$LHE = new CLightHTMLEditor;
$res = array(
	'id' => $arParams["LHE"]["id"],
//	'width' => '800', // default 100%
	'height' => $arParams["TEXT"]["HEIGHT"],
	'inputId' => $arParams["TEXT"]["ID"],
	'inputName' => $arParams["TEXT"]["NAME"],
	'content' => htmlspecialcharsBack($arParams["TEXT"]["VALUE"]),
	'bUseFileDialogs' => false,
	'bUseMedialib' => false,
	'toolbarConfig' => $arParams["PARSER"],
	'jsObjName' => $arParams["LHE"]["jsObjName"],
	'arSmiles' => $arParams["SMILES"],
	'smileCountInToolbar' => $arParams['SMILES_COUNT'],
	'bSaveOnBlur' => true,
	'BBCode' => true,
	'bConvertContentFromBBCodes' => false,
	'bQuoteFromSelection' => true, // Make quote from any text in the page
	'bSetDefaultCodeView' => false, // Set first view to CODE or to WYSIWYG
	'bBBParseImageSize' => true, // [IMG ID=XXX WEIGHT=5 HEIGHT=6],  [IMGWEIGHT=5 HEIGHT=6]/image.gif[/IMG]
	'bResizable' => true,
	'bAutoResize' => true,
	'autoResizeOffset' => 40,
	'controlButtonsHeight' => '34',
	'autoResizeSaveSize' => false
) + $arParams["LHE"];
if ($arParams["LHE"]['ctrlEnterHandler'] === true || !empty($arParams["LHE"]['ctrlEnterHandler']))
	$res['ctrlEnterHandler'] = "__ctrlEnterHandler".$arParams["FORM_ID"];
$LHE->Show($res);
$res = array();
foreach ($tmp = array(
	"UploadImage" => "postimage", "UploadFile" => "postfile",
	"InputVideo" => "postvideo", "MentionUser" => "postuser") as $key => $val):
	if (in_array($key, $arParams["PARSER"]))
		$res[] = $val;
endforeach;
?></div>
<script type="text/javascript">
window['<?=$arParams["LHE"]["id"]?>Settings'] = <?=CUtil::PhpToJSObject(
	array(
		'parsers' => $res,
		'arFiles' => array_keys($arParams["FILES"]["VALUE_HTML"]),
		'showEditor' => ($arParams["TEXT"]["SHOW"] == "Y"),
		'formID' => $arParams["FORM_ID"],
		'objName' => $arParams["JS_OBJECT_NAME"],
		'buttons' => $arParams["BUTTONS"]
	)
);?>;
BX.addCustomEvent(window, 'LHE_OnBeforeParsersInit', function(pEditor){__LHE_OnBeforeParsersInit(pEditor);});
BX.addCustomEvent(window, 'LHE_OnInit', function(pEditor){__LHE_OnInit(pEditor);});
</script>