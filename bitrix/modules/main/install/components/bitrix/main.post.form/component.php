<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(strlen($arParams["FORM_ID"]) <= 0)
	$arParams["FORM_ID"] = "POST_FORM_".RandString(3);
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['mfi_mode']) && ($_REQUEST['mfi_mode'] == "upload"))
{
	if (!function_exists("__MPF_ImageResizeHandler"))
	{
		function __MPF_ImageResizeHandler(&$arCustomFile, $arParams = null)
		{
			static $arResizeParams = array();

			if ($arParams !== null)
			{
				if (is_array($arParams) && array_key_exists("width", $arParams) && array_key_exists("height", $arParams))
				{
					$arResizeParams = $arParams;
				}
				elseif(intVal($arParams) > 0)
				{
					$arResizeParams = array("width" => intVal($arParams), "height" => intVal($arParams));
				}
			}

			if ((!is_array($arCustomFile)) || !isset($arCustomFile['fileID']))
				return false;

			$fileID = $arCustomFile['fileID'];

			if (!isset($arCustomFile['fileContentType']))
			{
				$arFile = CFile::GetFileArray($fileID);
				$arCustomFile['fileContentType'] = $arFile['CONTENT_TYPE'];
				$arCustomFile["fileSrc"] = $arFile["SRC"];
			}

			if (strpos($arCustomFile['fileContentType'], 'image/') === 0)
			{
				$aImgThumb = CFile::ResizeImageGet(
					$fileID,
					array("width" => 90, "height" => 90),
					BX_RESIZE_IMAGE_EXACT,
					true
				);
				$arCustomFile['img_thumb_src'] = $aImgThumb['src'];

				if (!empty($arResizeParams))
				{
					$aImgSource = CFile::ResizeImageGet(
						$fileID,
						array("width" => $arResizeParams["width"], "height" => $arResizeParams["height"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arCustomFile['img_source_src'] = $aImgSource['src'];
				}
			}
		}
	}

	AddEventHandler('main',  "main.file.input.upload", '__MPF_ImageResizeHandler');
	if (!empty($arParams["UPLOAD_FILE_PARAMS"]))
	{
		$bNull = null;
		__MPF_ImageResizeHandler($bNull, $arParams["UPLOAD_FILE_PARAMS"]);
	}
}
$this->IncludeComponentTemplate();
?>