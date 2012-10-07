<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

if (!$USER->CanDoOperation('fileman_view_file_structure'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile(__FILE__);

/**
* Return right number.
* It's Replace G-> Gigabytes (* 1024) M->Megabytes ... K-> Kilobytes ...
* @param string $mixedValue - for example 128M
* @return int 134217728
*/
function GetSize($mixedVal) 
{
	$retVal = trim($mixedVal);
	$last = strtolower($retVal[strlen($retVal)-1]);
	switch($last) 
	{        
		case 't':
			$retVal *= 1024;
		case 'g':
			$retVal *= 1024;
		case 'm':
			$retVal *= 1024;
		case 'k':
			$retVal *= 1024;
	}

	return $retVal;
}


$strWarning = "";
$site = CFileMan::__CheckSite($site);
$DOC_ROOT = CSite::GetSiteDocRoot($site);

$io = CBXVirtualIo::GetInstance();

$path = $io->CombinePath("/", $path);
$arPath = Array($site, $path);
$arParsedPath = CFileMan::ParsePath(Array($site, htmlspecialcharsex($path)));
$abs_path = $DOC_ROOT.$path;
$absPathConverted = CBXVirtualIoFileSystem::ConvertCharset($abs_path);

if(!$USER->CanDoFileOperation('fm_download_file', $arPath))
	$strWarning = GetMessage("ACCESS_DENIED");
else if(!$io->FileExists($abs_path))
	$strWarning = GetMessage("FILEMAN_FILENOT_FOUND")." ";
elseif(!$USER->CanDoOperation('edit_php') && (HasScriptExtension($path) || substr(CFileman::GetFileName($path), 0, 1) == "."))
	$strWarning .= GetMessage("FILEMAN_FILE_DOWNLOAD_PHPERROR")."\n";

if(strlen($strWarning) <= 0)
{
	$flTmp = $io->GetFile($abs_path);
	$fsize = $flTmp->GetFileSize();
	$memoryLimit = (GetSize(ini_get("memory_limit"))-memory_get_usage())/10; //http://jabber.bx/view.php?id=16063	

	if($fsize<=($memoryLimit))
		$bufSize = $fsize;
	else
		$bufSize = $memoryLimit;

	session_write_close();    
	set_time_limit(0);

	header("Content-Type: application/force-download; name=\"".$io->GetPhysicalName($arParsedPath["LAST"])."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$fsize);
	header("Content-Disposition: attachment; filename=\"".$io->GetPhysicalName($arParsedPath["LAST"])."\"");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header('Connection: close');

	$f=fopen($absPathConverted, 'rb'); 

	while(!feof($f))     	
	{   	    	
		echo fread($f, $bufSize);        
		ob_flush();
		flush();   
		ob_end_clean ();
	}    

	fclose($f);
	die();
}

$APPLICATION->SetTitle(GetMessage("FILEMAN_FILEDOWNLOAD")." \"".$arParsedPath["LAST"]."\"");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<font class="text"><?=$arParsedPath["HTML"]?></font><br><br>
<?
ShowError($strWarning);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
