<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

if (class_exists("infocom_like")) return;
Class infocom_like extends CModule
{
	var $MODULE_ID = "infocom.like";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_PARTNER;
	public $PARTNER_URI;
	public $NEED_MODULES = array();

	public function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage("EPRICE_PARTNER_NAME");
		$this->PARTNER_URI = 'http://dev.infocomdv.ru';

		$this->MODULE_NAME = GetMessage('EPRICE_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('EPRICE_INSTALL_DESCRIPTION');
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		$GLOBALS["errors"] = $this->errors;	
	}

	function DoUninstall()
	{
		global $APPLICATION;
		
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$GLOBALS["errors"] = $this->errors;
	}
	
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		RegisterModule($this->MODULE_ID);
		return true;
	}
	
	function UnInstallDB()
	{	
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		UnRegisterModule($this->MODULE_ID);
		return true;
	}


	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);			
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/infocom/like/");
		
		return true;
	}
}
?>