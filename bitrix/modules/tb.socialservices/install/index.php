<?
global $MESS;
include(GetLangFileName(substr(__FILE__, 0, -18)."/lang/", "/install/index.php"));

class tb_socialservices extends CModule
{
	var $MODULE_ID = "td.socialservices";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
    var $PARTNER_NAME = "MyTb.ru";
    var $PARTNER_URI = "http://mytb.ru";

	function tb_socialservices()
	{
		$arModuleVersion = array();

		include(substr(__FILE__, 0,  -10)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("tb_socialservices_install_name");
		$this->MODULE_DESCRIPTION = GetMessage("tb_socialservices_install_desc");
	}

	function InstallDB($arParams = array())
	{
		RegisterModule("tb.socialservices");

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModule("tb.socialservices");

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if($_ENV["COMPUTERNAME"]!='BX')
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tb.socialservices/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tb.socialservices/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tb.socialservices/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tb.socialservices/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		}
		return true;
	}

	function UnInstallFiles()
	{
		if($_ENV["COMPUTERNAME"]!='BX')
		{
			DeleteDirFilesEx("/bitrix/js/tb.socialservices/");
			DeleteDirFilesEx("/bitrix/images/tb.socialservices/");
			DeleteDirFilesEx("/bitrix/tools/tr.oauth/");
		}
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$this->InstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("tb_socialservices_install_title_inst"), $DOCUMENT_ROOT."/bitrix/modules/tb.socialservices/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("tb_socialservices_install_title_unitst"), $DOCUMENT_ROOT."/bitrix/modules/tb.socialservices/install/unstep.php");
	}
}
?>