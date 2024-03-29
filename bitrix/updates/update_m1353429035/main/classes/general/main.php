<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

define('BX_SPREAD_SITES', 2);
define('BX_SPREAD_DOMAIN', 4);

define('BX_RESIZE_IMAGE_PROPORTIONAL_ALT', 0);
define('BX_RESIZE_IMAGE_PROPORTIONAL', 1);
define('BX_RESIZE_IMAGE_EXACT', 2);

global $BX_CACHE_DOCROOT;
$BX_CACHE_DOCROOT = Array();
global $MODULE_PERMISSIONS;
$MODULE_PERMISSIONS = Array();

class CAllMain
{
	var $ma, $mapos;
	var $sDocPath2, $sDirPath, $sUriParam;
	var $sDocTitle;
	var $sDocTitleChanger = null;
	var $arPageProperties = array();
	var $arPagePropertiesChanger = array();
	var $arDirProperties = array();
	var $bDirProperties = false;
	var $sLastError;
	var $sPath2css = array();
	var $arHeadStrings = array();
	var $arHeadScripts = array();
	var $version;
	var $arAdditionalChain = array();
	var $FILE_PERMISSION_CACHE = array();
	var $arPanelButtons = array();
	var $arPanelFutureButtons = array();
	var $ShowLogout = false;
	var $ShowPanel = NULL, $PanelShowed = false;
	var $arrSPREAD_COOKIE = array();
	var $buffer_content = array();
	var $buffer_content_type = array();
	var $buffer_man = false;
	var $buffer_manual = false;
	var $auto_buffer_cleaned, $buffered = false;
	/**
	 * @var CApplicationException
	 */
	var $LAST_ERROR = false;
	var $ERROR_STACK = array();
	var $arIncludeDebug = array();
	var $aCachedComponents = array();
	var $ShowIncludeStat = false;
	var $_menu_recalc_counter = 0;
	var $__view = array();
	var $editArea = false;
	var $arComponentMatch = false;

	function __construct()
	{
		$this->CMain();
	}

	function CMain()
	{
		global $QUERY_STRING;
		$this->sDocPath2 = GetPagePath(false, true);
		$this->sDirPath = GetDirPath($this->sDocPath2);
		$this->sUriParam = (strlen($_SERVER["QUERY_STRING"])>0) ? $_SERVER["QUERY_STRING"] : $QUERY_STRING;
	}

	function reinitPath()
	{
		$this->sDocPath2 = GetPagePath(false, true);
		$this->sDirPath = GetDirPath($this->sDocPath2);
	}

	function GetCurPage($get_index_page=null)
	{
		if (null === $get_index_page)
		{
			if (defined('BX_DISABLE_INDEX_PAGE'))
				$get_index_page = !BX_DISABLE_INDEX_PAGE;
			else
				$get_index_page = true;
		}

		$str = $this->sDocPath2;

		if (!$get_index_page)
		{
			if (($i = strpos($str, '/index.php')) !== false)
				$str = substr($str, 0, $i).'/';
		}

		return $str;
	}

	function SetCurPage($page, $param=false)
	{
		$this->sDocPath2 = GetPagePath($page);
		$this->sDirPath = GetDirPath($this->sDocPath2);
		if($param !== false)
			$this->sUriParam = $param;
	}

	function GetCurUri($addParam="", $get_index_page=null)
	{
		$page = $this->GetCurPage($get_index_page);
		$param = $this->GetCurParam();
		if(strlen($param)>0)
			$url = $page."?".$param.($addParam!=""? "&".$addParam: "");
		else
			$url = $page.($addParam!=""? "?".$addParam: "");
		return $url;
	}

	function GetCurPageParam($strParam="", $arParamKill=array(), $get_index_page=null)
	{
		$sUrlPath = $this->GetCurPage($get_index_page);
		$strNavQueryString = DeleteParam($arParamKill);
		if($strNavQueryString <> "" && $strParam <> "")
			$strNavQueryString = "&".$strNavQueryString;
		if($strNavQueryString == "" && $strParam == "")
			return $sUrlPath;
		else
			return $sUrlPath."?".$strParam.$strNavQueryString;
	}

	function GetCurParam()
	{
		return $this->sUriParam;
	}

	function GetCurDir()
	{
		return $this->sDirPath;
	}

	function GetFileRecursive($strFileName, $strDir=false)
	{
		if($strDir===false)
			$strDir = $this->GetCurDir();

		$io = CBXVirtualIo::GetInstance();
		$fn = $io->CombinePath("/", $strDir, $strFileName);

		while(!$io->FileExists($io->RelativeToAbsolutePath($fn)))
		{
			$p = bxstrrpos($strDir, "/");
			if($p===false) break;
			$strDir = substr($strDir, 0, $p);
			$fn = $io->CombinePath("/", $strDir, $strFileName);
		}
		if($p===false)
			return false;

		return $fn;
	}

	function IncludeAdminFile($strTitle, $filepath)
	{
		//define all global vars
		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++)
			if($keys[$i]!="i" && $keys[$i]!="GLOBALS" && $keys[$i]!="strTitle" && $keys[$i]!="filepath")
				global ${$keys[$i]};

		//title
		$APPLICATION->SetTitle($strTitle);

		//� ����������� �� ���������� ������� �����
		include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
		include($filepath);
		include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
		die();
	}

	function SetAuthResult($arAuthResult)
	{
		$this->arAuthResult = $arAuthResult;
	}

	function AuthForm($mess, $show_prolog=true, $show_epilog=true, $not_show_links="N", $do_die=true)
	{
		//������� ��� ���������� ���������� �������� �����
		$excl = array("excl"=>1, "key"=>1, "GLOBALS"=>1, "mess"=>1, "show_epilog"=>1, "show_epilog"=>1, "not_show_links"=>1, "do_die"=>1);
		foreach($GLOBALS as $key => $value)
			if(!array_key_exists($key , $excl))
				global ${$key};

		if(substr($this->GetCurDir(), 0, strlen(BX_ROOT."/admin/")) == BX_ROOT."/admin/" || (defined("ADMIN_SECTION") && ADMIN_SECTION===true))
			$isAdmin = "_admin";
		else
			$isAdmin = "";

		if(isset($this->arAuthResult) && $this->arAuthResult !== true && (is_array($this->arAuthResult) || strlen($this->arAuthResult)>0))
			$arAuthResult = $this->arAuthResult;
		else
			$arAuthResult = $mess;

		//��������� ��������
		$APPLICATION->SetTitle(GetMessage("AUTH_TITLE"));

		//������� �� cookies ��������� ������� ��� �����
		$last_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};

		$inc_file = "";
		$comp_name = "";
		if($forgot_password=="yes")
		{
			//����� ������� ������
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_SEND_PASSWORD"));
			$comp_name = "system.auth.forgotpasswd";
			$inc_file = "forgot_password";
		}
		elseif($change_password=="yes")
		{
			//����� ��������� ������
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_CHANGE_PASSWORD"));
			$comp_name = "system.auth.changepasswd";
			$inc_file = "change_password";
		}
		elseif($register=="yes" && $isAdmin==""	&& COption::GetOptionString("main", "new_user_registration", "N")=="Y")
		{
			//����� �����������
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_REGISTER"));
			$comp_name = "system.auth.registration";
			$inc_file = "registration";
		}
		elseif(($confirm_registration === "yes") && ($isAdmin === "") && (COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") === "Y"))
		{
			//confirm registartion
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_CONFIRM"));
			$comp_name = "system.auth.confirmation";
			$inc_file = "confirmation";
		}
		elseif($authorize_registration=="yes" && $isAdmin=="")
		{
			//����� ����������� � �����������
			$inc_file = "authorize_registration";
		}
		else
		{
			//����� �����������
			$comp_name = "system.auth.authorize";
			$inc_file = "authorize";
		}

		if($show_prolog)
		{
			CMain::PrologActions();

			define("BX_AUTH_FORM", true);
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog".$isAdmin. "_after.php");
		}

		if($isAdmin == "")
		{
			// ���� �������� ������ ���������� � ���� ��� ���������� - ����������
			if(COption::GetOptionString("main", "auth_comp2", "N") == "Y" && $comp_name <> "")
			{
				$this->IncludeComponent("bitrix:".$comp_name, "", array(
					"AUTH_RESULT" => $arAuthResult,
					"NOT_SHOW_LINKS" => $not_show_links,
				));
			}
			else
			{
				$this->IncludeFile("main/auth/".$inc_file.".php", Array("last_login"=>$last_login, "arAuthResult"=>$arAuthResult, "not_show_links" => $not_show_links));
			}
		}
		else
		{
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/auth/wrapper.php");
		}

		if($show_epilog)
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog".$isAdmin.".php");

		if($do_die)
			die();
	}

	function ShowAuthForm($message)
	{
		$GLOBALS['APPLICATION']->AuthForm($message, false, false, "N", false);
	}

	function NeedCAPTHAForLogin($login)
	{
		//When last login was failed then ask for CAPTCHA
		if(isset($_SESSION["BX_LOGIN_NEED_CAPTCHA"]) && $_SESSION["BX_LOGIN_NEED_CAPTCHA"])
		{
			return true;
		}

		//This is local cache. May save one query.
		$USER_ATTEMPTS = false;

		//Check if SESSION cache for POLICY_ATTEMPTS is actual for given login
		if(
			!array_key_exists("BX_LOGIN_NEED_CAPTCHA_LOGIN", $_SESSION)
			|| $_SESSION["BX_LOGIN_NEED_CAPTCHA_LOGIN"]["LOGIN"] !== $login
		)
		{
			$POLICY_ATTEMPTS = 0;
			if($login <> '')
			{
				$rsUser = CUser::GetList(($o='LOGIN'), ($b='DESC'), array("LOGIN_EQUAL_EXACT" => $login), array('FIELDS' => array('ID', 'LOGIN', 'LOGIN_ATTEMPTS')));
				$arUser = $rsUser->Fetch();
				if($arUser)
				{
					$arPolicy = CUser::GetGroupPolicy($arUser["ID"]);
					$POLICY_ATTEMPTS = intval($arPolicy["LOGIN_ATTEMPTS"]);
					$USER_ATTEMPTS = intval($arUser["LOGIN_ATTEMPTS"]);
				}
			}
			$_SESSION["BX_LOGIN_NEED_CAPTCHA_LOGIN"] = array(
				"LOGIN" => $login,
				"POLICY_ATTEMPTS" => $POLICY_ATTEMPTS,
			);
		}

		//For users who had sucsessful login and if policy is set
		//check for CAPTCHA display
		if(
			$login <> ''
			&& $_SESSION["BX_LOGIN_NEED_CAPTCHA_LOGIN"]["POLICY_ATTEMPTS"] > 0
		)
		{
			//We need to know how many attempts user made
			if($USER_ATTEMPTS === false)
			{
				$rsUser = CUser::GetList(($o='LOGIN'), ($b='DESC'), array("LOGIN_EQUAL_EXACT" => $login), array('FIELDS' => array('ID', 'LOGIN', 'LOGIN_ATTEMPTS')));
				$arUser = $rsUser->Fetch();
				if($arUser)
					$USER_ATTEMPTS = intval($arUser["LOGIN_ATTEMPTS"]);
				else
					$USER_ATTEMPTS = 0;
			}
			//When user login attempts exceeding the policy we'll show the CAPTCHA
			if($USER_ATTEMPTS >= $_SESSION["BX_LOGIN_NEED_CAPTCHA_LOGIN"]["POLICY_ATTEMPTS"])
				return true;
		}

		return false;
	}

	function GetMenuHtml($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = $this->GetMenu($type, $bMenuExt, $template, $sInitDir);
		return $menu->GetMenuHtml();
	}

	function GetMenuHtmlEx($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = $this->GetMenu($type, $bMenuExt, $template, $sInitDir);
		return $menu->GetMenuHtmlEx();
	}

	function GetMenu($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = new CMenu($type);
		if($sInitDir===false)
			$sInitDir = $this->GetCurDir();
		if(!$menu->Init($sInitDir, $bMenuExt, $template))
			$menu->MenuDir = $sInitDir;
		return $menu;
	}

	function IsHTTPS()
	{
		return ($_SERVER["SERVER_PORT"]==443 || (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"])=="on"));
	}

	function GetTitle($property_name = false, $strip_tags = false)
	{
		if($property_name!==false && strlen($this->GetProperty($property_name))>0)
			$res = $this->GetProperty($property_name);
		else
			$res = $this->sDocTitle;
		if($strip_tags)
			return strip_tags($res);
		return $res;
	}

	function SetTitle($title, $arOptions = null)
	{
		$this->sDocTitle = $title;

		if (is_array($arOptions))
		{
			$this->sDocTitleChanger = $arOptions;
		}
		else
		{
			$arTrace = array_reverse(debug_backtrace());

			foreach ($arTrace as $key => $arTraceRes)
			{
				if (isset($arTraceRes['class']) && isset($arTraceRes['function']))
				{
					if (ToUpper($arTraceRes['class']) == 'CBITRIXCOMPONENT' && ToUpper($arTraceRes['function']) == 'INCLUDECOMPONENT' && is_object($arTraceRes['object']))
					{
						$this->sDocTitleChanger = array(
							'COMPONENT_NAME' => $arTraceRes['object']->GetName(),
						);

						break;
					}
				}
			}
		}
	}
	function ShowTitle($property_name="title", $strip_tags = true)
	{
		$this->AddBufferContent(Array(&$this, "GetTitle"), $property_name, $strip_tags);
	}

	function SetPageProperty($PROPERTY_ID, $PROPERTY_VALUE, $arOptions = null)
	{
		$this->arPageProperties[strtoupper($PROPERTY_ID)] = $PROPERTY_VALUE;

		if (is_array($arOptions))
			$this->arPagePropertiesChanger[strtoupper($PROPERTY_ID)] = $arOptions;
	}

	function GetPageProperty($PROPERTY_ID, $default_value = false)
	{
		if(isset($this->arPageProperties[strtoupper($PROPERTY_ID)]))
			return $this->arPageProperties[strtoupper($PROPERTY_ID)];
		return $default_value;
	}

	function ShowProperty($PROPERTY_ID, $default_value = false)
	{
		$this->AddBufferContent(Array(&$this, "GetProperty"), $PROPERTY_ID, $default_value);
	}

	function GetProperty($PROPERTY_ID, $default_value = false)
	{
		$propVal = $this->GetPageProperty($PROPERTY_ID);
		if($propVal !== false)
			return $propVal;

		$propVal = $this->GetDirProperty($PROPERTY_ID);
		if($propVal !== false)
			return $propVal;

		return $default_value;
	}

	function GetPagePropertyList()
	{
		return $this->arPageProperties;
	}

	function SetDirProperty($PROPERTY_ID, $PROPERTY_VALUE)
	{
		$this->arDirProperties[strtoupper($PROPERTY_ID)] = $PROPERTY_VALUE;
	}

	function InitPathVars(&$site, &$path)
	{
		$site = false;
		if(is_array($path))
		{
			$site = $path[0];
			$path = $path[1];
		}
		return $path;
	}

	function InitDirProperties($path)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($this->bDirProperties)
			return true;

		if($path===false)
			$path = $this->GetCurDir();

		$io = CBXVirtualIo::GetInstance();

		while (true) // ����� �� ����� ������
		{
			$path = rtrim($path, "/");
			$section_file_name = $DOC_ROOT.$path."/.section.php";

			if($io->FileExists($section_file_name))
			{
				$arDirProperties = false;
				include($io->GetPhysicalName($section_file_name));
				if(is_array($arDirProperties))
				{
					foreach($arDirProperties as $prid=>$prval)
					{
						$prid = strtoupper($prid);
						if(!isset($this->arDirProperties[$prid]))
							$this->arDirProperties[$prid] = $prval;
					}
				}
			}

			if(strlen($path)<=0)
				break;

			// ������ ��� ����� ��� �����
			$pos = bxstrrpos($path, "/");
			if($pos===false)
				break;

			//������ �����-��������
			$path = substr($path, 0, $pos+1);
		}

		$this->bDirProperties = true;
		return true;
	}

	function GetDirProperty($PROPERTY_ID, $path=false, $default_value = false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($path===false)
			$path = $this->GetCurDir();

		if(!$this->bDirProperties)
			$this->InitDirProperties(array($site, $path));

		if(isset($this->arDirProperties[strtoupper($PROPERTY_ID)]))
			return $this->arDirProperties[strtoupper($PROPERTY_ID)];

		return $default_value;
	}

	function GetDirPropertyList($path=false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($path===false)
			$path = $this->GetCurDir();

		if(!$this->bDirProperties)
			$this->InitDirProperties(array($site, $path));

		if(is_array($this->arDirProperties))
			return $this->arDirProperties;

		return false;
	}

	function GetMeta($id, $meta_name=false, $bXhtmlStyle=true)
	{
		if($meta_name==false)
			$meta_name=$id;
		$val = $this->GetProperty($id);
		if(!empty($val))
			return '<meta name="'.htmlspecialcharsbx($meta_name).'" content="'.htmlspecialcharsEx($val).'"'.($bXhtmlStyle? ' /':'').'>'."\n";
		return '';
	}

	function ShowBanner($type, $html_before="", $html_after="")
	{
		if(!CModule::IncludeModule("advertising"))
			return false;

		global $APPLICATION;
		$APPLICATION->AddBufferContent(Array("CAdvBanner", "Show"), $type, $html_before, $html_after);
	}

	function ShowMeta($id, $meta_name=false, $bXhtmlStyle=true)
	{
		$this->AddBufferContent(Array(&$this, "GetMeta"), $id, $meta_name, $bXhtmlStyle);
	}

	function SetAdditionalCSS($Path2css)
	{
		$this->sPath2css[] = $Path2css;
	}
	function GetAdditionalCSS()
	{
		$n = count($this->sPath2css);
		if($n > 0)
			return $this->sPath2css[$n-1];
		return false;
	}
	function GetCSSArray()
	{
		return array_unique($this->sPath2css);
	}

	function GetCSS($cMaxStylesCnt=true, $bXhtmlStyle=true)
	{
		if($cMaxStylesCnt === true)
			$cMaxStylesCnt = COption::GetOptionInt('main', 'max_css_files', 15);

		$res = '';
		$site_template = '';
		$arCSS = $this->sPath2css;
		$addTemplateStyle = true;
		$arTemplateCss = array();
		$optimizeCSS = COption::GetOptionString('main', 'optimize_css_files', 'N');

		global $USER;
		if(isset($_GET['bx_template_preview_mode']) && $_GET['bx_template_preview_mode'] == 'Y' && $USER->CanDoOperation('edit_other_settings'))
		{
			$addTemplateStyle = false;
			$path = BX_PERSONAL_ROOT."/tmp/templates/__bx_preview/";
			$arTemplateCss[] = $path."styles.css";
			$arTemplateCss[] = $path."template_styles.css";
		}
		elseif(defined("SITE_TEMPLATE_ID"))
		{
			$site_template = SITE_TEMPLATE_ID;
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID;
			$arTemplateCss[] = $path."/styles.css";
			$arTemplateCss[] = $path."/template_styles.css";
		}
		else
		{
			$site_template = '.default';
			$path = BX_PERSONAL_ROOT."/templates/.default";
			$arTemplateCss[] = $path."/styles.css";
			$arTemplateCss[] = $path."/template_styles.css";
		}

		if($optimizeCSS == 'Y')
		{
			$cssFile = array();
			$cssSrcFile = array();
			$cssMD5 = '';
			$oldCssMD5 = '';
		}
		else
		{
			$arCSS[] = $arTemplateCss[0];
			$arCSS[] = $arTemplateCss[1];
		}

		$arCSS = array_unique($arCSS);

		$isIE = IsIE();
		$cnt = 0;
		$res_content = '';
		$ruleCount = 0;
		foreach($arCSS as $css_path)
		{
			$bExternalLink = (strncmp($css_path, 'http://', 7) == 0 || strncmp($css_path, 'https://', 8) == 0);

			if(!$bExternalLink)
			{
				if(($p = strpos($css_path, "?"))>0)
					$css_file = substr($css_path, 0, $p);
				else
					$css_file = $css_path;

				$filename = $_SERVER["DOCUMENT_ROOT"].$css_file;
			}

			$bLink = ($bExternalLink || substr($css_file, -4, 4) != '.css');
			$addCSS = (strncmp($css_path, '/bitrix/themes/', 15) != 0);

			if((($cnt < $cMaxStylesCnt || ($optimizeCSS == 'Y' && $addCSS) || !$isIE) || $bLink) && strncmp($css_path, '/bitrix/modules/', 16) != 0)
			{
				if($bExternalLink || file_exists($filename))
				{
					if($res_content != '')
					{
						$res .= '<style type="text/css">'."\n".$res_content."\n</style>\n";
						$res_content = '';
					}

					if(!$bExternalLink && strpos($css_path, '?') === false)
						$css_path = CUtil::GetAdditionalFileURL($css_path, true);

					if($optimizeCSS == 'Y' && $addCSS)
					{
						$cssSrcFile[] = $css_path;
						$cssFile[] = $css_file;
					}
					else
					{
						$res .= '<link href="'.$css_path.'" type="text/css" rel="stylesheet"'.($bXhtmlStyle? ' /':'').'>'."\n";
					}
					$cnt++;
				}
			}
			elseif(!$bLink && file_exists($filename) && filesize($filename) > 0)
			{
				$contents = file_get_contents($filename);
				if($contents != '')
				{
					$contents = preg_replace('#([;\s:]+url\s*\(\s*)([^\)]+)\)#sie', "'\\1'.CMain::__ReplaceUrlCSS('\\2', '".AddSlashes($css_path)."').')'", $contents);
					if($isIE)
					{
						$c = CMain::__GetCssSelectCnt($contents);
						$ruleCount += $c;
						if($ruleCount > 4000)
						{
							$ruleCount = $c;
							if($res_content <> '')
								$res_content .= "</style>\n<style type=\"text/css\">";
						}
					}
					$res_content .= "\n".$contents."\n";
				}
			}
		}

		if($optimizeCSS == 'Y' && $cnt > 0)
		{
			$res .= CMain::__OptimizeCss($cssFile, $cssSrcFile, $site_template, $addTemplateStyle, $arTemplateCss, $bXhtmlStyle);
			unset($cssFile, $cssSrcFile, $addTemplateStyle);
		}

		if($res_content!='')
			$res .= '<style type="text/css">'."\n".$res_content."\n</style>\n";

		return $res;
	}

	function __OptimizeCss($cssFile, $cssSrcFile, $site_template, $addTemplateStyle, $arTemplateCss, $bXhtmlStyle)
	{
		$cssOptPath = BX_PERSONAL_ROOT."/cache/css/".SITE_ID.'/'.$site_template.'/';
		$cssFName = $cssOptPath.'styles.css';
		$cssFNameIE = $cssOptPath.'styles#CNT#.css';
		$cssTemplateFName = $cssOptPath.'template_styles.css';
		$cssInfoFile = BX_PERSONAL_ROOT."/managed_cache/".$GLOBALS['DB']->type.'/css/'.SITE_ID.'/'.$site_template.'_styles.php';

		$res = '';
		$upCSS = 'NO';
		$unsetKey = array();
		$arCSSInfo = array();
		$arCSSInfo['CUR_SEL_CNT'] = 0;
		$arCSSInfo['CUR_IE_CNT'] = 0;
		$arCSSInfo['CSS_MD5'] = 0;
		$maxAddCssSelect = 3950;
		$maxCssSelect = 4000;

		if($addTemplateStyle)
		{
			foreach($arTemplateCss as $css_path)
			{
				if(!$bExternalLink && strpos($css_path, '?') === false)
					$css_path = CUtil::GetAdditionalFileURL($css_path);
				$cssMD5 .= $css_path;
			}
			$cssMD5 = md5($cssMD5);
		}

		if(file_exists($_SERVER['DOCUMENT_ROOT'].$cssInfoFile))
		{
			include($_SERVER['DOCUMENT_ROOT'].$cssInfoFile);
			$oldCssMD5 = $arCSSInfo['CSS_MD5'];
			foreach($cssFile as $key => $css)
			{
				if(array_key_exists($css, $arCSSInfo['CSS']))
				{
					if($cssSrcFile[$key] != $css.'?'.$arCSSInfo['CSS'][$css])
					{
						$upCSS = 'NEW';
						break;
					}
					else
					{
						$unsetKey[] = $key;
					}
				}
				else
				{
					$upCSS = 'UP';
				}
			}
		}
		else
		{
			$upCSS = 'NEW';
		}

		if(!file_exists($_SERVER["DOCUMENT_ROOT"].$cssFName) || $upCSS == 'NEW')
		{
			$upCSS = 'NEW';
			$arCSSInfo = array();
			$arCSSInfo['CUR_SEL_CNT'] = 0;
			$arCSSInfo['CUR_IE_CNT'] = 0;
			$arCSSInfo['CSS_MD5'] = 0;
			DeleteDirFilesEx($cssOptPath);
		}

		if($upCSS != 'NO')
		{
			$contents = '';
			$arIEContent = array();

			if($upCSS == 'UP' && file_exists($_SERVER["DOCUMENT_ROOT"].$cssFName))
			{
				foreach($unsetKey as $key)
					unset($cssFile[$key], $cssSrcFile[$key]);

				$contents .= file_get_contents($_SERVER["DOCUMENT_ROOT"].$cssFName);

				if($arCSSInfo['CUR_SEL_CNT'] < $maxAddCssSelect)
				{
					$css = str_replace('#CNT#', $arCSSInfo['CUR_IE_CNT'], $cssFNameIE);
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$css))
					{
						$arIEContent[$arCSSInfo['CUR_IE_CNT']] .= file_get_contents($_SERVER["DOCUMENT_ROOT"].$css);
						$arCSSInfo['CUR_SEL_CNT'] = CMain::__GetCssSelectCnt($arIEContent[$arCSSInfo['CUR_IE_CNT']]);
					}
				}
				elseif($arCSSInfo['CUR_SEL_CNT'] >= $maxAddCssSelect)
				{
					$arCSSInfo['CUR_IE_CNT']++;
					$arCSSInfo['CUR_SEL_CNT'] = 0;
				}
			}

			foreach($cssFile as $key => $filename)
			{
				$filename = $_SERVER['DOCUMENT_ROOT'].$filename;
				$tmp_content = file_get_contents($filename);

				$f_cnt = CMain::__GetCssSelectCnt($tmp_content);
				$new_cnt = $f_cnt + $arCSSInfo['CUR_SEL_CNT'];
				$contents .= $tmp_content = "\n".preg_replace('#([;\s:]+url\s*\(\s*)([^\)]+)\)#sie', "'\\1'.CMain::__ReplaceUrlCSS('\\2', '".AddSlashes($cssSrcFile[$key])."').')'", $tmp_content)."\n";
				if($new_cnt < $maxCssSelect)
				{
					$arCSSInfo['CUR_SEL_CNT'] = $new_cnt;
					$arIEContent[$arCSSInfo['CUR_IE_CNT']] .= $tmp_content;
				}
				else
				{
					$arCSSInfo['CUR_SEL_CNT'] = $f_cnt;
					$arCSSInfo['CUR_IE_CNT']++;
					$arIEContent[$arCSSInfo['CUR_IE_CNT']] .= $tmp_content;
				}

				$arCSSInfo['CSS'][$cssFile[$key]] = CMain::__GetCssJsTime($cssSrcFile[$key]);
			}

			if(CMain::__WriteCssCache($cssFName, $contents))
			{
				$cacheInfo = '<? $arCSSInfo = array( \'CSS\' => array(';

				foreach($arCSSInfo['CSS'] as $css => $time)
					$cacheInfo .= "'".EscapePHPString($css)."' => '".intval($time)."',";

				$cacheInfo .= "), 'CUR_SEL_CNT' => '".$arCSSInfo['CUR_SEL_CNT']."', 'CUR_IE_CNT' => '".$arCSSInfo['CUR_IE_CNT']."', ";
				$cacheInfo .= "'CSS_MD5' => '".$cssMD5."'); ?>";

				CMain::__WriteCssCache($cssInfoFile, $cacheInfo, false);
			}

			foreach($arIEContent as $key => $ieContent)
			{
				$css = str_replace('#CNT#', $key, $cssFNameIE);
				CMain::__WriteCssCache($css, $ieContent);
			}
		}

		if(IsIE())
		{
			for($i = 0; $i <= $arCSSInfo['CUR_IE_CNT']; $i++)
			{
				$css = str_replace('#CNT#', $i, $cssFNameIE);
				$res .= '<link href="'.CUtil::GetAdditionalFileURL($css).'" type="text/css" rel="stylesheet"'.($bXhtmlStyle? ' /':'').'>'."\n";
			}
		}
		else
		{
			$res .= '<link href="'.CUtil::GetAdditionalFileURL($cssFName).'" type="text/css" rel="stylesheet"'.($bXhtmlStyle? ' /':'').'>'."\n";
		}

		if($addTemplateStyle)
		{
			if($cssMD5 != $oldCssMD5 || !file_exists($_SERVER['DOCUMENT_ROOT'].$cssTemplateFName))
			{
				$contents = '';
				foreach($arTemplateCss as $css)
				{
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$css))
					{
						$tmp_content = file_get_contents($_SERVER["DOCUMENT_ROOT"].$css);
						$contents .= preg_replace('#([;\s:]+url\s*\(\s*)([^\)]+)\)#sie', "'\\1'.CMain::__ReplaceUrlCSS('\\2', '".AddSlashes($css)."').')'", $tmp_content);
					}
				}
				CMain::__WriteCssCache($cssTemplateFName, $contents);
			}
			$res .= '<link href="'.$cssTemplateFName.'?'.$cssMD5.'" type="text/css" rel="stylesheet"'.($bXhtmlStyle? ' /':'').'>'."\n";
		}
		unset($content, $arIEContent, $arCSSInfo, $cacheInfo);
		return $res;
	}

	function __WriteCssCache($css_path, $contents, $gzip_content = true)
	{
		$result = false;
		CheckDirPath($_SERVER["DOCUMENT_ROOT"].$css_path);
		$handle = fopen($_SERVER["DOCUMENT_ROOT"].$css_path.'.tmp', "wb");
		$written = fwrite($handle, $contents);
		$len = function_exists('mb_strlen') ? mb_strlen($contents, 'latin1'): strlen($contents);
		fclose($handle);

		if($written === $len)
		{
			//This checks for Zend Server CE in order to supress warnings
			if(function_exists('accelerator_reset'))
				@unlink($_SERVER["DOCUMENT_ROOT"].$css_path);
			elseif(file_exists($_SERVER["DOCUMENT_ROOT"].$css_path))
				unlink($_SERVER["DOCUMENT_ROOT"].$css_path);

			rename($_SERVER["DOCUMENT_ROOT"].$css_path.'.tmp', $_SERVER["DOCUMENT_ROOT"].$css_path);
			$result = true;
		}

		if(
			extension_loaded('zlib') && function_exists("gzcompress")
			&& COption::GetOptionString('main', 'compres_css_files', 'N') == 'Y'
			&& $gzip_content
		)
		{
			$tmpGz = $_SERVER["DOCUMENT_ROOT"].$css_path.'.tmp.gz';
			$gz = gzopen($tmpGz, 'wb9f');
			$written = gzwrite ($gz, $contents);
			gzclose ($gz);
			$len = function_exists('mb_strlen') ? mb_strlen($contents, 'latin1'): strlen($contents);
			if($written === $len)
				rename($tmpGz, $_SERVER["DOCUMENT_ROOT"].$css_path.'.gz');
		}
		return $result;
	}

	function __GetCssJsTime($css_file)
	{
		$qpos = strpos($css_file, '?');
		if($qpos === false)
			return false;
		$qpos++;
		return intval(substr($css_file, $qpos));
	}

	function __GetCssSelectCnt($css)
	{
		$cnt = 0;
		$ar = explode('}', $css);
		foreach ($ar as $key => $val)
		{
			$ar[$key] = explode('{', $val);
			$cnt += count(explode(',', $ar[$key][0]));
		}
		unset($ar);
		return $cnt;
	}

	function __ReplaceUrlCSS($url, $cssPath)
	{
		if(strpos($url, "://") !== false || strpos($url, "data:") !== false)
			return $url;
		$url = trim(stripslashes($url), "'\" \r\n\t");
		if(substr($url, 0, 1) == "/")
			return $url;
		$cssPath = dirname($cssPath);
		return "'".$cssPath.'/'.$url."'";
	}

	function ShowCSS($cMaxStylesCnt=true, $bXhtmlStyle=true)
	{
		$this->AddBufferContent(Array(&$this, "GetCSS"), $cMaxStylesCnt, $bXhtmlStyle);
	}

	function AddHeadString($str, $bUnique=false)
	{
		if($str <> '')
		{
			if($bUnique)
			{
				$check_sum = md5($str);
				if(!array_key_exists($check_sum, $this->arHeadStrings))
					$this->arHeadStrings[$check_sum] = $str;
			}
			else
				$this->arHeadStrings[] = $str;
		}
	}
	function GetHeadStrings()
	{
		return implode("\n", $this->arHeadStrings)."\n";
	}
	function ShowHeadStrings()
	{
		$this->AddBufferContent(Array(&$this, "GetHeadStrings"));
	}

	function AddHeadScript($src)
	{
		if($src <> '')
			$this->arHeadScripts[] = $src;
	}
	function GetHeadScripts()
	{
		$arScripts = array_unique($this->arHeadScripts);
		$res = "";
		foreach($arScripts as $src)
		{
			if(strncmp($src, 'http://', 7) !== 0 && strncmp($src, 'https://', 8) !== 0)
				if(strpos($src, '?') === false)
					$src = CUtil::GetAdditionalFileURL($src);
			$res .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
		}
		return $res;
	}
	function ShowHeadScripts()
	{
		$this->AddBufferContent(array(&$this, "GetHeadScripts"));
	}

	function ShowHead($bXhtmlStyle=true)
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.($bXhtmlStyle? ' /':'').'>'."\n";
		$this->ShowMeta("robots", false, $bXhtmlStyle);
		$this->ShowMeta("keywords", false, $bXhtmlStyle);
		$this->ShowMeta("description", false, $bXhtmlStyle);
		$this->ShowCSS(true, $bXhtmlStyle);
		$this->ShowHeadStrings();
		$this->ShowHeadScripts();
	}

	function SetShowIncludeAreas($bShow=true)
	{
		$_SESSION["SESS_INCLUDE_AREAS"] = $bShow;
	}

	function GetShowIncludeAreas()
	{
		if(!$GLOBALS["USER"]->IsAuthorized())
			return false;
		if($_SESSION["SESS_INCLUDE_AREAS"])
			return true;
		$aUserOpt = CUserOptions::GetOption("global", "settings", array());
		return ($aUserOpt["panel_dynamic_mode"] == "Y");
	}

	function SetPublicShowMode($mode)
	{
		$this->SetShowIncludeAreas($mode != 'view');
	}

	function GetPublicShowMode()
	{
		return $this->GetShowIncludeAreas() ? 'configure' : 'view';
	}

	function SetEditArea($areaId, $arIcons)
	{
		if(!$this->GetShowIncludeAreas())
			return;

		if($this->editArea === false)
			$this->editArea = new CEditArea();

		$this->editArea->SetEditArea($areaId, $arIcons);
	}

	function IncludeStringBefore()
	{
		if($this->editArea === false)
			$this->editArea = new CEditArea();
		return $this->editArea->IncludeStringBefore();
	}

	function IncludeStringAfter($arIcons=false, $arParams=array())
	{
		return $this->editArea->IncludeStringAfter($arIcons, $arParams);
	}

	function IncludeString($string, $arIcons=false)
	{
		return $this->IncludeStringBefore().$string.$this->IncludeStringAfter($arIcons);
	}

	function GetTemplatePath($rel_path)
	{
		if(substr($rel_path, 0, 1)!="/")
		{
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				return $path;

			$path = BX_PERSONAL_ROOT."/templates/.default/".$rel_path;
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				return $path;

			$module_id = substr($rel_path, 0, strpos($rel_path, "/"));
			if(strlen($module_id)>0)
			{
				$path = "/bitrix/modules/".$module_id."/install/templates/".$rel_path;
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
					return $path;
			}

			return false;
		}

		return $rel_path;
	}

	function SetTemplateCSS($rel_path)
	{
		if($path = $this->GetTemplatePath($rel_path))
			$this->SetAdditionalCSS($path);
	}

	// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	// COMPONENTS 2.0 >>>>>
	function IncludeComponent($componentName, $componentTemplate, $arParams = array(), $parentComponent = null, $arFunctionParams = array())
	{
		global $APPLICATION, $USER;

		if(is_array($this->arComponentMatch))
		{
			$skipComponent = true;
			foreach($this->arComponentMatch as $cIndex => $cValue)
			{
				if(strpos($componentName, $cValue) !== false)
				{
					$skipComponent = false;
					break;
				}
			}
			if($skipComponent)
				return false;
		}

		$componentRelativePath = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($componentRelativePath) <= 0)
			return False;

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}
		elseif($APPLICATION->ShowIncludeStat)
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}
		else
		{
			$debug = null;
		}

		if (is_object($parentComponent))
		{
			if (!($parentComponent instanceof cbitrixcomponent))
				$parentComponent = null;
		}

		$bDrawIcons = ((!isset($arFunctionParams["HIDE_ICONS"]) || $arFunctionParams["HIDE_ICONS"] <> "Y") && $APPLICATION->GetShowIncludeAreas());

		if($bDrawIcons)
			echo $this->IncludeStringBefore();

		$result = null;
		$bComponentEnabled = (!isset($arFunctionParams["ACTIVE_COMPONENT"]) || $arFunctionParams["ACTIVE_COMPONENT"] <> "N");

		$component = new CBitrixComponent();
		if($component->InitComponent($componentName) && $bComponentEnabled)
		{
			if($arParams['AJAX_MODE'] == 'Y')
				$obAjax = new CComponentAjax($componentName, $componentTemplate, $arParams, $parentComponent);

			$result = $component->IncludeComponent($componentTemplate, $arParams, $parentComponent);

			if($arParams['AJAX_MODE'] == 'Y')
				$obAjax->Process();
		}

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
			echo $debug->Output($componentName, "/bitrix/components".$componentRelativePath."/component.php", $arParams["CACHE_TYPE"].$arParams["MENU_CACHE_TYPE"]);
		elseif(is_object($debug))
			$debug->Stop($componentName, "/bitrix/components".$componentRelativePath."/component.php", $arParams["CACHE_TYPE"].$arParams["MENU_CACHE_TYPE"]);

		if($bDrawIcons)
		{
			$panel = new CComponentPanel($component, $componentName, $componentTemplate, $parentComponent, $bComponentEnabled);
			$arIcons = $panel->GetIcons();

			echo $this->IncludeStringAfter($arIcons["icons"], $arIcons["parameters"]);
		}

		return $result;
	}

	function AddViewContent($view, $content, $pos = 500)
	{
		if(!is_array($this->__view[$view]))
			$this->__view[$view] = array(array($content, $pos));
		else
			$this->__view[$view][] = array($content, $pos);
	}

	function ShowViewContent($view)
	{
		$this->AddBufferContent(Array(&$this, "GetViewContent"), $view);
	}

	function GetViewContent($view)
	{
		if(!is_array($this->__view[$view]))
			return '';

		uasort($this->__view[$view], create_function('$a, $b', 'if($a[1] == $b[1]) return 0; return ($a[1] < $b[1])? -1 : 1;'));

		$res = array();
		foreach($this->__view[$view] as $item)
			$res[] = $item[0];

		return implode($res);
	}

	function OnChangeFileComponent($path, $site)
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/php_parser.php");

		global $APPLICATION;

		$docRoot = CSite::GetSiteDocRoot($site);

		CUrlRewriter::Delete(
			array("SITE_ID" => $site, "PATH" => $path, "ID" => "NULL")
		);

		$fileSrc = $APPLICATION->GetFileContent($docRoot.$path);
		$arComponents = PHPParser::ParseScript($fileSrc);
		for ($i = 0, $cnt = count($arComponents); $i < $cnt; $i++)
		{
			if (isset($arComponents[$i]["DATA"]["PARAMS"]) && is_array($arComponents[$i]["DATA"]["PARAMS"]))
			{
				if (array_key_exists("SEF_MODE", $arComponents[$i]["DATA"]["PARAMS"])
					&& $arComponents[$i]["DATA"]["PARAMS"]["SEF_MODE"] == "Y")
				{
					CUrlRewriter::Add(
						array(
							"SITE_ID" => $site,
							"CONDITION" => "#^".$arComponents[$i]["DATA"]["PARAMS"]["SEF_FOLDER"]."#",
							"ID" => $arComponents[$i]["DATA"]["COMPONENT_NAME"],
							"PATH" => $path
						)
					);
				}
			}
		}
	}
	// <<<<< COMPONENTS 2.0
	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

	// $arParams - �� ��������������� !
	function IncludeFile($rel_path, $arParams = Array(), $arFunctionParams = Array())
	{
		global $APPLICATION, $USER, $DB, $MESS, $DOCUMENT_ROOT;

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}
		elseif($APPLICATION->ShowIncludeStat)
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}
		else
		{
			$debug = null;
		}

		$sType = "TEMPLATE";
		$bComponent = false;
		if(substr($rel_path, 0, 1)!="/")
		{
			$bComponent = true;
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
			if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
			{
				$sType = "DEFAULT";
				$path = BX_PERSONAL_ROOT."/templates/.default/".$rel_path;
				if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				{
					$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
					$module_id = substr($rel_path, 0, strpos($rel_path, "/"));
					if(strlen($module_id)>0)
					{
						$path = "/bitrix/modules/".$module_id."/install/templates/".$rel_path;
						$sType = "MODULE";
						if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
						{
							$sType = "TEMPLATE";
							$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
						}
					}
				}
			}
		}
		else
			$path = $rel_path;

		if($arFunctionParams["WORKFLOW"] && !IsModuleInstalled("workflow"))
			$arFunctionParams["WORKFLOW"] = false;
		elseif($sType!="TEMPLATE" && $arFunctionParams["WORKFLOW"])
			$arFunctionParams["WORKFLOW"] = false;

		$bDrawIcons = (
			$arFunctionParams["SHOW_BORDER"] !== false && $APPLICATION->GetShowIncludeAreas()
			&& (
				$USER->CanDoFileOperation('fm_edit_existent_file', array(SITE_ID, $path))
				|| ($arFunctionParams["WORKFLOW"] && $USER->CanDoFileOperation('fm_edit_in_workflow', array(SITE_ID, $path)))
			)
		);

		if($bDrawIcons)
		{
			$path_url = "path=".$path;
			$encSiteTemplateId = urlencode(SITE_TEMPLATE_ID);
			$editor = '';

			if (!in_array($arFunctionParams['MODE'], array('html', 'text', 'php')))
			{
				$arFunctionParams['MODE'] = $bComponent ? 'php' : 'html';
			}

			if ($sType != 'TEMPLATE')
			{
				switch ($arFunctionParams['MODE'])
				{
					case 'html':
						$editor = "/bitrix/admin/fileman_html_edit.php?site=".SITE_ID."&";
						break;
					case 'text':
						$editor = "/bitrix/admin/fileman_file_edit.php?site=".SITE_ID."&";
						break;
					case 'php':
						$editor = "/bitrix/admin/fileman_file_edit.php?full_src=Y&site=".SITE_ID."&";
						break;
				}
				$editor .= "templateID=".$encSiteTemplateId."&";
			}
			else
			{
				switch ($arFunctionParams['MODE'])
				{
					case 'html':
						$editor = '/bitrix/admin/public_file_edit.php?bxpublic=Y&from=includefile&templateID='.$encSiteTemplateId.'&';
						$resize = 'false';
						break;

					case 'text':
						$editor = '/bitrix/admin/public_file_edit.php?bxpublic=Y&from=includefile&noeditor=Y&';
						$resize = 'true';
						break;

					case 'php':
						$editor = '/bitrix/admin/public_file_edit_src.php?templateID='.$encSiteTemplateId.'&';
						$resize = 'true';
						break;
				}
			}

			if($arFunctionParams["TEMPLATE"])
				$arFunctionParams["TEMPLATE"] = "&template=".urlencode($arFunctionParams["TEMPLATE"]);

			if($arFunctionParams["BACK_URL"])
				$arFunctionParams["BACK_URL"] = "&back_url=".urlencode($arFunctionParams["BACK_URL"]);
			else
				$arFunctionParams["BACK_URL"] = "&back_url=".urlencode($_SERVER["REQUEST_URI"]);

			if($arFunctionParams["LANG"])
				$arFunctionParams["LANG"] = "&lang=".urlencode($arFunctionParams["LANG"]);
			else
				$arFunctionParams["LANG"] = "&lang=".LANGUAGE_ID;

			$arIcons = array();
			$arPanelParams = array();

			$bDefaultExists = false;
			if($USER->CanDoOperation('edit_php') && $bComponent && function_exists("debug_backtrace"))
			{
				$bDefaultExists = true;
				$arPanelParams["TOOLTIP"] = array(
					'TITLE' => GetMessage("main_incl_component1"),
					'TEXT' => $rel_path
				);

				$aTrace = debug_backtrace();

				$sSrcFile = $aTrace[0]["file"];
				$iSrcLine = intval($aTrace[0]["line"]);
				$arIcons[] = array(
					'URL' => 'javascript:'.$APPLICATION->GetPopupLink(array(
						'URL' => "/bitrix/admin/component_props.php?".
							"path=".urlencode(CUtil::addslashes($rel_path)).
							"&template_id=".urlencode(CUtil::addslashes(SITE_TEMPLATE_ID)).
							"&lang=".LANGUAGE_ID.
							"&src_path=".urlencode(CUtil::addslashes($sSrcFile)).
							"&src_line=".$iSrcLine.
							""
					)),
					'ICON'=>"parameters",
					'TITLE'=>GetMessage("main_incl_file_comp_param"),
					'DEFAULT'=>true
				);
			}

			if($sType == "MODULE")
			{
				$arIcons[] = Array(
					'URL'=>'javascript:if(confirm(\''.GetMessage("MAIN_INC_BLOCK_MODULE").'\'))window.location=\''.$editor.'&path='.urlencode(BX_PERSONAL_ROOT.'/templates/'.SITE_TEMPLATE_ID.'/'.$rel_path).$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].'&template='.$path.'\';',
					'ICON'=>'copy',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("main_incl_file_edit_copy")))
				);
			}
			elseif($sType == "DEFAULT")
			{
				$arIcons[] = Array(
					'URL'=>'javascript:if(confirm(\''.GetMessage("MAIN_INC_BLOCK_COMMON").'\'))window.location=\''.$editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"].'\';',
					'ICON'=>'edit-common',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_BLOCK_EDIT")))
				);

				$arIcons[] = Array(
					'URL'=>$editor.'&path='.urlencode(BX_PERSONAL_ROOT.'/templates/'.SITE_TEMPLATE_ID.'/'.$rel_path).$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].'&template='.$path,
					'ICON'=>'copy',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_BLOCK_COMMON_COPY")))
				);
			}
			else
			{
				$arPanelParams["TOOLTIP"] = array(
					'TITLE' => GetMessage('main_incl_file'),
					'TEXT' => $path
				);

				$arIcons[] = Array(
					'URL' => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => $editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"],
							"PARAMS" => array(
								'width' => 770,
								'height' => 470,
								'resize' => $resize
							)
						)
					),
					//'URL'=>'javascript:jsPopup.ShowDialog(\''.$editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"].'\', {width: 770, height: 570, resize: '.$resize.'})',
					'ICON'=>'bx-context-toolbar-edit-icon',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK") : $arFunctionParams["NAME"]), GetMessage("MAIN_INC_ED"))),
					'DEFAULT'=>!$bDefaultExists
				);

				if($arFunctionParams["WORKFLOW"])
				{
					$arIcons[] = Array(
						'URL'=>'/bitrix/admin/workflow_edit.php?'.$arFunctionParams["LANG"].'&fname='.urlencode($path).$arFunctionParams["TEMPLATE"].$arFunctionParams["BACK_URL"],
						'ICON'=>'bx-context-toolbar-edit-icon',
						'TITLE'=>str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_ED_WF"))
					);
				}
			}

			echo $this->IncludeStringBefore();
		}

		$res = null;
		if(is_file($_SERVER["DOCUMENT_ROOT"].$path))
		{
			if(is_array($arParams))
				extract($arParams, EXTR_SKIP);

			$res = include($_SERVER["DOCUMENT_ROOT"].$path);
		}

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
			echo $debug->Output($rel_path, $path);
		elseif(is_object($debug))
			$debug->Stop($rel_path, $path);

		if($bDrawIcons)
		{
			$comp_id = $path;
			if ($sSrcFile) $comp_id .= '|'.$sSrcFile;
			if ($iSrcLine) $comp_id .= '|'.$iSrcLine;

			$arPanelParams['COMPONENT_ID'] = md5($comp_id);
			echo $this->IncludeStringAfter($arIcons, $arPanelParams);
		}

		return $res;
	}

	function AddChainItem($title, $link="", $bUnQuote=true)
	{
		if($bUnQuote)
			$title = str_replace(array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), array("&", "\"", "'", "<", ">"), $title);
		$this->arAdditionalChain[] = array("TITLE"=>$title, "LINK"=>htmlspecialcharsbx($link));
	}

	function GetNavChain($path=false, $iNumFrom=0, $sNavChainPath=false, $bIncludeOnce=false, $bShowIcons = true)
	{
		global $APPLICATION;
		if($APPLICATION->GetProperty("NOT_SHOW_NAV_CHAIN")=="Y")
			return "";

		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($path===false)
			$path = $this->GetCurDir();

		$arChain = Array();
		$strChainTemplate = $DOC_ROOT.BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/chain_template.php";
		if(!file_exists($strChainTemplate))
			$strChainTemplate = $DOC_ROOT.BX_PERSONAL_ROOT."/templates/.default/chain_template.php";
		$i = -1;

		$io = CBXVirtualIo::GetInstance();

		while(true)//����� �� ����� ������
		{
			//������� / � �����
			$path = rtrim($path, "/");

			$chain_file_name = $DOC_ROOT.$path."/.section.php";
			$section_template_init = false;
			if($io->FileExists($chain_file_name))
			{
				$sChainTemplate = "";
				$sSectionName = "";
				include($io->GetPhysicalName($chain_file_name));
				if(strlen($sSectionName)>0)
					$arChain[] = Array("TITLE"=>$sSectionName, "LINK"=>$path."/");
				if(strlen($sChainTemplate)>0 && !$section_template_init)
				{
					$section_template_init = true;
					$strChainTemplate = $sChainTemplate;
				}
			}

			if($path.'/' == SITE_DIR)
				break;

			if(strlen($path)<=0)
				break;

			//������ ��� ����� ��� �����
			$pos = bxstrrpos($path, "/");
			if($pos===false)
				break;

			//������ �����-��������
			$path = substr($path, 0, $pos+1);
		}

		if($sNavChainPath!==false)
			$strChainTemplate = $DOC_ROOT.$sNavChainPath;

		$arChain = array_reverse($arChain);
		$arChain = array_merge($arChain, $this->arAdditionalChain);
		if($iNumFrom>0)
			$arChain = array_slice($arChain, $iNumFrom);

		return $this->_mkchain($arChain, $strChainTemplate, $bIncludeOnce, $bShowIcons);
	}

	function _mkchain($arChain, $strChainTemplate, $bIncludeOnce=false, $bShowIcons = true)
	{
		$strChain = $sChainProlog = $sChainEpilog = "";
		if(file_exists($strChainTemplate))
		{
			$ITEM_COUNT = count($arChain);
			$arCHAIN = $arChain;
			$arCHAIN_LINK = &$arChain;
			$arResult = &$arChain; // for component 2.0
			if($bIncludeOnce)
			{
				$strChain = include($strChainTemplate);
			}
			else
			{
				for($i=0; $i<count($arChain); $i++)
				{
					$ITEM_INDEX = $i;
					$TITLE = $arChain[$i]["TITLE"];
					$LINK = $arChain[$i]["LINK"];
					$sChainBody = "";
					include($strChainTemplate);
					$strChain .= $sChainBody;
					if($i==0)
						$strChain = $sChainProlog . $strChain;
				}
				if(count($arChain)>0)
					$strChain .= $sChainEpilog;
			}
		}

		global $APPLICATION, $USER;
		if($APPLICATION->GetShowIncludeAreas() && $USER->CanDoOperation('edit_php') && $bShowIcons)
		{
			$site = CSite::GetSiteByFullPath($strChainTemplate);
			$DOC_ROOT = CSite::GetSiteDocRoot($site);

			if(strpos($strChainTemplate, $DOC_ROOT)===0)
			{
				$path = substr($strChainTemplate, strlen($DOC_ROOT));

				$templ_perm = $APPLICATION->GetFileAccessPermission($path);
				if((!defined("ADMIN_SECTION") || ADMIN_SECTION!==true) && $templ_perm>="W")
				{
					$arIcons = Array();
					$arIcons[] = Array(
						"URL"=>"/bitrix/admin/fileman_file_edit.php?lang=".LANGUAGE_ID."&site=".$site."&back_url=".urlencode($_SERVER["REQUEST_URI"])."&full_src=Y&path=".urlencode($path),
						"ICON"=>"nav-template",
						"TITLE"=>GetMessage("MAIN_INC_ED_NAV")
					);

					$strChain = $APPLICATION->IncludeString($strChain, $arIcons);
				}
			}
		}
		return $strChain;
	}

	function ShowNavChain($path=false, $iNumFrom=0, $sNavChainPath=false)
	{
		$this->AddBufferContent(Array(&$this, "GetNavChain"), $path, $iNumFrom, $sNavChainPath);
	}

	function ShowNavChainEx($path=false, $iNumFrom=0, $sNavChainPath=false)
	{
		$this->AddBufferContent(Array(&$this, "GetNavChain"), $path, $iNumFrom, $sNavChainPath, true);
	}

	/*****************************************************/

	function SetFileAccessPermission($path, $arPermissions, $bOverWrite=true)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		$path = rtrim($path, "/");
		if($path == '')
			$path = "/";

		if(($p = bxstrrpos($path, "/")) !== false)
		{
			$path_file = substr($path, $p+1);
			$path_dir = substr($path, 0, $p);
		}
		else
			return false;

		if($path_file == "" && $path_dir == "")
			$path_file = "/";

		$PERM = array();

		$io = CBXVirtualIo::GetInstance();
		if ($io->FileExists($DOC_ROOT.$path_dir."/.access.php"))
		{
			$fTmp = $io->GetFile($DOC_ROOT.$path_dir."/.access.php");
			//include replaced with eval in order to honor of ZendServer
			eval("?>".$fTmp->GetContents());
		}

		$FILE_PERM = $PERM[$path_file];
		if(!is_array($FILE_PERM))
			$FILE_PERM = array();

		if(!$bOverWrite && count($FILE_PERM)>0)
			return true;

		$bDiff = false;

		$str="<?\n";
		foreach($arPermissions as $group=>$perm)
		{
			if(strlen($perm) > 0)
				$str .= "\$PERM[\"".EscapePHPString($path_file)."\"][\"".EscapePHPString($group)."\"]=\"".EscapePHPString($perm)."\";\n";

			if(!$bDiff)
			{
				//compatibility with group id
				$curr_perm = $FILE_PERM[$group];
				if(!isset($curr_perm) && preg_match('/^G[0-9]+$/', $group))
					$curr_perm = $FILE_PERM[substr($group, 1)];

				if($curr_perm != $perm)
					$bDiff = true;
			}
		}

		foreach($PERM as $file=>$arPerm)
		{
			if(strval($file) !== $path_file)
				foreach($arPerm as $group=>$perm)
					$str .= "\$PERM[\"".EscapePHPString($file)."\"][\"".EscapePHPString($group)."\"]=\"".EscapePHPString($perm)."\";\n";
		}

		if(!$bDiff)
		{
			foreach($FILE_PERM as $group=>$perm)
			{
				//compatibility with group id
				$new_perm = $arPermissions[$group];
				if(!isset($new_perm) && preg_match('/^G[0-9]+$/', $group))
					$new_perm = $arPermissions[substr($group, 1)];

				if($new_perm != $perm)
				{
					$bDiff = true;
					break;
				}
			}
		}

		$str .= "?".">";

		$this->SaveFileContent($DOC_ROOT.$path_dir."/.access.php", $str);
		$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
		unset($this->FILE_PERMISSION_CACHE[$site."|".$path_dir."/.access.php"]);

		if($bDiff)
		{
			$db_events = GetModuleEvents("main", "OnChangePermissions");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent, array(array($site, $path), $arPermissions, $FILE_PERM));

			if(COption::GetOptionString("main", "event_log_file_access", "N") === "Y")
				CEventLog::Log("SECURITY", "FILE_PERMISSION_CHANGED", "main", "[".$site."] ".$path, print_r($FILE_PERM, true)." => ".print_r($arPermissions, true));
		}
		return true;
	}

	function RemoveFileAccessPermission($path, $arGroups=false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		$path = rtrim($path, "/");
		if($path == '')
			$path = "/";

		if(($p = bxstrrpos($path, "/")) !== false)
		{
			$path_file = substr($path, $p+1);
			$path_dir = substr($path, 0, $p);
		}
		else
			return false;

		$PERM = array();
		$io = CBXVirtualIo::GetInstance();
		if (!$io->FileExists($DOC_ROOT.$path_dir."/.access.php"))
			return true;

		include($io->GetPhysicalName($DOC_ROOT.$path_dir."/.access.php"));

		$str = "<?\n";
		foreach($PERM as $file=>$arPerm)
		{
			if($file != $path_file || $arGroups !== false)
			{
				foreach($arPerm as $group=>$perm)
				{
					if($arGroups !== false)
					{
						//compatibility with group id
						$bExists = false;
						if(in_array($group, $arGroups))
							$bExists = true;
						elseif(preg_match('/^G[0-9]+$/', $group) && in_array(substr($group, 1), $arGroups))
							$bExists = true;
						elseif(preg_match('/^[0-9]+$/', $group) && in_array('G'.$group, $arGroups))
							$bExists = true;
					}
					if($file != $path_file || ($arGroups !== false && !$bExists))
						$str .= "\$PERM[\"".EscapePHPString($file)."\"][\"".EscapePHPString($group)."\"]=\"".EscapePHPString($perm)."\";\n";
				}
			}
		}

		$str .= "?".">";

		$this->SaveFileContent($DOC_ROOT.$path_dir."/.access.php", $str);
		$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
		unset($this->FILE_PERMISSION_CACHE[$site."|".$path_dir."/.access.php"]);

		$db_events = GetModuleEvents("main", "OnChangePermissions");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array(array($site, $path), array()));

		return true;
	}

	function CopyFileAccessPermission($path_from, $path_to, $bOverWrite=false)
	{
		CMain::InitPathVars($site_from, $path_from);
		$DOC_ROOT_FROM = CSite::GetSiteDocRoot($site_from);

		CMain::InitPathVars($site_to, $path_to);
		$DOC_ROOT_TO = CSite::GetSiteDocRoot($site_to);

		//������� ����������� .access.php
		if(($p = bxstrrpos($path_from, "/"))!==false)
		{
			$path_from_file = substr($path_from, $p+1);
			$path_from_dir = substr($path_from, 0, $p);
		}
		else
			return false;

		$PERM = array();

		$io = CBXVirtualIo::GetInstance();
		if (!$io->FileExists($DOC_ROOT_FROM.$path_from_dir."/.access.php"))
			return true;

		include($io->GetPhysicalName($DOC_ROOT_FROM.$path_from_dir."/.access.php"));

		$FILE_PERM = $PERM[$path_from_file];
		if(count($FILE_PERM)>0)
			return $this->SetFileAccessPermission(array($site_to, $path_to), $FILE_PERM, $bOverWrite);

		return true;
	}


	function GetFileAccessPermission($path, $groups=false, $task_mode=false) // task_mode - new access mode
	{
		global $USER;

		if($groups === false)
		{
			if(!is_object($USER))
				$groups = array('G2');
			else
				$groups = $USER->GetAccessCodes();
		}
		elseif(is_array($groups) && !empty($groups))
		{
			//compatibility with user groups id
			$bNumbers = preg_match('/^[0-9]+$/', $groups[0]);
			if($bNumbers)
				foreach($groups as $key=>$val)
					$groups[$key] = "G".$val;
		}

		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		//windows files are case-insensitive
		$bWin = (strncasecmp(PHP_OS, "WIN", 3) == 0);
		if($bWin)
			$path = strtolower($path);

		if(trim($path, "/") != "")
		{
			$path = Rel2Abs("/", $path);
			if($path == "")
				return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));
		}

		if(COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y")
			$bAdminM = (is_object($USER)? $USER->IsAdmin() : false);
		else
			$bAdminM = in_array("G1", $groups);

		if($bAdminM)
			return (!$task_mode? 'X' : array(CTask::GetIdByLetter('X', 'main', 'file')));

		if(substr($path, -12) == "/.access.php" && !$bAdminM)
			return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));

		if(substr($path, -10) == "/.htaccess" && !$bAdminM)
			return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));

		$max_perm = "D";
		$arGroupTask = Array();

		$io = CBXVirtualIo::GetInstance();

		//� ������ ����� ������� * === "����� ������"
		$groups[] = "*";
		while(true)//����� �� ����� ������
		{
			$path = rtrim($path, "\0");
			$path = rtrim($path, "/");

			if($path == '')
			{
				$access_file_name="/.access.php";
				$Dir = "/";
			}
			else
			{
				//������ ��� ����� ��� �����
				$pos = strrpos($path, "/");
				if($pos === false)
					break;
				$Dir = substr($path, $pos+1);

				//security fix: under Windows "my." == "my"
				$Dir = TrimUnsafe($Dir);

				//������ �����-��������
				$path = substr($path, 0, $pos+1);

				$access_file_name=$path.".access.php";
			}

			if(array_key_exists($site."|".$access_file_name, $this->FILE_PERMISSION_CACHE))
			{
				$PERM = $this->FILE_PERMISSION_CACHE[$site."|".$access_file_name];
			}
			else
			{
				$PERM = array();

				//��������� ���� � ������� ���� �� ����
				if ($io->FileExists($DOC_ROOT.$access_file_name))
					include($io->GetPhysicalName($DOC_ROOT.$access_file_name));

				//windows files are case-insensitive
				if($bWin && !empty($PERM))
				{
					$PERM_TMP = array();
					foreach($PERM as $key => $val)
						$PERM_TMP[strtolower($key)] = $val;
					$PERM = $PERM_TMP;
				}

				$this->FILE_PERMISSION_CACHE[$site."|".$access_file_name] = $PERM;
			}

			//�������� - ������ �� ����� �� ���� ����/����� ��� ������ ����� � ���� �����
			$dir_perm = $PERM[$Dir];

			if(is_array($dir_perm))
			{
				foreach($groups as $key => $group_id)
				{
					$perm = $dir_perm[$group_id];

					//compatibility with group id
					if(!isset($perm) && preg_match('/^G[0-9]+$/', $group_id))
						$perm = $dir_perm[substr($group_id, 1)];

					if(isset($perm))
					{
						if ($task_mode)
						{
							if(substr($perm, 0, 2) == 'T_')
								$tid = intval(substr($perm, 2));
							elseif(($tid = CTask::GetIdByLetter($perm, 'main', 'file')) === false)
								continue;

							$arGroupTask[$group_id] = $tid;
						}
						else
						{
							if(substr($perm, 0, 2) == 'T_')
							{
								$tid = intval(substr($perm, 2));
								$perm = CTask::GetLetter($tid);
								if(strlen($perm) == 0)
									$perm = 'D';
							}

							if($max_perm == "" || $perm > $max_perm)
							{
								$max_perm = $perm;
								if($perm == "W")
									break 2;
							}
						}

						if($group_id == "*")
							break 2;

						//������ ��� ������ �� ������, �.�. �� ��� ����� ��� ��� �����
						unset($groups[$key]);

						if(count($groups) == 1 && in_array("*", $groups))
							break 2;
					}
				}

				if(count($groups)<=1)
					break;
			}

			if($path == '')
				break;
		}

		if($task_mode)
		{
			$arTasks = array_unique(array_values($arGroupTask));
			if(empty($arTasks))
				return array(CTask::GetIdByLetter('D', 'main', 'file'));
			sort($arTasks);
			return $arTasks;
		}
		else
			return $max_perm;
	}

	function GetFileAccessPermissionByUser($intUserID, $path, $groups=false, $task_mode=false) // task_mode - new access mode
	{
		global $USER;

		$intUserIDTmp = intval($intUserID);
		if ($intUserIDTmp.'|' != $intUserID.'|')
			return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));
		$intUserID = $intUserIDTmp;

		if ($groups === false)
		{
			$groups = CUser::GetUserGroup($intUserID);
			foreach ($groups as $key=>$val)
				$groups[$key] = "G".$val;
		}
		elseif (is_array($groups) && !empty($groups))
		{
			$bNumbers = preg_match('/^[0-9]+$/', $groups[0]);
			if($bNumbers)
				foreach($groups as $key=>$val)
					$groups[$key] = "G".$val;
		}

		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		$bWin = (strncasecmp(PHP_OS, "WIN", 3) == 0);
		if ($bWin)
			$path = strtolower($path);

		if (trim($path, "/") != "")
		{
			$path = Rel2Abs("/", $path);
			if($path == "")
				return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));
		}

		$bAdminM = in_array("G1", $groups);

		if ($bAdminM)
			return (!$task_mode? 'X' : array(CTask::GetIdByLetter('X', 'main', 'file')));

		if (substr($path, -12) == "/.access.php" && !$bAdminM)
			return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));

		if (substr($path, -10) == "/.htaccess" && !$bAdminM)
			return (!$task_mode? 'D' : array(CTask::GetIdByLetter('D', 'main', 'file')));

		$max_perm = "D";
		$arGroupTask = array();

		$io = CBXVirtualIo::GetInstance();

		$groups[] = "*";
		while (true)
		{
			$path = rtrim($path, "\0");
			$path = rtrim($path, "/");

			if ($path == '')
			{
				$access_file_name="/.access.php";
				$Dir = "/";
			}
			else
			{
				$pos = strrpos($path, "/");
				if ($pos === false)
					break;
				$Dir = substr($path, $pos+1);

				$Dir = TrimUnsafe($Dir);

				$path = substr($path, 0, $pos+1);

				$access_file_name=$path.".access.php";
			}

			if (array_key_exists($site."|".$access_file_name, $this->FILE_PERMISSION_CACHE))
			{
				$PERM = $this->FILE_PERMISSION_CACHE[$site."|".$access_file_name];
			}
			else
			{
				$PERM = array();

				if ($io->FileExists($DOC_ROOT.$access_file_name))
					include($io->GetPhysicalName($DOC_ROOT.$access_file_name));

				if ($bWin && !empty($PERM))
				{
					$PERM_TMP = array();
					foreach($PERM as $key => $val)
						$PERM_TMP[strtolower($key)] = $val;
					$PERM = $PERM_TMP;
				}

				$this->FILE_PERMISSION_CACHE[$site."|".$access_file_name] = $PERM;
			}

			$dir_perm = $PERM[$Dir];

			if (is_array($dir_perm))
			{
				foreach ($groups as $key => $group_id)
				{
					$perm = $dir_perm[$group_id];

					if (!isset($perm) && preg_match('/^G[0-9]+$/', $group_id))
						$perm = $dir_perm[substr($group_id, 1)];

					if (isset($perm))
					{
						if ($task_mode)
						{
							if(substr($perm, 0, 2) == 'T_')
								$tid = intval(substr($perm, 2));
							elseif(($tid = CTask::GetIdByLetter($perm, 'main', 'file')) === false)
								continue;

							$arGroupTask[$group_id] = $tid;
						}
						else
						{
							if(substr($perm, 0, 2) == 'T_')
							{
								$tid = intval(substr($perm, 2));
								$perm = CTask::GetLetter($tid);
								if(strlen($perm) == 0)
									$perm = 'D';
							}

							if ($max_perm == "" || $perm > $max_perm)
							{
								$max_perm = $perm;
								if($perm == "W")
									break 2;
							}
						}

						if($group_id == "*")
							break 2;

						unset ($groups[$key]);

						if (count($groups) == 1 && in_array("*", $groups))
							break 2;
					}
				}

				if (count($groups)<=1)
					break;
			}

			if($path == '')
				break;
		}

		if ($task_mode)
		{
			$arTasks = array_unique(array_values($arGroupTask));
			if(empty($arTasks))
				return array(CTask::GetIdByLetter('D', 'main', 'file'));
			sort($arTasks);
			return $arTasks;
		}
		else
			return $max_perm;
	}
	/***********************************************/

	function SaveFileContent($abs_path, $strContent)
	{
		$strContent = str_replace("\r\n", "\n", $strContent);

		$aMsg = array();
		$file = array();
		$this->ResetException();

		$db_events = GetModuleEvents("main", "OnBeforeChangeFile");
		while($arEvent = $db_events->Fetch())
		{
			if(ExecuteModuleEventEx($arEvent, array($abs_path, &$strContent)) == false)
			{
				if(!$this->GetException())
					$this->ThrowException(GetMessage("main_save_file_handler_error", array("#HANDLER#"=>$arEvent["TO_NAME"])));
				return false;
			}
		}

		$io = CBXVirtualIo::GetInstance();
		$fileIo = $io->GetFile($abs_path);

		$io->CreateDirectory($fileIo->GetPath());

		if($fileIo->IsExists())
		{
			$file["exists"] = true;
			if (!$fileIo->IsWritable())
				$fileIo->MarkWritable();
			$file["size"] = $fileIo->GetFileSize();
		}

		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new CDiskQuota();
			if (false === $quota->checkDiskQuota(array("FILE_SIZE" => intVal(strLen($strContent) - intVal($file["size"])))))
			{
				$this->ThrowException($quota->LAST_ERROR, "BAD_QUOTA");
				return false;
			}
		}
		/****************************** QUOTA ******************************/
		if ($fileIo->PutContents($strContent))
		{
			$fileIo->MarkWritable();
		}
		else
		{
			if ($file["exists"])
				$this->ThrowException(GetMessage("MAIN_FILE_NOT_CREATE"), "FILE_NOT_CREATE");
			else
				$this->ThrowException(GetMessage("MAIN_FILE_NOT_OPENED"), "FILE_NOT_OPEN");
			return false;
		}

		bx_accelerator_reset();

		$site = CSite::GetSiteByFullPath($abs_path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			//Fix for name case under Windows
			$abs_path = strtolower($abs_path);
			$DOC_ROOT = strtolower($DOC_ROOT);
		}

		if(strpos($abs_path, $DOC_ROOT)===0 && $site!==false)
		{
			$DOC_ROOT = rtrim($DOC_ROOT, "/\\");
			$path = "/".ltrim(substr($abs_path, strlen($DOC_ROOT)), "/\\");

			$db_events = GetModuleEvents("main", "OnChangeFile");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent, array($path, $site));
		}
		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$fs = $fileIo->GetFileSize();
			CDiskQuota::updateDiskQuota("files", intVal($fs - intVal($file["size"])), "update");
		}
		/****************************** QUOTA ******************************/
		return true;
	}

	function GetFileContent($path)
	{
		clearstatcache();

		$io = CBXVirtualIo::GetInstance();

		if(!$io->FileExists($path))
			return false;
		$f = $io->GetFile($path);
		if($f->GetFileSize()<=0)
			return "";
		$contents = $f->GetContents();
		return $contents;
	}

	function ProcessLPA($filesrc = false, $old_filesrc = false)
	{
		if ($filesrc === false)
			return '';

		// Find all php fragments in $filesrc and:
		// 	1. Kill all non-component 2.0 fragments
		// 	2. Get and check params of components
		$arPHP = PHPParser::ParseFile($filesrc);
		$l = count($arPHP);
		if ($l > 0)
		{
			$new_filesrc = '';
			$end = 0;
			$php_count = 0;
			for ($n = 0; $n<$l; $n++)
			{
				$start = $arPHP[$n][0];
				$new_filesrc .= CMain::EncodePHPTags(substr($filesrc,$end,$start-$end));
				$end = $arPHP[$n][1];

				//Trim php tags
				$src = $arPHP[$n][2];
				if (substr($src, 0, 5) == "<?php")
					$src = '<?'.substr($src, 5);

				//If it's Component 2 - we handle it's params, non components2 will be erased
				$comp2_begin = '<?$APPLICATION->INCLUDECOMPONENT(';
				if (strtoupper(substr($src, 0, strlen($comp2_begin))) == $comp2_begin)
				{
					$arRes = PHPParser::CheckForComponent2($src);

					if ($arRes)
					{
						$comp_name = CMain::_ReplaceNonLatin($arRes['COMPONENT_NAME']);
						$template_name = CMain::_ReplaceNonLatin($arRes['TEMPLATE_NAME']);
						$arParams = $arRes['PARAMS'];
						$arPHPparams = Array();
						CMain::LPAComponentChecker($arParams, $arPHPparams);
						$len = count($arPHPparams);
						$br = "\r\n";
						$code = '$APPLICATION->IncludeComponent('.$br.
							"\t".'"'.$comp_name.'",'.$br.
							"\t".'"'.$template_name.'",'.$br;
						// If exist at least one parameter with php code inside
						if (count($arParams) > 0)
						{
							// Get array with description of component params
							$arCompParams = CComponentUtil::GetComponentProps($comp_name);
							$arTemplParams = CComponentUtil::GetTemplateProps($comp_name,$template_name,$template);

							$arParameters = array();
							if (isset($arCompParams["PARAMETERS"]) && is_array($arCompParams["PARAMETERS"]))
								$arParameters = $arParameters + $arCompParams["PARAMETERS"];
							if (is_array($arTemplParams))
								$arParameters = $arParameters + $arTemplParams;

							// Replace values from 'DEFAULT'
							for ($e = 0; $e < $len; $e++)
							{
								$par_name = $arPHPparams[$e];
								$arParams[$par_name] = isset($arParameters[$par_name]['DEFAULT']) ? $arParameters[$par_name]['DEFAULT'] : '';
							}

							//ReturnPHPStr
							$params = PHPParser::ReturnPHPStr2($arParams, $arParameters);
							$code .= "\t".'Array('.$br."\t".$params.$br."\t".')';
						}
						else
						{
							$code .=  "\t".'Array()';
						}
						$parent_comp = CMain::_ReplaceNonLatin($arRes['PARENT_COMP']);
						$arExParams_ = $arRes['FUNCTION_PARAMS'];

						$bEx = isset($arExParams_) && is_array($arExParams_) && count($arExParams_) > 0;

						if (!$parent_comp || strtolower($parent_comp) == 'false')
							$parent_comp = false;
						if ($parent_comp)
						{
							if ($parent_comp == 'true' || intVal($parent_comp) == $parent_comp)
								$code .= ','.$br."\t".$parent_comp;
							else
								$code .= ','.$br."\t\"".$parent_comp.'"';
						}
						if ($bEx)
						{
							if (!$parent_comp)
								$code .= ','.$br."\tfalse";

							$arExParams = array();
							foreach ($arExParams_ as $k => $v)
							{
								$k = CMain::_ReplaceNonLatin($k);
								$v = CMain::_ReplaceNonLatin($v);
								if (strlen($k) > 0 && strlen($v) > 0)
									$arExParams[$k] = $v;
							}
							//CComponentUtil::PrepareVariables($arExParams);
							$exParams = PHPParser::ReturnPHPStr2($arExParams);
							$code .= ','.$br."\tArray(".$exParams.')';
						}
						$code .= $br.');';
						$code = '<?'.$code.'?>';
						$new_filesrc .= $code;
					}
				}
			}
			$new_filesrc .= CMain::EncodePHPTags(substr($filesrc,$end));
			$filesrc = $new_filesrc;
		}
		else
		{
			$filesrc = CMain::EncodePHPTags($filesrc);
		}

		if (strpos($filesrc, '#PHP') !== false && $old_filesrc !== false) // We have to handle php fragments
		{
			// Get array of PHP scripts from old saved file
			$arPHP = PHPParser::ParseFile($old_filesrc);
			$arPHPscripts = Array();
			$l = count($arPHP);
			if ($l > 0)
			{
				$new_filesrc = '';
				$end = 0;
				$php_count = 0;
				for ($n = 0; $n < $l; $n++)
				{
					$start = $arPHP[$n][0];
					$new_filesrc .= substr($old_filesrc, $end, $start - $end);
					$end = $arPHP[$n][1];
					$src = $arPHP[$n][2];
					$src = SubStr($src, (SubStr($src, 0, 5) == "<?"."php") ? 5 : 2, -2); // Trim php tags
					$comp2_begin = '$APPLICATION->INCLUDECOMPONENT(';
					if (strtoupper(substr($src,0, strlen($comp2_begin))) != $comp2_begin)
						$arPHPscripts[] = $src;
				}
			}

			// Ok, so we already have array of php scripts lets check our new content
			// LPA-users CAN delete PHP fragments and swap them but CAN'T add new or modify existent:
			while (preg_match('/#PHP\d{4}#/i'.BX_UTF_PCRE_MODIFIER, $filesrc, $res))
			{
				$php_begin = strpos($filesrc, $res[0]);
				$php_fr_num = intval(substr($filesrc, $php_begin + 4, 4)) - 1; // Number of PHP fragment from #PHPXXXX# conctruction

				if (isset($arPHPscripts[$php_fr_num]))
					$filesrc = substr($filesrc, 0, $php_begin).'<?'.$arPHPscripts[$php_fr_num].'?>'.substr($filesrc, $php_begin + 9);
				else
					$filesrc = substr($filesrc, 0, $php_begin).substr($filesrc, $php_begin + 9);
			}
		}

		return $filesrc;
	}

	function EncodePHPTags($str)
	{
		$str = str_replace(array("<?","?>", "<%", "%>"),array("&lt;?","?&gt;","&lt;%","%&gt;"), $str);

		static $pattern = "/(<script[^>]*language\s*=\s*)('|\"|)php('|\"|)([^>]*>)/i";
		$str = preg_replace($pattern, "&lt;??&gt;", $str);

		return $str;
	}

	function LPAComponentChecker(&$arParams, &$arPHPparams, $parentParamName = false)
	{
		//all php fragments wraped by ={}
		foreach ($arParams as $param_name => $paramval)
		{
			if (substr($param_name, 0, 2) == '={' && substr($param_name, -1) == '}')
			{
				$key = substr($param_name, 2, -1);
				if (strval($key) !== strval(intval($key)))
				{
					unset($arParams[$param_name]);
					continue;
				}
			}
			if (is_array($paramval))
			{
				CMain::LPAComponentChecker($paramval, $arPHPparams, $param_name);
				$arParams[$param_name] = $paramval;
			}
			elseif (substr($paramval, 0, 2) == '={' && substr($paramval, -1) == '}')
			{
				$arPHPparams[] = $parentParamName ? $parentParamName : $param_name;
			}
		}
	}

	function _ReplaceNonLatin($str)
	{
		return preg_replace("/[^a-zA-Z0-9_:\.!\$\-;@\^\~]/is", "", $str);
	}

	function GetLangSwitcherArray()
	{
		return CMain::GetSiteSwitcherArray();
	}

	function GetSiteSwitcherArray()
	{
		global $DB, $REQUEST_URI, $DOCUMENT_ROOT;

		$cur_dir = $this->GetCurDir();
		$cur_page = $this->GetCurPage();
		$bAdmin = (substr($cur_dir, 0, strlen(BX_ROOT."/admin/")) == BX_ROOT."/admin/");

		$db_res = CSite::GetList($by, $order, array("ACTIVE"=>"Y","ID"=>LANG));
		if(($ar = $db_res->Fetch()) && strpos($cur_page, $ar["DIR"])===0)
		{
			$path_without_lang = substr($cur_page, strlen($ar["DIR"])-1);
			$path_without_lang = LTrim($path_without_lang, "/");
			$path_without_lang_tmp = RTrim($path_without_lang, "/");
		}

		$result = Array();
		$db_res = CSite::GetList($by="SORT", $order="ASC", Array("ACTIVE"=>"Y"));
		while($ar = $db_res->Fetch())
		{
			$ar["NAME"] = htmlspecialcharsbx($ar["NAME"]);
			$ar["SELECTED"] = ($ar["LID"]==LANG);

			if($bAdmin)
			{
				global $QUERY_STRING;
				$p = rtrim(str_replace("&#", "#", preg_replace("/lang=[^&#]*&*/", "", $QUERY_STRING)), "&");
				$ar["PATH"] = $this->GetCurPage()."?lang=".$ar["LID"].($p <> ''? '&'.$p : '');
			}
			else
			{
				$ar["PATH"] = "";

				if(strlen($path_without_lang)>1 && file_exists($ar["ABS_DOC_ROOT"]."/".$ar["DIR"]."/".$path_without_lang_tmp))
					$ar["PATH"] = $ar["DIR"].$path_without_lang;

				if(strlen($ar["PATH"])<=0)
					$ar["PATH"] = $ar["DIR"];

				if($ar["ABS_DOC_ROOT"]!==$_SERVER["DOCUMENT_ROOT"])
					$ar["FULL_URL"] = (CMain::IsHTTPS() ? "https://" : "http://").$ar["SERVER_NAME"].$ar["PATH"];
				else
					$ar["FULL_URL"] = $ar["PATH"];
			}

			$result[] = $ar;
		}
		return $result;
	}

	/*
	���������� ������ �����, ���������� � ���������� ������
	W - ���� � ������������� ������� (�������������)
	D - ����������� ���� (������ ������)

	$module_id - ������������� ������
	$arGroups - ������ ID �����, ���� �� �����, �� ������� ������ ����� �������� ������������
	$use_default_role - "Y" - ��� ����������� ����� ������������ ������� "�� ���������"
	$max_role_for_super_admin - "Y" - ���� � ������� ����� ������������ ������������ ������ � ID=1 �� ������� ������������ ����
	*/
	function GetUserRoles($module_id, $arGroups=false, $use_default_role="Y", $max_role_for_super_admin="Y", $site_id=false)
	{
		global $DB, $USER;
		static $MODULE_ROLES = array();

		$err_mess = (CAllMain::err_mess())."<br>Function: GetUserRoles<br>Line: ";
		$arRoles = array();
		$min_role = "D";
		$max_role = "W";
		if($arGroups===false)
		{
			if(is_object($USER))
				$arGroups = $USER->GetUserGroupArray();
			if(!is_array($arGroups))
				$arGroups[] = 2;
		}
		$key = $use_default_role."_".$max_role_for_super_admin;
		if(is_array($arGroups) && count($arGroups)>0)
		{
			$groups = '';
			foreach($arGroups as $grp)
				$groups .= ($groups<>''? ',':'').intval($grp);
			$key .= "_".$groups;
		}

		$cache_site_key = ($site_id ? $site_id : "COMMON");

		if(isset($MODULE_ROLES[$module_id][$cache_site_key][$key]))
			$arRoles = $MODULE_ROLES[$module_id][$cache_site_key][$key];
		else
		{
			if(is_array($arGroups) && count($arGroups)>0)
			{
				if(in_array(1,$arGroups) && $max_role_for_super_admin=="Y")
					$arRoles[] = $max_role;

				$strSql =
					"SELECT MG.G_ACCESS FROM b_group G ".
					"	LEFT JOIN b_module_group MG ON (G.ID = MG.GROUP_ID ".
					"		AND MG.MODULE_ID = '".$DB->ForSql($module_id,50)."') ".
					"		AND MG.SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL")." ".
					"WHERE G.ID in (".$groups.") AND G.ACTIVE = 'Y'";

				$t = $DB->Query($strSql, false, $err_mess.__LINE__);

				if($use_default_role=="Y")
					$default_role = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_role);

				while ($tr = $t->Fetch())
				{
					if ($tr["G_ACCESS"] !== null)
					{
						$arRoles[] = $tr["G_ACCESS"];
					}
					else
					{
						if($use_default_role=="Y")
							$arRoles[] = $default_role;
					}
				}

			}
			//if($use_default_role=="Y")
			//{
			//	$arRoles[] = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_role);
			//}
			$arRoles = array_unique($arRoles);
			$MODULE_ROLES[$module_id][$cache_site_key][$key] = $arRoles;
		}
		return $arRoles;
	}

	/*
	���������� ������� ����� ������� � ������, ����������� � ���������� ������
	W - ������������ ������� �������
	D - ����������� ������� ������� (������ ������)

	$module_id - ������������� ������
	$arGroups - ������ ID �����, ���� �� �����, �� ������� ������ ����� �������� ������������
	$use_default_level - "Y" - ��� ����������� ����� ������������ ������� "�� ���������"
	$max_right_for_super_admin - "Y" - ���� � ������� ����� ������������ ������������ ������ � ID=1 �� ������� ������������ �����
	*/
	function GetUserRight($module_id, $arGroups=false, $use_default_level="Y", $max_right_for_super_admin="Y", $site_id=false)
	{
		global $DB, $USER, $MODULE_PERMISSIONS;
		$err_mess = (CAllMain::err_mess())."<br>Function: GetUserRight<br>Line: ";
		$min_right = "D";
		$max_right = "W";
		$cur_admin = false;
		if($arGroups===false)
		{
			if(is_object($USER))
			{
				if($USER->IsAdmin())
					return $max_right;
				$arGroups = $USER->GetUserGroupArray();
			}
			if(!is_array($arGroups))
				$arGroups = array(2);
		}

		$key = $use_default_level."_".$max_right_for_super_admin;
		if(is_array($arGroups) && count($arGroups)>0)
		{
			$groups = '';
			foreach($arGroups as $grp)
				$groups .= ($groups<>''? ',':'').intval($grp);
			$key .= "_".$groups;
		}


		$cache_site_key = ($site_id ? $site_id : "COMMON");

		if (!$site_id)
			$cache_site_key = "COMMON";
		elseif(is_array($site_id))
		{
			$cache_site_key = "";
			foreach($site_id as $i => $site_id_tmp)
			{
				if ($i > 0)
					$cache_site_key .= "_";

				$cache_site_key .= ($site_id_tmp ? $site_id_tmp : "COMMON");
			}
		}
		else
			$cache_site_key = $site_id;

		if(!is_array($MODULE_PERMISSIONS[$module_id][$cache_site_key]))
			$MODULE_PERMISSIONS[$module_id][$cache_site_key] = array();

		$right = "";
		if(is_set($MODULE_PERMISSIONS[$module_id][$cache_site_key], $key))
			$right = $MODULE_PERMISSIONS[$module_id][$cache_site_key][$key];
		else
		{
			if(is_array($arGroups) && count($arGroups)>0)
			{
				if(in_array(1, $arGroups) && $max_right_for_super_admin=="Y" && (COption::GetOptionString("main", "controller_member", "N") != "Y" || COption::GetOptionString("main", "~controller_limited_admin", "N") != "Y"))
					$right = $max_right;
				else
				{
					if (!$site_id)
						$strSqlSite = "and MG.SITE_ID IS NULL";
					elseif(is_array($site_id))
					{
						$strSqlSite = " and (";
						foreach($site_id as $i => $site_id_tmp)
						{
							if ($i > 0)
								$strSqlSite .= " OR ";

							$strSqlSite .= "MG.SITE_ID ".($site_id_tmp ? "= '".$DB->ForSql($site_id_tmp)."'" : "IS NULL");
						}
						$strSqlSite .= ")";
					}
					else
						$strSqlSite = "and MG.SITE_ID = '".$DB->ForSql($site_id)."'";

					$strSql = "
						SELECT
							max(MG.G_ACCESS) G_ACCESS
						FROM
							b_module_group MG
						INNER JOIN b_group G ON (MG.GROUP_ID = G.ID)
						WHERE
							MG.MODULE_ID = '".$DB->ForSql($module_id,50)."'
						and MG.GROUP_ID in (".$groups.")
						and G.ACTIVE = 'Y'
						".$strSqlSite;

					$t = $DB->Query($strSql, false, $err_mess.__LINE__);
					$tr = $t->Fetch();
					$right = $tr["G_ACCESS"];
				}
			}

			if($right == "" && $use_default_level=="Y")
				$right = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_right);

			if($right <> "")
			{
				if(!is_array($MODULE_PERMISSIONS[$module_id][$cache_site_key]))
					$MODULE_PERMISSIONS[$module_id][$cache_site_key] = array();
				$MODULE_PERMISSIONS[$module_id][$cache_site_key][$key] = $right;
			}
		}
		return $right;
	}

	function GetGroupRightList($arFilter, $site_id=false)
	{
		global $DB;

		$strSqlWhere = "";
		if (array_key_exists("MODULE_ID", $arFilter))
			$strSqlWhere .= " AND MODULE_ID = '".$DB->ForSql($arFilter["MODULE_ID"])."' ";
		if (array_key_exists("GROUP_ID", $arFilter))
			$strSqlWhere .= " AND GROUP_ID = ".IntVal($arFilter["GROUP_ID"])." ";
		if (array_key_exists("G_ACCESS", $arFilter))
			$strSqlWhere .= " AND G_ACCESS = '".$DB->ForSql($arFilter["G_ACCESS"])."' ";
		$strSqlWhere .= " AND SITE_ID ".($site_id? "= '".$DB->ForSql($site_id)."'" : "IS NULL");

		$dbRes = $DB->Query(
			"SELECT ID, MODULE_ID, GROUP_ID, G_ACCESS ".
			"FROM b_module_group ".
			"WHERE 1 = 1 ".
			$strSqlWhere
		);

		return $dbRes;
	}

	function GetGroupRight($module_id, $arGroups=false, $use_default_level="Y", $max_right_for_super_admin="Y", $site_id = false)
	{
		return CMain::GetUserRight($module_id, $arGroups, $use_default_level, $max_right_for_super_admin, $site_id);
	}

	function SetGroupRight($module_id, $group_id, $right, $site_id=false)
	{
		global $DB;
		$err_mess = (CAllMain::err_mess())."<br>Function: SetGroupRight<br>Line: ";
		$group_id = intval($group_id);

		if(COption::GetOptionString("main", "event_log_module_access", "N") === "Y")
		{
			//get old value
			$sOldRight = "";
			$rsRight = $DB->Query("SELECT G_ACCESS FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' AND GROUP_ID=".$group_id." AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL"));
			if($arRight = $rsRight->Fetch())
				$sOldRight = $arRight["G_ACCESS"];
			if($sOldRight <> $right)
				CEventLog::Log("SECURITY", "MODULE_RIGHTS_CHANGED", "main", $group_id, $module_id.($site_id ? "/".$site_id : "").": (".$sOldRight.") => (".$right.")");
		}

		$arFields = Array(
			"MODULE_ID"	=> "'".$DB->ForSql($module_id,50)."'",
			"GROUP_ID"	=> $group_id,
			"G_ACCESS"	=> "'".$DB->ForSql($right,255)."'"
			);

		$rows = $DB->Update("b_module_group", $arFields, "WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' AND GROUP_ID='".$group_id."' AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL"), $err_mess.__LINE__);
		if(intval($rows)<=0)
		{
			if ($site_id)
				$arFields["SITE_ID"] = "'".$DB->ForSql($site_id,2)."'";

			$DB->Insert("b_module_group",$arFields, $err_mess.__LINE__);
		}
	}

	function DelGroupRight($module_id='', $arGroups=array(), $site_id=false)
	{
		global $DB;
		$err_mess = (CAllMain::err_mess())."<br>Function:  DelGroupRight<br>Line: ";
		$strSql = '';

		$sGroups = '';
		if(is_array($arGroups) && count($arGroups)>0)
			foreach($arGroups as $grp)
				$sGroups .= ($sGroups <> ''? ',':'').intval($grp);

		if($module_id <> '')
		{
			if($sGroups <> '')
			{
				if(COption::GetOptionString("main", "event_log_module_access", "N") === "Y")
				{
					//get old value
					$rsRight = $DB->Query("SELECT GROUP_ID, G_ACCESS FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' AND GROUP_ID IN (".$sGroups.") AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL"));
					while($arRight = $rsRight->Fetch())
						CEventLog::Log("SECURITY", "MODULE_RIGHTS_CHANGED", "main", $arRight["GROUP_ID"], $module_id.($site_id ? "/".$site_id : "").": (".$arRight["G_ACCESS"].") => ()");
				}
				$strSql = "DELETE FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' and GROUP_ID in (".$sGroups.") AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL");
			}
			else
			{
				//on delete module
				$strSql = "DELETE FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL");
			}
		}
		elseif($sGroups <> '')
		{
			//on delete user group
			$strSql = "DELETE FROM b_module_group WHERE GROUP_ID in (".$sGroups.") AND SITE_ID ".($site_id ? "= '".$DB->ForSql($site_id)."'" : "IS NULL");
		}

		if($strSql <> '')
			$DB->Query($strSql, false, $err_mess.__LINE__);
	}

	function GetMainRightList()
	{
		$arr = array(
			"reference_id" => array(
				"D",
				"P",
				"R",
				"T",
				"V",
				"W"),
			"reference" => array(
				"[D] ".GetMessage("OPTION_DENIED"),
				"[P] ".GetMessage("OPTION_PROFILE"),
				"[R] ".GetMessage("OPTION_READ"),
				"[T] ".GetMessage("OPTION_READ_PROFILE_WRITE"),
				"[V] ".GetMessage("OPTION_READ_OTHER_PROFILES_WRITE"),
				"[W] ".GetMessage("OPTION_WRITE"))
			);
		return $arr;
	}

	function GetDefaultRightList()
	{
		$arr = array(
			"reference_id" => array("D","R","W"),
			"reference" => array(
				"[D] ".GetMessage("OPTION_DENIED"),
				"[R] ".GetMessage("OPTION_READ"),
				"[W] ".GetMessage("OPTION_WRITE"))
			);
		return $arr;
	}

	function err_mess()
	{
		return "<br>Class: CAllMain<br>File: ".__FILE__;
	}

	/*
	���������� �������� ���� �� ��������� ����� ����������

	$name			: ��� ���� (��� ��������)
	$name_prefix	: ������� ��� ����� ���� (���� �� �����, �� ������� �� �������� �������� ������)
	*/
	function get_cookie($name, $name_prefix=false)
	{
		if($name_prefix===false)
			$name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_".$name;
		else
			$name = $name_prefix."_".$name;
		return (isset($_COOKIE[$name])? $_COOKIE[$name] : "");
	}

	/*
	������������� ��� � ��� ������������� ���������� ��������� �������������� ���� � ������� ��� ����������� �������������� �� �������

	$name			: ��� ���� (��� ��������)
	$value			: �������� ����������
	$time			: ���� ����� ������� ��� ��������
	$folder			: ������� �������� ����
	$domain			: ����� �������� ����
	$secure			: ���� secure ��� ���� (1 - secure)
	$spread			: Y - ������������� ��� �� ��� ����� � �� ������
	$name_prefix	: ������� ��� ����� ���� (���� �� �����, �� ������� �� �������� �������� ������)
	*/
	function set_cookie($name, $value, $time=false, $folder="/", $domain=false, $secure=false, $spread=true, $name_prefix=false)
	{
		if($time === false)
			$time = time()+60*60*24*30*12; // 30 ����� * 12 ~ 1 ���
		if($name_prefix===false)
			$name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_".$name;
		else
			$name = $name_prefix."_".$name;

		if($domain === false)
			$domain = $this->GetCookieDomain();

		if($spread === "Y" || $spread === true)
			$spread_mode = BX_SPREAD_DOMAIN | BX_SPREAD_SITES;
		elseif($spread >= 1)
			$spread_mode = $spread;
		else
			$spread_mode = BX_SPREAD_DOMAIN;

		//current domain only
		if($spread_mode & BX_SPREAD_DOMAIN)
			setcookie($name, $value, $time, $folder, $domain, $secure);

		//spread over sites
		if($spread_mode & BX_SPREAD_SITES)
			$this->arrSPREAD_COOKIE[$name] = array("V" => $value, "T" => $time, "F" => $folder, "D" => $domain, "S" => $secure);
	}

	function GetCookieDomain()
	{
		static $bCache = false;
		static $cache  = false;
		if($bCache)
			return $cache;

		global $DB;
		if(CACHED_b_lang_domain===false)
		{
			$strSql = "
				SELECT
					DOMAIN
				FROM
					b_lang_domain
				WHERE
					'".$DB->ForSql('.'.$_SERVER["HTTP_HOST"])."' like ".$DB->Concat("'%.'", "DOMAIN")."
				ORDER BY
					".$DB->Length("DOMAIN")."
				";
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			if($ar = $res->Fetch())
			{
				$cache = $ar['DOMAIN'];
			}
		}
		else
		{
			global $CACHE_MANAGER;
			if($CACHE_MANAGER->Read(CACHED_b_lang_domain, "b_lang_domain", "b_lang_domain"))
			{
				$arLangDomain = $CACHE_MANAGER->Get("b_lang_domain");
			}
			else
			{
				$arLangDomain = array("DOMAIN"=>array(), "LID"=>array());
				$res = $DB->Query("SELECT * FROM b_lang_domain ORDER BY ".$DB->Length("DOMAIN"));
				while($ar = $res->Fetch())
				{
					$arLangDomain["DOMAIN"][]=$ar;
					$arLangDomain["LID"][$ar["LID"]][]=$ar;
				}
				$CACHE_MANAGER->Set("b_lang_domain", $arLangDomain);
			}
			//$strSql = "'".$DB->ForSql($_SERVER["HTTP_HOST"])."' like ".$DB->Concat("'%.'", "DOMAIN")."";
			foreach($arLangDomain["DOMAIN"] as $ar)
			{
				if(strcasecmp(substr('.'.$_SERVER["HTTP_HOST"], -(strlen($ar['DOMAIN'])+1)), ".".$ar['DOMAIN']) == 0)
				{
					$cache = $ar['DOMAIN'];
					break;
				}
			}
		}

		$bCache = true;
		return $cache;
	}

	function StoreCookies()
	{
		$_SESSION['SPREAD_COOKIE'] = $this->arrSPREAD_COOKIE;
	}

	// ������� ����� IFRAME'�� ��� �������������� ����� �� ��� �������
	function GetSpreadCookieHTML()
	{
		static $showed_already;
		$res = "";
		if($showed_already!="Y" && COption::GetOptionString("main", "ALLOW_SPREAD_COOKIE", "Y")=="Y")
		{
			if(isset($_SESSION['SPREAD_COOKIE']) && is_array($_SESSION['SPREAD_COOKIE']) && !empty($_SESSION['SPREAD_COOKIE']))
			{
				$this->arrSPREAD_COOKIE += $_SESSION['SPREAD_COOKIE'];
				unset($_SESSION['SPREAD_COOKIE']);
			}

			if(!empty($this->arrSPREAD_COOKIE))
			{
				$params = "";
				reset($this->arrSPREAD_COOKIE);
				while (list($name,$ar)=each($this->arrSPREAD_COOKIE))
				{
					$ar["D"] = ""; // domain must be empty
					$params .= $name.chr(1).$ar["V"].chr(1).$ar["T"].chr(1).$ar["F"].chr(1).$ar["D"].chr(1).$ar["S"].chr(2);
				}
				$salt = $_SERVER["REMOTE_ADDR"]."|".@filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/version.php")."|".LICENSE_KEY;
				$params = "s=".urlencode(base64_encode($params))."&k=".urlencode(md5($params.$salt));
				$arrDomain = array();
				$arrDomain[] = $_SERVER["HTTP_HOST"];
				$rs = CSite::GetList(($v1="sort"), ($v2="asc"), array("ACTIVE" => "Y"));
				while($ar = $rs->Fetch())
				{
					//$arrDomain[] = $ar["SERVER_NAME"];
					$arD = array();
					$arD = explode("\n", str_replace("\r", "\n", $ar["DOMAINS"]));
					if(is_array($arD) && count($arD)>0)
						foreach($arD as $d)
							if(strlen(trim($d))>0)
								$arrDomain[] = $d;
				}

				if(count($arrDomain)>0)
				{
					$arUniqDomains = array();
					$arrDomain = array_unique($arrDomain);
					$arrDomain2 = array_unique($arrDomain);
					foreach($arrDomain as $domain1)
					{
						$bGood = true;
						foreach($arrDomain2 as $domain2)
						{
							if(strlen($domain1)>strlen($domain2) && substr($domain1, -(strlen($domain2)+1)) == ".".$domain2)
							{
								$bGood = false;
								break;
							}
						}
						if($bGood)
							$arUniqDomains[] = $domain1;
					}

					$protocol = (CMain::IsHTTPS()) ? "https://" : "http://";
					$arrCurUrl = parse_url($protocol.$_SERVER["HTTP_HOST"]."/".$_SERVER["REQUEST_URI"]);
					foreach($arUniqDomains as $domain)
					{
						if(strlen(trim($domain))>0)
						{
							$url = $protocol.$domain."/bitrix/spread.php?".$params;
							$arrUrl = parse_url($url);
							if($arrUrl["host"] != $arrCurUrl["host"])
								$res .= '<img src="'.htmlspecialcharsbx($url).'" alt="" style="width:0px; height:0px; position:absolute; left:-1px; top:-1px;" />'."\n";
						}
					}
				}
				$showed_already = "Y";
			}
		}
		return $res;
	}

	function ShowSpreadCookieHTML()
	{
		$this->AddBufferContent(Array(&$this, "GetSpreadCookieHTML"));
	}

	function AddPanelButton($arButton, $bReplace=false)
	{
		if(is_array($arButton) && count($arButton)>0)
		{
			if(isset($arButton["ID"]) && $arButton["ID"] <> "")
			{
				if(!isset($this->arPanelButtons[$arButton["ID"]]))
				{
					$this->arPanelButtons[$arButton["ID"]] = $arButton;
				}
				elseif($bReplace)
				{
					if(is_array($this->arPanelButtons[$arButton["ID"]]["MENU"]))
					{
						if(!is_array($arButton["MENU"]))
							$arButton["MENU"] = array();
						$arButton["MENU"] = array_merge($this->arPanelButtons[$arButton["ID"]]["MENU"], $arButton["MENU"]);
					}
					$this->arPanelButtons[$arButton["ID"]] = $arButton;
				}

				if (isset($this->arPanelFutureButtons[$arButton['ID']]))
				{
					if (is_array($this->arPanelButtons[$arButton["ID"]]["MENU"]))
					{
						$this->arPanelButtons[$arButton["ID"]]["MENU"] = array_merge(
							$this->arPanelButtons[$arButton["ID"]]["MENU"],
							$this->arPanelFutureButtons[$arButton["ID"]]
						);
					}
					else
					{
						$this->arPanelButtons[$arButton["ID"]]["MENU"] = $this->arPanelFutureButtons[$arButton["ID"]];
					}
					unset($this->arPanelFutureButtons[$arButton['ID']]);
				}
			}
			else
			{
				$this->arPanelButtons[] = $arButton;
			}
		}
	}

	function AddPanelButtonMenu($button_id, $arMenuItem)
	{
		if(isset($this->arPanelButtons[$button_id]))
		{
			if(!is_array($this->arPanelButtons[$button_id]['MENU']))
				$this->arPanelButtons[$button_id]['MENU'] = array();
			$this->arPanelButtons[$button_id]['MENU'][] = $arMenuItem;
		}
		else
		{
			if(!isset($this->arPanelFutureButtons[$button_id]))
				$this->arPanelFutureButtons[$button_id] = array();

			$this->arPanelFutureButtons[$button_id][] = $arMenuItem;
		}
	}

	function GetPanel()
	{
		if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAuthorized() && !isset($_REQUEST["bx_hit_hash"]))
			echo CTopPanel::GetPanelHtml();
	}

	function ShowPanel()
	{
		if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAuthorized() && !isset($_REQUEST["bx_hit_hash"]))
		{
			class_exists('CTopPanel'); //http://bugs.php.net/bug.php?id=47948
			AddEventHandler('main', 'OnBeforeEndBufferContent', array('CTopPanel', 'InitPanel'));
			$this->AddBufferContent(array('CTopPanel', 'GetPanelHtml'));

			//Prints global url classes and  variables for HotKeys
			$this->AddBufferContent(array('CAllMain',"PrintHKGlobalUrlVar"));

			$this->AddBufferContent(array('CAdminInformer',"PrintHtml"));
		}
	}

	function PrintHKGlobalUrlVar()
	{
		return CHotKeys::GetInstance()->PrintGlobalUrlVar();
	}

	function GetSiteByDir($cur_dir=false, $cur_host=false)
	{
		return $this->GetLang($cur_dir, $cur_host);
	}

	function AddBufferContent($callback)
	{
		//var_dump($callback);
		//var_dump("-----");
		$args = Array();
		$args_num = func_num_args();
		if($args_num>1)
			for($i=1; $i<$args_num; $i++)
				$args[] = func_get_arg($i);

		if(!defined("BX_BUFFER_USED") || BX_BUFFER_USED!==true)
		{
			echo call_user_func_array($callback, $args);
			return;
		}
		//var_dump(ob_get_length());
		$this->buffer_content[] = ob_get_contents();
		$this->buffer_content[] = "";
		$this->buffer_content_type[] = Array("F"=>$callback, "P"=>$args);
		$this->buffer_man = true;
		$this->auto_buffer_cleaned = false;
		ob_end_clean();
		$this->buffer_man = false;
		$this->buffered = true;
		if($this->auto_buffer_cleaned) // cross buffer fix
			ob_start(Array(&$this, "EndBufferContent"));
		else
			ob_start();
	}

	function RestartBuffer()
	{
		$this->buffer_man = true;
		ob_end_clean();
		$this->buffer_man = false;
		$this->buffer_content_type = Array();
		$this->buffer_content = Array();

		if(function_exists("getmoduleevents"))
		{
			$db_events = GetModuleEvents("main", "OnBeforeRestartBuffer");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent);
		}

		ob_start(Array(&$this, "EndBufferContent"));
	}

	function &EndBufferContentMan()
	{
		if(!$this->buffered)
			return;
		$content = ob_get_contents();
		$this->buffer_man = true;
		ob_end_clean();
		$this->buffered = false;
		$this->buffer_man = false;

		$this->buffer_manual = true;
		$res = $this->EndBufferContent($content);
		$this->buffer_manual = false;

		$this->buffer_content_type = Array();
		$this->buffer_content = Array();
		return $res;
	}

	function EndBufferContent($content="")
	{
		if($this->buffer_man)
		{
			$this->auto_buffer_cleaned = true;
			return '';
		}

		if(function_exists("getmoduleevents"))
		{
			$db_events = GetModuleEvents("main", "OnBeforeEndBufferContent");
			while($arEvent = $db_events->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array());
			}
		}

		if(is_object($GLOBALS["APPLICATION"])) //php 5.1.6 fix: http://bugs.php.net/bug.php?id=40104
		{
			$cnt = count($this->buffer_content_type);
			for($i=0; $i<$cnt; $i++)
				$this->buffer_content[$i*2+1] = call_user_func_array($this->buffer_content_type[$i]["F"], $this->buffer_content_type[$i]["P"]);
		}

		$content = implode('', $this->buffer_content).$content;

		if(function_exists("getmoduleevents"))
		{
			$db_events = GetModuleEvents("main", "OnEndBufferContent");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent, array(&$content));
		}

		return $content;
	}

	function ResetException()
	{
		if($this->LAST_ERROR)
			$this->ERROR_STACK[] = $this->LAST_ERROR;
		$this->LAST_ERROR = false;
	}

	function ThrowException($msg, $id = false)
	{
		$this->ResetException();
		if(is_object($msg) && (is_subclass_of($msg, 'CApplicationException') || (strtolower(get_class($msg))=='capplicationexception')))
			$this->LAST_ERROR = $msg;
		else
			$this->LAST_ERROR = new CApplicationException($msg, $id);
	}

	function GetException()
	{
		return $this->LAST_ERROR;
	}

	function ConvertCharset($string, $charset_in, $charset_out)
	{
		$this->ResetException();

		$error = "";
		$result = CharsetConverter::ConvertCharset($string, $charset_in, $charset_out, $error);
		if (!$result && !empty($error))
			$this->ThrowException($error, "ERR_CHAR_BX_CONVERT");

		return $result;
	}

	function ConvertCharsetArray($arData, $charset_from, $charset_to)
	{
		if (!is_array($arData))
			return $this->ConvertCharset($arData, $charset_from, $charset_to);

		foreach ($arData as $key => $value)
		{
			$arData[$key] = $this->ConvertCharsetArray($value, $charset_from, $charset_to);
		}

		return $arData;
	}

	function CaptchaGetCode()
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

		$cpt = new CCaptcha();
		$cpt->SetCode();

		return $cpt->GetSID();
	}

	function CaptchaCheckCode($captcha_word, $captcha_sid)
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

		$cpt = new CCaptcha();
		if ($cpt->CheckCode($captcha_word, $captcha_sid))
			return True;
		else
			return False;
	}

	function UnJSEscape($str)
	{
		if(strpos($str, "%u")!==false)
		{
			$str = preg_replace_callback("'%u([0-9A-F]{2})([0-9A-F]{2})'i", create_function('$ch', '$res = chr(hexdec($ch[2])).chr(hexdec($ch[1])); return $GLOBALS["APPLICATION"]->ConvertCharset($res, "UTF-16", LANG_CHARSET);'), $str);
		}
		return $str;
	}

	// DEPRECATED! Use CAdminFileDialog::ShowScript instead
	function ShowFileSelectDialog($event, $arResultDest, $arPath = array(), $fileFilter = "", $bAllowFolderSelect = False, $arOptions = Array())
	{
		return CAdminFileDialog::ShowScript(Array
			(
				"event" => $event,
				"arResultDest" => $arResultDest,
				"arPath" => $arPath,
				"select" => $bAllowFolderSelect ? 'DF' : 'F',
				"fileFilter" => $fileFilter,
				"operation" => 'O',
				"showUploadTab" => true,
				"showAddToMenuTab" => false,
				"allowAllFiles" => true,
				"SaveConfig" => true
			)
		);
	}

	/*
	array(
		"URL"=> 'url to open'
		"PARAMS"=> array('param' => 'value') - additional params, 2nd argument of jsPopup.ShowDialog()
	),
	*/
	function GetPopupLink($arUrl, $jsPopupSuffix = '')
	{
		CUtil::InitJSCore(array('window', 'ajax'));

		if (class_exists('CUserOptions') && (!is_array($arUrl['PARAMS']) || $arUrl['PARAMS']['resizable'] !== false))
		{
			$pos = strpos($arUrl['URL'], '?');
			if ($pos === false)
				$check_url = $arUrl['URL'];
			else
				$check_url = substr($arUrl['URL'], 0, $pos);

			$arPos = CUtil::GetPopupSize($check_url, $arUrl['PARAMS']);

			if ($arPos['width'])
			{
				if (!is_array($arUrl['PARAMS']))
					$arUrl['PARAMS'] = array();

				$arUrl['PARAMS']['width'] = $arPos['width'];
				$arUrl['PARAMS']['height'] = $arPos['height'];
			}
		}

		$dialog_class = 'CDialog';
		if (!!$arUrl['PARAMS']['dialog_type'])
		{
			switch ($arUrl['PARAMS']['dialog_type'])
			{
				case 'EDITOR': $dialog_class = 'CEditorDialog'; break;
				case 'ADMIN': $dialog_class = 'CAdminDialog'; break;
				default: $dialog_class = 'CDialog';
			}
		}
		elseif (strpos($arUrl['URL'], 'bxpublic=') !== false)
		{
			$dialog_class = 'CAdminDialog';
		}

		$arDialogParams = array(
			'content_url' => $arUrl['URL'],
			'width' => $arUrl['PARAMS']['width'],
			'height' => $arUrl['PARAMS']['height']
		);

		if (isset($arUrl['PARAMS']['min_width'])) $arDialogParams['min_width'] = intval($arUrl['PARAMS']['min_width']);
		if (isset($arUrl['PARAMS']['min_height'])) $arDialogParams['min_height'] = intval($arUrl['PARAMS']['min_height']);

		if ($arUrl['PARAMS']['resizable'] === false) $arDialogParams['resizable'] = false;

		if ($arUrl['POST'])
		{
			$arDialogParams['content_post'] = $arUrl['POST'];
		}

		return '(new BX.'.$dialog_class.'('.CUtil::PhpToJsObject($arDialogParams).')).Show()';
	}

	function GetServerUniqID()
	{
		$uniq = COption::GetOptionString("main", "server_uniq_id", "");
		if(strlen($uniq)<=0)
		{
			$uniq = md5(uniqid(rand(), true));
			COption::SetOptionString("main", "server_uniq_id", $uniq);
		}
		return $uniq;
	}

	function PrologActions()
	{
		global $APPLICATION;

		if (defined("BX_CHECK_SHORT_URI") && BX_CHECK_SHORT_URI)
		{
			if ($arUri = CBXShortUri::GetUri($_SERVER["REQUEST_URI"]))
			{
				CBXShortUri::SetLastUsed($arUri["ID"]);
				if (CModule::IncludeModule("statistic"))
					CStatEvent::AddCurrent("short_uri_redirect", "", "", "", "", $arUri["URI"], "N", SITE_ID);
				LocalRedirect($arUri["URI"], true, CBXShortUri::GetHttpStatusCodeText($arUri["STATUS"]));
				die();
			}
		}

		//session expander
		if(COption::GetOptionString("main", "session_expand", "Y") <> "N" && (!defined("BX_SKIP_SESSION_EXPAND") || BX_SKIP_SESSION_EXPAND == false))
		{
			$arPolicy = $GLOBALS["USER"]->GetSecurityPolicy();

			$phpSessTimeout = ini_get("session.gc_maxlifetime");
			if($arPolicy["SESSION_TIMEOUT"] > 0)
				$sessTimeout = min($arPolicy["SESSION_TIMEOUT"]*60, $phpSessTimeout);
			else
				$sessTimeout = $phpSessTimeout;

			$cookie_prefix = COption::GetOptionString('main', 'cookie_name', 'BITRIX_SM');
			$salt = $_COOKIE[$cookie_prefix.'_UIDH']."|".$_SERVER["REMOTE_ADDR"]."|".@filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/version.php")."|".LICENSE_KEY."|".CMain::GetServerUniqID();
			$key = md5(bitrix_sessid().$salt);

			$bShowMess = ($GLOBALS["USER"]->IsAuthorized() && COption::GetOptionString("main", "session_show_message", "Y") <> "N");

			$ext = array('ajax');
			if($bShowMess)
				$ext[] = "fx";

			CUtil::InitJSCore($ext);

			$GLOBALS["APPLICATION"]->AddHeadString(
				'<script type="text/javascript" src="'.CUtil::GetAdditionalFileURL('/bitrix/js/main/session.js').'"></script>'."\n".
				'<script type="text/javascript">'."\n".
				($bShowMess? 'bxSession.mess.messSessExpired = \''.CUtil::JSEscape(GetMessage("MAIN_SESS_MESS", array("#TIMEOUT#"=>round($sessTimeout/60)))).'\';'."\n" : '').
				'bxSession.Expand('.$sessTimeout.', \''.bitrix_sessid().'\', '.($bShowMess? 'true':'false').', \''.$key.'\');'."\n".
				'</script>', true
			);

			$_SESSION["BX_SESSION_COUNTER"] = intval($_SESSION["BX_SESSION_COUNTER"]) + 1;
			if(!defined("BX_SKIP_SESSION_TERMINATE_TIME"))
				$_SESSION["BX_SESSION_TERMINATE_TIME"] = time()+$sessTimeout;
		}

		//user auto time zone via js cookies
		if(CTimeZone::Enabled())
			CTimeZone::SetAutoCookie();

		// check user options set via cookie
		if ($GLOBALS["USER"]->IsAuthorized())
		{
			$cookieName = COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LAST_SETTINGS";
			if(!empty($_COOKIE[$cookieName]))
			{
				CUserOptions::SetCookieOptions($cookieName);
			}
		}

		if(COption::GetOptionString("main", "buffer_content", "Y")=="Y" && (!defined("BX_BUFFER_USED") || BX_BUFFER_USED!==true))
		{
			ob_start(array(&$APPLICATION, "EndBufferContent"));
			$APPLICATION->buffered = true;
			define("BX_BUFFER_USED", true);
			register_shutdown_function(create_function('', 'while(@ob_end_flush());'));
		}

		$db_events = GetModuleEvents("main", "OnProlog");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent);
	}

	function EpilogActions()
	{
		global $DB;
		//send email events
		if(COption::GetOptionString("main", "check_events", "Y") !== "N")
		{
			$DB->StartUsingMasterOnly();
			CEvent::CheckEvents();
			$DB->StopUsingMasterOnly();
		}
		//files cleanup
		CMain::FileAction();
	}

	function ForkActions($func = false, $args = array())
	{
		static $arFunctions = array();

		if(
			!defined("BX_FORK_AGENTS_AND_EVENTS_FUNCTION")
			|| !function_exists(BX_FORK_AGENTS_AND_EVENTS_FUNCTION)
			|| !function_exists("getmypid")
			|| !function_exists("posix_kill")
		)
			return false;

		//Avoid to recurse itself
		if(defined("BX_FORK_AGENTS_AND_EVENTS_FUNCTION_STARTED"))
			return false;

		//Register function to execute in forked process
		if($func !== false)
		{
			$arFunctions[] = array($func, $args);
			return true;
		}

		//There is nothing to do
		if(empty($arFunctions))
			return true;

		$func = BX_FORK_AGENTS_AND_EVENTS_FUNCTION;
		$pid = $func();

		//Parent just exits.
		if($pid > 0)
			return false;

		//Fork was successfull let's do seppuku on shutdown
		if($pid == 0)
			register_shutdown_function(create_function('', 'posix_kill(getmypid(), 9);'));

		//Mark start of execution
		define("BX_FORK_AGENTS_AND_EVENTS_FUNCTION_STARTED", true);

		//Release session
		session_write_close();
		global $DB, $CACHE_MANAGER;
		$CACHE_MANAGER = new CCacheManager;

		$DB->DoConnect();
		$DB->StartUsingMasterOnly();
		foreach($arFunctions as $action)
			call_user_func_array($action[0], $action[1]);
		$DB->Disconnect();
		$CACHE_MANAGER->_Finalize();
	}
}

global $MAIN_LANGS_CACHE;
$MAIN_LANGS_CACHE = Array();

global $MAIN_LANGS_ADMIN_CACHE;
$MAIN_LANGS_ADMIN_CACHE = Array();


class CAllSite
{
	function InDir($strDir)
	{
		global $APPLICATION;
		return (substr($APPLICATION->GetCurPage(true), 0, strlen($strDir))==$strDir);
	}

	function InPeriod($iUnixTimestampFrom, $iUnixTimestampTo)
	{
		if($iUnixTimestampFrom>0 && time()<$iUnixTimestampFrom)
			return false;
		if($iUnixTimestampTo>0 && time()>$iUnixTimestampTo)
			return false;

		return true;
	}

	function InGroup($arGroups)
	{
		global $USER;
		$arUserGroups = $USER->GetUserGroupArray();
		if (count(array_intersect($arUserGroups,$arGroups))>0)
			return true;
		return false;
	}

	function GetWeekStart()
	{
		static $weekStart = -1;

		if ($weekStart < 0)
		{
			if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
			{
				global $MAIN_LANGS_CACHE;
				if(!is_set($MAIN_LANGS_CACHE, SITE_ID))
				{
					$res = CLang::GetByID(SITE_ID);
					if ($res = $res->Fetch())
					{
						$MAIN_LANGS_CACHE[$res["LID"]] = $res;
					}
				}

				if (is_set($MAIN_LANGS_CACHE, SITE_ID))
				{
					$weekStart = $MAIN_LANGS_CACHE[SITE_ID]['WEEK_START'];
				}
			}
			else
			{
				global $MAIN_LANGS_ADMIN_CACHE;
				if(!is_set($MAIN_LANGS_ADMIN_CACHE, LANGUAGE_ID))
				{
					$res = CLanguage::GetByID($lang);
					if($res = $res->Fetch())
					{
						$MAIN_LANGS_ADMIN_CACHE[$res["LID"]] = $res;
					}
				}

				if (is_set($MAIN_LANGS_ADMIN_CACHE, LANGUAGE_ID))
				{
					$weekStart = $MAIN_LANGS_ADMIN_CACHE[LANGUAGE_ID]['WEEK_START'];
				}
			}

			if ($weekStart < 0 || $weekStart == null)
			{
				$weekStart = 1;
			}
		}

		return $weekStart;
	}

	function GetDateFormat($type="FULL", $lang=false, $bSearchInSitesOnly=false)
	{
		$bFullFormat = (strtoupper($type) == "FULL");

		if($lang === false)
			$lang = LANG;

		if(defined("SITE_ID") && $lang == SITE_ID)
		{
			if($bFullFormat && defined("FORMAT_DATETIME"))
				return FORMAT_DATETIME;
			if(!$bFullFormat && defined("FORMAT_DATE"))
				return FORMAT_DATE;
		}

		if(!$bSearchInSitesOnly && defined("ADMIN_SECTION") && ADMIN_SECTION===true)
		{
			global $MAIN_LANGS_ADMIN_CACHE;
			if(!is_set($MAIN_LANGS_ADMIN_CACHE, $lang))
			{
				$res = CLanguage::GetByID($lang);
				if($res = $res->Fetch())
					$MAIN_LANGS_ADMIN_CACHE[$res["LID"]] = $res;
			}

			if(is_set($MAIN_LANGS_ADMIN_CACHE, $lang))
			{
				if($bFullFormat)
					return strtoupper($MAIN_LANGS_ADMIN_CACHE[$lang]["FORMAT_DATETIME"]);
				return strtoupper($MAIN_LANGS_ADMIN_CACHE[$lang]["FORMAT_DATE"]);
			}
		}

		// if LANG is not found in LangAdmin:
		global $MAIN_LANGS_CACHE;
		if(!is_set($MAIN_LANGS_CACHE, $lang))
		{
			$res = CLang::GetByID($lang);
			$res = $res->Fetch();
			$MAIN_LANGS_CACHE[$res["LID"]] = $res;
			if(defined("ADMIN_SECTION") && ADMIN_SECTION === true)
				$MAIN_LANGS_ADMIN_CACHE[$res["LID"]] = $res;
		}

		if($bFullFormat)
		{
			$format = strtoupper($MAIN_LANGS_CACHE[$lang]["FORMAT_DATETIME"]);
			if($format == '')
				$format = "DD.MM.YYYY HH:MI:SS";
		}
		else
		{
			$format = strtoupper($MAIN_LANGS_CACHE[$lang]["FORMAT_DATE"]);
			if($format == '')
				$format = "DD.MM.YYYY";
		}
		return $format;
	}

	function GetTimeFormat($lang=false, $bSearchInSitesOnly = false, $format = false)
	{
		$dateTimeFormat = self::GetDateFormat('FULL', $lang, $bSearchInSitesOnly);
		preg_match('~[HG]~', $dateTimeFormat, $chars, PREG_OFFSET_CAPTURE);
		return trim(substr($dateTimeFormat, $chars[0][1]));
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if(is_set($arFields, "NAME") && strlen($arFields["NAME"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SITE_NAME")." ";
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("BAD_SITE_NAME"));
		}
		if($ID===false && is_set($arFields, "LID") && strlen($arFields["LID"])!=2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SITE_LID")." ";
			$arMsg[] = array("id"=>"LID", "text"=> GetMessage("BAD_SITE_LID"));
		}
		if(is_set($arFields, "DIR") && strlen($arFields["DIR"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_DIR")." ";
			$arMsg[] = array("id"=>"DIR", "text"=> GetMessage("BAD_LANG_DIR"));
		}
		if($ID===false && !is_set($arFields, "LANGUAGE_ID"))
		{
			$this->LAST_ERROR .= GetMessage("MAIN_BAD_LANGUAGE_ID")." ";
			$arMsg[] = array("id"=>"LANGUAGE_ID", "text"=> GetMessage("MAIN_BAD_LANGUAGE_ID"));
		}
		elseif($ID!==false && is_set($arFields, "LANGUAGE_ID"))
		{
			$dbl_check = CLanguage::GetByID($arFields["LANGUAGE_ID"]);
			if(!$dbl_check->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("MAIN_BAD_LANGUAGE_ID_BAD")." ";
				$arMsg[] = array("id"=>"LANGUAGE_ID", "text"=> GetMessage("MAIN_BAD_LANGUAGE_ID_BAD"));
			}
		}
		if(is_set($arFields, "SORT") && strlen($arFields["SORT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SORT")." ";
			$arMsg[] = array("id"=>"SORT", "text"=> GetMessage("BAD_SORT"));
		}
		if(is_set($arFields, "FORMAT_DATE") && strlen($arFields["FORMAT_DATE"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_FORMAT_DATE")." ";
			$arMsg[] = array("id"=>"FORMAT_DATE", "text"=> GetMessage("BAD_FORMAT_DATE"));
		}
		if(is_set($arFields, "FORMAT_DATETIME") && strlen($arFields["FORMAT_DATETIME"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_FORMAT_DATETIME")." ";
			$arMsg[] = array("id"=>"FORMAT_DATETIME", "text"=> GetMessage("BAD_FORMAT_DATETIME"));
		}
		if(is_set($arFields, "FORMAT_NAME") && strlen($arFields["FORMAT_NAME"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_FORMAT_NAME")." ";
			$arMsg[] = array("id"=>"FORMAT_NAME", "text"=> GetMessage("BAD_FORMAT_NAME"));
		}
		if(is_set($arFields, "CHARSET") && strlen($arFields["CHARSET"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_CHARSET")." ";
			$arMsg[] = array("id"=>"CHARSET", "text"=> GetMessage("BAD_CHARSET"));
		}
		if(is_set($arFields, "TEMPLATE"))
		{
			$isOK = false;
			$check_templ = Array();
			foreach($arFields["TEMPLATE"] as $val)
			{
				if(strlen($val["TEMPLATE"])>0 && file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$val["TEMPLATE"]))
				{
					if(in_array($val["TEMPLATE"].", ".$val["CONDITION"], $check_templ))
						$this->LAST_ERROR = GetMessage("MAIN_BAD_TEMPLATE_DUP");
					$check_templ[] = $val["TEMPLATE"].", ".$val["CONDITION"];
					$isOK = true;
				}
			}
			if(!$isOK)
			{
				$this->LAST_ERROR .= GetMessage("MAIN_BAD_TEMPLATE");
				$arMsg[] = array("id"=>"SITE_TEMPLATE", "text"=> GetMessage("MAIN_BAD_TEMPLATE"));
			}
		}

		if($ID===false)
			$db_events = GetModuleEvents("main", "OnBeforeSiteAdd");
		else
			$db_events = GetModuleEvents("main", "OnBeforeSiteUpdate");
		while($arEvent = $db_events->Fetch())
		{
			$bEventRes = ExecuteModuleEventEx($arEvent, array(&$arFields));
			if($bEventRes===false)
			{
				if($err = $GLOBALS["APPLICATION"]->GetException())
				{
					$this->LAST_ERROR .= $err->GetString()." ";
					$arMsg[] = array("id"=>"EVENT_ERROR", "text"=> $err->GetString());
				}
				else
				{
					$this->LAST_ERROR .= "Unknown error. ";
					$arMsg[] = array("id"=>"EVENT_ERROR", "text"=> "Unknown error. ");
				}
				break;
			}
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		if($ID===false)
		{
			$r = $DB->Query("SELECT 'x' FROM b_lang WHERE LID='".$DB->ForSQL($arFields["LID"], 2)."'");
			if($r->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("BAD_SITE_DUP")." ";
				$e = new CAdminException(Array(Array("id" => "LID", "text" => GetMessage("BAD_SITE_DUP"))));
				$GLOBALS["APPLICATION"]->ThrowException($e);
				return false;
			}
		}

		return true;
	}

	function SaveDomains($LID, $domains)
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_lang_domain !== false)
			$CACHE_MANAGER->CleanDir("b_lang_domain");

		$DB->Query("DELETE FROM b_lang_domain WHERE LID='".$DB->ForSQL($LID)."'");

		$domains = str_replace("\r", "\n", $domains);
		$arDomains = explode("\n", $domains);
		for($i=0, $n=count($arDomains); $i<$n; $i++)
			$arDomains[$i] = preg_replace("#^(http://|https://)#", "", trim(strtolower($arDomains[$i])));
		$arDomains = array_unique($arDomains);

		$bIsDomain = false;
		foreach($arDomains as $domain)
		{
			if($domain <> '')
			{
				if ($domainTmp = CBXPunycode::ToASCII($domain, $arErrors))
					$domain = $domainTmp;
				$DB->Query("INSERT INTO b_lang_domain(LID, DOMAIN) VALUES('".$DB->ForSQL($LID, 2)."', '".$DB->ForSQL($domain, 255)."')");
				$bIsDomain = true;
			}
		}
		$DB->Query("UPDATE b_lang SET DOMAIN_LIMITED='".($bIsDomain? "Y":"N")."' WHERE LID='".$DB->ForSql($LID)."'");
	}

	function Add($arFields)
	{
		global $DB, $DOCUMENT_ROOT, $CACHE_MANAGER;

		if(!$this->CheckFields($arFields))
			return false;
		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_lang SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$arInsert = $DB->PrepareInsert("b_lang", $arFields);

		$strSql =
			"INSERT INTO b_lang(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";

		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		if(is_set($arFields, "DIR"))
			CheckDirPath($DOCUMENT_ROOT.$arFields["DIR"]);

		if(is_set($arFields, "DOMAINS"))
			self::SaveDomains($arFields["LID"], $arFields["DOMAINS"]);

		if(is_set($arFields, "TEMPLATE"))
		{
			global $CACHE_MANAGER;
			if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");
			$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID)."'");
			foreach($arFields["TEMPLATE"] as $arTemplate)
			{
				if(strlen(trim($arTemplate["TEMPLATE"]))>0)
				{
					$DB->Query(
						"INSERT INTO b_site_template(SITE_ID, ".CMain::__GetConditionFName().", SORT, TEMPLATE) ".
						"VALUES('".$DB->ForSQL($arFields["LID"])."', '".$DB->ForSQL(trim($arTemplate["CONDITION"]), 255)."', ".IntVal($arTemplate["SORT"]).", '".$DB->ForSQL(trim($arTemplate["TEMPLATE"]), 255)."')");
				}
			}
		}

		return $arFields["LID"];
	}


	function Update($ID, $arFields)
	{
		global $DB, $MAIN_LANGS_CACHE, $MAIN_LANGS_ADMIN_CACHE, $CACHE_MANAGER;
		UnSet($MAIN_LANGS_CACHE[$ID]);
		UnSet($MAIN_LANGS_ADMIN_CACHE[$ID]);

		if(!$this->CheckFields($arFields, $ID))
			return false;
		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_lang SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strUpdate = $DB->PrepareUpdate("b_lang", $arFields);
		$strSql = "UPDATE b_lang SET ".$strUpdate." WHERE LID='".$DB->ForSql($ID, 2)."'";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		global $BX_CACHE_DOCROOT;
		unset($BX_CACHE_DOCROOT[$ID]);

		if(is_set($arFields, "DIR"))
			CheckDirPath($DOCUMENT_ROOT.$arFields["DIR"]);

		if(is_set($arFields, "DOMAINS"))
			self::SaveDomains($ID, $arFields["DOMAINS"]);

		if(is_set($arFields, "TEMPLATE"))
		{
			if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");
			$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID)."'");
			foreach($arFields["TEMPLATE"] as $arTemplate)
			{
				if(strlen(trim($arTemplate["TEMPLATE"]))>0)
				{
					$DB->Query(
						"INSERT INTO b_site_template(SITE_ID, ".CMain::__GetConditionFName().", SORT, TEMPLATE) ".
						"VALUES('".$DB->ForSQL($ID)."', '".$DB->ForSQL(trim($arTemplate["CONDITION"]), 255)."', ".IntVal($arTemplate["SORT"]).", '".$DB->ForSQL(trim($arTemplate["TEMPLATE"]), 255)."')");
				}
			}
		}

		return true;
	}

	function Delete($ID)
	{
		global $DB, $APPLICATION, $CACHE_MANAGER;

		$APPLICATION->ResetException();
		//�������� - ������� �� ��� ���-������ ���������� �� OnBeforeDelete
		$db_events = GetModuleEvents("main", "OnBeforeLangDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		$db_events = GetModuleEvents("main", "OnBeforeSiteDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		//�������� - ������� �� ��� �����-������ ������ ���������� �� OnDelete
		$events = GetModuleEvents("main", "OnLangDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		$events = GetModuleEvents("main", "OnSiteDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		if(!$DB->Query("DELETE FROM b_event_message_site WHERE SITE_ID='".$DB->ForSQL($ID, 2)."'"))
			return false;

		if(!$DB->Query("DELETE FROM b_lang_domain WHERE LID='".$DB->ForSQL($ID, 2)."'"))
			return false;
		if(CACHED_b_lang_domain!==false) $CACHE_MANAGER->CleanDir("b_lang_domain");

		if(!$DB->Query("UPDATE b_event_message SET LID=NULL WHERE LID='".$DB->ForSQL($ID, 2)."'"))
			return false;

		if(!$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID, 2)."'"))
			return false;
		if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");

		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");
		return $DB->Query("DELETE FROM b_lang WHERE LID='".$DB->ForSQL($ID, 2)."'", true);
	}

	function GetTemplateList($site_id)
	{
		global $DB;
		$strSql =
				"SELECT * ".
				"FROM b_site_template ".
				"WHERE SITE_ID='".$DB->ForSQL($site_id, 2)."' ".
				"ORDER BY SORT";

		$dbr = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $dbr;
	}

	///////////////////////////////////////////////////////////////////
	//������� ������� ������ ������ � ������� ����������
	///////////////////////////////////////////////////////////////////
	function GetDefList()
	{
		global $DB;
		$strSql = "SELECT L.*, L.LID as ID, L.LID as SITE_ID FROM b_lang L WHERE ACTIVE='Y' ORDER BY DEF desc, SORT";
		$sl = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $sl;
	}

	function GetSiteDocRoot($site)
	{
		if($site === false)
			$site = SITE_ID;

		global $BX_CACHE_DOCROOT;
		if(!is_set($BX_CACHE_DOCROOT, $site))
		{
			$res = CSite::GetByID($site);
			if(($ar = $res->Fetch()) && strlen($ar["DOC_ROOT"])>0)
			{
				$BX_CACHE_DOCROOT[$site] = Rel2Abs($_SERVER["DOCUMENT_ROOT"], $ar["DOC_ROOT"]);
			}
			else
				$BX_CACHE_DOCROOT[$site] = RTrim($_SERVER["DOCUMENT_ROOT"], "/\\");
		}

		return $BX_CACHE_DOCROOT[$site];
	}

	function GetSiteByFullPath($path, $bOneResult = true)
	{
		$res = Array();

		if(($p = realpath($path)))
			$path = $p;
		$path = str_replace("\\", "/", $path);
		$path = strtoupper($path);

		$db_res = CSite::GetList($by="lendir", $order="desc");
		while($ar_res = $db_res->Fetch())
		{
			$abspath = $ar_res["ABS_DOC_ROOT"].$ar_res["DIR"];
			if(($p = realpath($abspath)))
				$abspath = $p;
			$abspath = str_replace("\\", "/", $abspath);
			$abspath = strtoupper($abspath);
			if(substr($abspath, -1) <> "/")
				$abspath .= "/";
			if(strpos($path, $abspath)===0)
			{
				if($bOneResult)
					return $ar_res["ID"];
				$res[] = $ar_res["ID"];
			}
		}

		if(count($res)>0)
			return $res;

		return false;
	}

	///////////////////////////////////////////////////////////////////
	//������� ������� ������ ������
	///////////////////////////////////////////////////////////////////
	function GetList(&$by, &$order, $arFilter=Array())
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_lang!==false)
		{
			$cacheId = "b_lang".md5($by.".".$order.".".serialize($arFilter));
			if($CACHE_MANAGER->Read(CACHED_b_lang, $cacheId, "b_lang"))
			{
				$arResult = $CACHE_MANAGER->Get($cacheId);

				$res = new CDBResult;
				$res->InitFromArray($arResult);
				$res = new _CLangDBResult($res);
				return $res;
			}
		}

		$strSqlSearch = " 1=1\n";
		$bIncDomain = false;
		if(is_array($arFilter))
		{
			foreach($arFilter as $key=>$val)
			{
				if(strlen($val)<=0) continue;
				$val = $DB->ForSql($val);
				switch(strtoupper($key))
				{
					case "ACTIVE":
						if($val=="Y" || $val=="N")
							$strSqlSearch .= " AND L.ACTIVE='".$val."'\n";
						break;
					case "DEFAULT":
						if($val=="Y" || $val=="N")
							$strSqlSearch .= " AND L.DEF='".$val."'\n";
						break;
					case "NAME":
						$strSqlSearch .= " AND UPPER(L.NAME) LIKE UPPER('".$val."')\n";
						break;
					case "DOMAIN":
						$bIncDomain = true;
						$strSqlSearch .= " AND UPPER(D.DOMAIN) LIKE UPPER('".$val."')\n";
						break;
					case "IN_DIR":
						$strSqlSearch .= " AND UPPER('".$val."') LIKE ".$DB->Concat("UPPER(L.DIR)", "'%'")."\n";
						break;
					case "ID":
					case "LID":
						$strSqlSearch .= " AND L.LID='".$val."'\n";
						break;
					case "LANGUAGE_ID":
						$strSqlSearch .= " AND L.LANGUAGE_ID='".$val."'\n";
						break;
				}
			}
		}

		$strSql = "
			SELECT ".($bIncDomain ? " DISTINCT " : "")."
				L.*,
				L.LID ID,
				".$DB->Length("L.DIR").",
				".$DB->IsNull($DB->Length("L.DOC_ROOT"), "0")."
			FROM
				b_lang L
				".($bIncDomain ? " LEFT JOIN b_lang_domain D ON D.LID=L.LID " : "")."
			WHERE
				".$strSqlSearch."
			";

		$by = strtolower($by);
		$order = strtolower($order);

		if($by == "lid" || $by=="id")	$strSqlOrder = " ORDER BY L.LID ";
		elseif($by == "active")			$strSqlOrder = " ORDER BY L.ACTIVE ";
		elseif($by == "name")			$strSqlOrder = " ORDER BY L.NAME ";
		elseif($by == "dir")			$strSqlOrder = " ORDER BY L.DIR ";
		elseif($by == "lendir")			$strSqlOrder = " ORDER BY ".$DB->IsNull($DB->Length("L.DOC_ROOT"), "0").($order=="desc"? " desc":"").", ".$DB->Length("L.DIR");
		elseif($by == "def")			$strSqlOrder = " ORDER BY L.DEF ";
		else
		{
			$strSqlOrder = " ORDER BY L.SORT ";
			$by = "sort";
		}

		if($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		if(CACHED_b_lang===false)
		{
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			$arResult = array();
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $res->Fetch())
				$arResult[]=$ar;

			$CACHE_MANAGER->Set($cacheId, $arResult);

			$res = new CDBResult;
			$res->InitFromArray($arResult);
		}
		$res = new _CLangDBResult($res);
		return $res;
	}

	///////////////////////////////////////////////////////////////////
	//������� ������� ������ ����� �� ����
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CSite::GetList($ord, $by, Array("LID"=>$ID));
	}

	function GetDefSite($LID = false)
	{
		if(strlen($LID)>0)
		{
			$dbSite = CSite::GetByID($LID);
			if($dbSite->Fetch())
				return $LID;
		}

		$dbDefSites = CSite::GetDefList();
		if($arDefSite = $dbDefSites->Fetch())
			return $arDefSite["LID"];

		return false;
	}

	function IsDistinctDocRoots($arFilter=Array())
	{
		$s = false;
		$res = CSite::GetList($by, $order, $arFilter);
		while($ar = $res->Fetch())
		{
			if($s!==false && $s!=$ar["ABS_DOC_ROOT"])
				return true;
			$s = $ar["ABS_DOC_ROOT"];
		}
		return false;
	}


	///////////////////////////////////////////////////////////////////
	// Returns drop down list with langs
	///////////////////////////////////////////////////////////////////
	function SelectBox($sFieldName, $sValue, $sDefaultValue="", $sFuncName="", $field="class=\"typeselect\"")
	{
		$l = CLang::GetList(($by="sort"), ($order="asc"));
		$s = '<select name="'.$sFieldName.'" '.$field;
		if(strlen($sFuncName)>0) $s .= ' OnChange="'.$sFuncName.'"';
		$s .= '>'."\n";
		$found = false;
		while(($l_arr = $l->Fetch()))
		{
			$found = ($l_arr["LID"] == $sValue);
			$s1 .= '<option value="'.$l_arr["LID"].'"'.($found ? ' selected':'').'>['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</option>'."\n";
		}
		if(strlen($sDefaultValue)>0)
			$s .= "<option value='NOT_REF' ".($found ? "" : "selected").">".htmlspecialcharsex($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}

	function SelectBoxMulti($sFieldName, $Value)
	{
		$l = CLang::GetList(($by="sort"), ($order="asc"));
		if(is_array($Value))
			$arValue = $Value;
		else
			$arValue = Array($Value);

		$s = '';
		while($l_arr = $l->Fetch())
		{
			$s .=
				'<input type="checkbox" name="'.$sFieldName.'[]" value="'.htmlspecialcharsex($l_arr["LID"]).'" id="'.htmlspecialcharsex($l_arr["LID"]).'" class="typecheckbox"'.(in_array($l_arr["LID"], $arValue)?' checked':'').'>'.
				'<label for="'.htmlspecialcharsex($l_arr["LID"]).'">['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</label>'.
				'<br>';
		}

		return $s;
	}

	function GetNameTemplates()
	{
		return array(
			'#NOBR##NAME# #LAST_NAME##/NOBR#' => GetMessage('MAIN_NAME_JOHN_SMITH'),
			'#NOBR##LAST_NAME# #NAME##/NOBR#' => GetMessage('MAIN_NAME_SMITH_JOHN'),
			'#NOBR##NAME# #SECOND_NAME_SHORT# #LAST_NAME##/NOBR#' => GetMessage('MAIN_NAME_JOHN_L_SMITH'),
			'#NOBR##LAST_NAME# #NAME##/NOBR# #SECOND_NAME#' => GetMessage('MAIN_NAME_SMITH_JOHN_LLOYD'),
			'#LAST_NAME#, #NOBR##NAME# #SECOND_NAME##/NOBR#' => GetMessage('MAIN_NAME_SMITH_COMMA_JOHN_LLOYD'),
			'#NAME# #SECOND_NAME# #LAST_NAME#' => GetMessage('MAIN_NAME_JOHN_LLOYD_SMITH'),
			'#NOBR##NAME_SHORT# #SECOND_NAME_SHORT# #LAST_NAME##/NOBR#' => GetMessage('MAIN_NAME_J_L_SMITH'),
			'#NOBR##NAME_SHORT# #LAST_NAME##/NOBR#' => GetMessage('MAIN_NAME_J_SMITH'),
			'#NOBR##LAST_NAME# #NAME_SHORT##/NOBR#' => GetMessage('MAIN_NAME_SMITH_J'),
			'#NOBR##LAST_NAME# #NAME_SHORT# #SECOND_NAME_SHORT##/NOBR#' => GetMessage('MAIN_NAME_SMITH_J_L'),
			'#NOBR##LAST_NAME#, #NAME_SHORT##/NOBR#' => GetMessage('MAIN_NAME_SMITH_COMMA_J'),
			'#NOBR##LAST_NAME#, #NAME_SHORT# #SECOND_NAME_SHORT##/NOBR#' => GetMessage('MAIN_NAME_SMITH_COMMA_J_L')
		);
	}

	function GetNameFormatByValue($sValue)
	{
		$arNameTemplates = self::GetNameTemplates();

		foreach ($arNameTemplates as $sFormat => $sName)
		{
			if ($sValue == $sName)
				return $sFormat;
		}

		if ($sValue == GetMessage("MAIN_FORMAT_NAME_NOT_SET"))
			return "";
		else
			return self::GetDefaultNameFormat();
	}

	/**
	* Returns current name template
	*
	* If site is not defined - will look for name template for current language.
	* If there is no value for language - returns pre-defined value @see CSite::GetDefaultNameFormat
	* FORMAT_NAME constant can be set in dbconn.php
	*
	* @param bool $bUseBreaks - if true, will contain #NOBR# #/NOBR# for <nobr> tags. Default - true
	* @param string $site_id - use to get value for the specific site
	* @return string ex: #LAST_NAME# #NAME#
	*/
	function GetNameFormat($bUseBreaks = true, $site_id = "")
	{
		if ($site_id == "")
			$site_id = SITE_ID;

		if(defined("SITE_ID") && $site_id == SITE_ID) //for current site
		{
			if(defined("FORMAT_NAME"))
				$format = FORMAT_NAME;
		}

		//site value
		if ($format == "")
		{
			$db_res = CSite::GetByID($site_id);

			if ($res = $db_res->Fetch())
				$format = $res["FORMAT_NAME"];
		}

		//if not found - trying to get value for the language
		if ($format == "")
		{
			global $MAIN_LANGS_ADMIN_CACHE;
			if(!is_set($MAIN_LANGS_ADMIN_CACHE, $site_id))
			{
				$db_res = CLanguage::GetByID(LANGUAGE_ID);
				if ($res = $db_res->Fetch())
					$MAIN_LANGS_ADMIN_CACHE[$res["LID"]] = $res;
			}

			if(is_set($MAIN_LANGS_ADMIN_CACHE, LANGUAGE_ID))
				$format = strtoupper($MAIN_LANGS_ADMIN_CACHE[LANGUAGE_ID]["FORMAT_NAME"]);
		}

		//if not found - trying to get default values
		if ($format == "")
			$format = self::GetDefaultNameFormat(empty($res["LANGUAGE_ID"]) ? "" : $res["LANGUAGE_ID"]);

		if (!$bUseBreaks)
			$format = str_replace(array("#NOBR#","#/NOBR#"), array("",""), $format);

		return $format;
	}

	/**
	* Returns default name template
	* By default: Russian #LAST_NAME# #NAME#, English #NAME# #LAST_NAME#
	*
	* @param string $sLangId - language id, if we need to get value for specific language
	* @return string - one of two possible default values
	*/
	function GetDefaultNameFormat($sLangId = "")
	{
		return '#NOBR##NAME# #LAST_NAME##/NOBR#';

		// if ($sLangId == "")
		// 	$sCheckLang = LANGUAGE_ID;
		// else
		// 	$sCheckLang = $sLangId;

		// return ($sCheckLang == 'ru' ? '#NOBR##LAST_NAME# #NAME##/NOBR#' : '#NOBR##NAME# #LAST_NAME##/NOBR#');
	}

	function SelectBoxName($sFieldName, $sValue, $sDefaultValue="", $sFuncName="", $field="class=\"typeselect\"")
	{
		$arNameTemplates = self::GetNameTemplates();

		if (empty($sValue))
			$arNameTemplates["0"] = GetMessage("MAIN_FORMAT_NAME_NOT_SET");

		$s = '<select name="'.$sFieldName.'" '.$field;
		if(strlen($sFuncName)>0) $s .= ' OnChange="'.$sFuncName.'"';
		$s .= '>'."\n";
		$found = false;

		if (defined('FORMAT_NAME'))
		{
			$s1 .= '<option value="constant" selected>'.CUser::FormatName(FORMAT_NAME,
				array("NAME"		=>	GetMessage("MAIN_NAME_JOHN"),
					"LAST_NAME"		=>	GetMessage("MAIN_NAME_SMITH"),
					"SECOND_NAME"	=>	GetMessage("MAIN_NAME_LLOYD")), false).' '.GetMessage("MAIN_NAME_DEFINED_IN_DBCONN").'</option>';
		}
		else
		{
			foreach ($arNameTemplates as $template => $value) {
				$found = ($template == $sValue);
				$s1 .= '<option value="'.$value.'"'.($found ? ' selected':'').'>'.htmlspecialcharsex($value).'</option>'."\n";
			}

			if(strlen($sDefaultValue)>0)
				$s .= "<option value='NOT_REF' ".($found ? "" : "selected").">".htmlspecialcharsex($sDefaultValue)."</option>";
		}

		return $s.$s1.'</select>';
	}
}

class _CLangDBResult extends CDBResult
{

	function _CLangDBResult($res)
	{
		parent::CDBResult($res);
	}

	function Fetch()
	{
		if($res = parent::Fetch())
		{
			global $DB, $CACHE_MANAGER;
			static $arCache;
			if(!is_array($arCache))
				$arCache = Array();
			if(is_set($arCache, $res["LID"]))
				$res["DOMAINS"] = $arCache[$res["LID"]];
			else
			{
				if(CACHED_b_lang_domain===false)
				{
					$res["DOMAINS"] = "";
					$db_res = $DB->Query("SELECT * FROM b_lang_domain WHERE LID='".$res["LID"]."'");
					while($ar_res = $db_res->Fetch())
					{
						$domain = $ar_res["DOMAIN"];
						if ($domainTmp = CBXPunycode::ToUnicode($ar_res["DOMAIN"], $arErrorsTmp))
							$domain = $domainTmp;
						$res["DOMAINS"] .= $domain."\r\n";
					}
				}
				else
				{
					if($CACHE_MANAGER->Read(CACHED_b_lang_domain, "b_lang_domain", "b_lang_domain"))
					{
						$arLangDomain = $CACHE_MANAGER->Get("b_lang_domain");
					}
					else
					{
						$arLangDomain = array("DOMAIN"=>array(), "LID"=>array());
						$rs = $DB->Query("SELECT * FROM b_lang_domain ORDER BY ".$DB->Length("DOMAIN"));
						while($ar = $rs->Fetch())
						{
							$arLangDomain["DOMAIN"][]=$ar;
							$arLangDomain["LID"][$ar["LID"]][]=$ar;
						}
						$CACHE_MANAGER->Set("b_lang_domain", $arLangDomain);
					}
					$res["DOMAINS"] = "";
					if(is_array($arLangDomain["LID"][$res["LID"]]))
						foreach($arLangDomain["LID"][$res["LID"]] as $ar_res)
						{
							$domain = $ar_res["DOMAIN"];
							if ($domainTmp = CBXPunycode::ToUnicode($ar_res["DOMAIN"], $arErrorsTmp))
								$domain = $domainTmp;
							$res["DOMAINS"] .= $domain."\r\n";

						}
				}
				$res["DOMAINS"] = Trim($res["DOMAINS"]);
				$arCache[$res["LID"]] = $res["DOMAINS"];
			}

			if(trim($res["DOC_ROOT"])=="")
				$res["ABS_DOC_ROOT"] = $_SERVER["DOCUMENT_ROOT"];
			else
				$res["ABS_DOC_ROOT"] = Rel2Abs($_SERVER["DOCUMENT_ROOT"], $res["DOC_ROOT"]);

			if($res["ABS_DOC_ROOT"]!==$_SERVER["DOCUMENT_ROOT"])
				$res["SITE_URL"] = (CMain::IsHTTPS() ? "https://" : "http://").$res["SERVER_NAME"];
		}
		return $res;
	}

}

class CAllLanguage
{
	///////////////////////////////////////////////////////////////////
	//������� ������� ������ ������
	///////////////////////////////////////////////////////////////////
	function GetList(&$by, &$order, $arFilter=Array())
	{
		global $DB;
		$arSqlSearch = Array();

		if(!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for($i=0; $i<count($filter_keys); $i++)
		{
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
			if(strlen($val)<=0) continue;
			switch(strtoupper($filter_keys[$i]))
			{
			case "ACTIVE":
				if($val=="Y" || $val=="N")
					$arSqlSearch[] = "L.ACTIVE='".$val."'";
				break;
			case "NAME":
				$arSqlSearch[] = "UPPER(L.NAME) LIKE UPPER('".$val."')";
				break;
			case "ID":
			case "LID":
				$arSqlSearch[] = "L.LID='".$val."'";
				break;
			}
		}

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
		{
			if($i>0)
				$strSqlSearch .= " AND ";
			else
				$strSqlSearch = " WHERE ";

			$strSqlSearch .= " (".$arSqlSearch[$i].") ";
		}

		$strSql =
			"SELECT L.*, L.LID as ID, L.LID as LANGUAGE_ID ".
			"FROM b_language L ".
				$strSqlSearch;

		if($by == "lid" || $by=="id")	$strSqlOrder = " ORDER BY L.LID ";
		elseif($by == "active")			$strSqlOrder = " ORDER BY L.ACTIVE ";
		elseif($by == "name")			$strSqlOrder = " ORDER BY L.NAME ";
		elseif($by == "def")			$strSqlOrder = " ORDER BY L.DEF ";
		else
		{
			$strSqlOrder = " ORDER BY L.SORT ";
			$by = "sort";
		}

		if($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return $res;
	}

	///////////////////////////////////////////////////////////////////
	//������� ������� ������ ����� �� ����
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CLanguage::GetList($o, $b, Array("LID"=>$ID));
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if($ID===false && is_set($arFields, "LID") && strlen($arFields["LID"])!=2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_LID")." ";
			$arMsg[] = array("id"=>"LID", "text"=> GetMessage("BAD_LANG_LID"));
		}
		if(is_set($arFields, "NAME") && strlen($arFields["NAME"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_NAME")." ";
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("BAD_LANG_NAME"));
		}
		if(is_set($arFields, "SORT") && intval($arFields["SORT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_SORT")." ";
			$arMsg[] = array("id"=>"SORT", "text"=> GetMessage("BAD_LANG_SORT"));
		}
		if(is_set($arFields, "FORMAT_DATE") && strlen($arFields["FORMAT_DATE"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_FORMAT_DATE")." ";
			$arMsg[] = array("id"=>"FORMAT_DATE", "text"=> GetMessage("BAD_LANG_FORMAT_DATE"));
		}
		if(is_set($arFields, "FORMAT_DATETIME") && strlen($arFields["FORMAT_DATETIME"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_FORMAT_DATETIME")." ";
			$arMsg[] = array("id"=>"FORMAT_DATETIME", "text"=> GetMessage("BAD_LANG_FORMAT_DATETIME"));
		}
		if(is_set($arFields, "FORMAT_NAME") && strlen($arFields["FORMAT_NAME"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_FORMAT_NAME")." ";
			$arMsg[] = array("id"=>"FORMAT_NAME", "text"=> GetMessage("BAD_LANG_FORMAT_NAME"));
		}
		if(is_set($arFields, "CHARSET") && strlen($arFields["CHARSET"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_CHARSET")." ";
			$arMsg[] = array("id"=>"CHARSET", "text"=> GetMessage("BAD_LANG_CHARSET"));
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		if($ID===false)
		{
			$r = $DB->Query("SELECT 'x' FROM b_language WHERE LID='".$DB->ForSQL($arFields["LID"], 2)."'");
			if($r->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("BAD_LANG_DUP")." ";
				$e = new CAdminException(Array(Array("id"=>"LID", "text" =>GetMessage("BAD_LANG_DUP"))));
				$GLOBALS["APPLICATION"]->ThrowException($e);
				return false;
			}
		}

		return true;
	}

	function Add($arFields)
	{
		global $DB, $DOCUMENT_ROOT;

		if(!$this->CheckFields($arFields))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DIRECTION") && $arFields["DIRECTION"]!="Y")
			$arFields["DIRECTION"]="N";

		$arInsert = $DB->PrepareInsert("b_language", $arFields);

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_language SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strSql =
			"INSERT INTO b_language(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $arFields["LID"];
	}


	function Update($ID, $arFields)
	{
		global $DB, $MAIN_LANGS_CACHE, $MAIN_LANGS_ADMIN_CACHE;
		UnSet($MAIN_LANGS_CACHE[$ID]);
		UnSet($MAIN_LANGS_ADMIN_CACHE[$ID]);

		if(!$this->CheckFields($arFields, $ID))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DIRECTION") && $arFields["DIRECTION"]!="Y")
			$arFields["DIRECTION"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_language SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strUpdate = $DB->PrepareUpdate("b_language", $arFields);
		$strSql = "UPDATE b_language SET ".$strUpdate." WHERE LID='".$DB->ForSql($ID, 2)."'";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return true;
	}

	function Delete($ID)
	{
		global $DB;

		$db_res = CLang::GetList(($b=""), ($o=""), Array("LANGUAGE_ID" => $ID));
		if($db_res->Fetch())
			return false;

		//�������� - ������� �� ��� ���-������ ���������� �� OnBeforeDelete
		$bCanDelete = true;
		$db_events = GetModuleEvents("main", "OnBeforeLanguageDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		//�������� - ������� �� ��� �����-������ ������ ���������� �� OnDelete
		$events = GetModuleEvents("main", "OnLanguageDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		return $DB->Query("DELETE FROM b_language WHERE LID='".$DB->ForSQL($ID, 2)."'", true);
	}

	///////////////////////////////////////////////////////////////////
	//������� ������ ���������� ������ ������
	///////////////////////////////////////////////////////////////////
	function SelectBox($sFieldName, $sValue, $sDefaultValue="", $sFuncName="", $field="class=\"typeselect\"")
	{
		$l = CLanguage::GetList(($by="sort"), ($order="asc"));
		$s = '<select name="'.$sFieldName.'" '.$field;
		if(strlen($sFuncName)>0) $s .= ' OnChange="'.$sFuncName.'"';
		$s .= '>'."\n";
		$found = false;
		while(($l_arr = $l->Fetch()))
		{
			$found = ($l_arr["LID"] == $sValue);
			$s1 .= '<option value="'.$l_arr["LID"].'"'.($found ? ' selected':'').'>['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</option>'."\n";
		}
		if(strlen($sDefaultValue)>0)
			$s .= "<option value='' ".($found ? "" : "selected").">".htmlspecialcharsex($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}

	function GetLangSwitcherArray()
	{
		global $DB, $REQUEST_URI, $DOCUMENT_ROOT, $APPLICATION;

		$result = Array();
		$db_res = $DB->Query("SELECT * FROM b_language WHERE ACTIVE='Y' ORDER BY SORT");
		while($ar = $db_res->Fetch())
		{
			$ar["NAME"] = htmlspecialcharsbx($ar["NAME"]);
			$ar["SELECTED"] = ($ar["LID"]==LANG);

			global $QUERY_STRING;
			$p = rtrim(str_replace("&#", "#", preg_replace("/lang=[^&#]*&*/", "", $QUERY_STRING)), "&");
			$ar["PATH"] = $APPLICATION->GetCurPage()."?lang=".$ar["LID"].($p <> ''? '&amp;'.htmlspecialcharsbx($p) : '');

			$result[] = $ar;
		}
		return $result;
	}
}

class CLanguage extends CAllLanguage
{
}

class CLangAdmin extends CLanguage
{
}

$SHOWIMAGEFIRST=false;

function ShowImage($PICTURE_ID, $iMaxW=0, $iMaxH=0, $sParams=false, $strImageUrl="", $bPopup=false, $strPopupTitle=false,$iSizeWHTTP=0, $iSizeHHTTP=0)
{
	return CFile::ShowImage($PICTURE_ID, $iMaxW, $iMaxH, $sParams, $strImageUrl, $bPopup, $strPopupTitle,$iSizeWHTTP, $iSizeHHTTP);
}


class CAllFilterQuery
{
	var $cnt = 0;
	var $m_query;
	var $m_words;
	var $m_fields;
	var $m_kav;
	var $default_query_type;
	var $rus_bool_lang;
	var $error;
	var $procent;
	var $ex_sep;
	var $clob;
	var $div_fields;

	function __construct($default_query_type = "and", $rus_bool_lang = "yes", $procent="Y", $ex_sep = array(), $clob="N", $div_fields="Y", $clob_upper="N")
	{
		$this->CFilterQuery($default_query_type, $rus_bool_lang, $procent, $ex_sep, $clob, $div_fields, $clob_upper);
	}

	/*
	$default_query_type - ������ ��� �������� �� ���������
	$rus_bool_lang - ��������� �� ������� ������ - ��, �, ���
	$ex_sep - ������ �������� ������� �� ���� ������� ������������� ����
	*/
	function CFilterQuery($default_query_type = "and", $rus_bool_lang = "yes", $procent="Y", $ex_sep = array(), $clob="N", $div_fields="Y", $clob_upper="N")
	{
		$this->m_query  = "";
		$this->m_fields = "";
		$this->default_query_type = $default_query_type;
		$this->rus_bool_lang = $rus_bool_lang;
		$this->m_kav = array();
		$this->error = "";
		$this->procent = $procent;
		$this->ex_sep = $ex_sep;
		$this->clob = $clob;
		$this->clob_upper = $clob_upper;
		$this->div_fields = $div_fields;
	}

	function GetQueryString($fields, $query)
	{
		$this->m_words = Array();
		if($this->div_fields=="Y")
			$this->m_fields = explode(",", $fields);
		else
			$this->m_fields = $fields;
		if(!is_array($this->m_fields))
			$this->m_fields=array($this->m_fields);

		$query = $this->CutKav($query);
		$query = $this->ParseQ($query);
		if($query == "( )" || strlen($query)<=0)
		{
			$this->error=GetMessage("FILTER_ERROR3");
			$this->errorno=3;
			return false;
		}
		$query = $this->PrepareQuery($query);

		return $query;
	}

	function CutKav($query)
	{
		$bdcnt = 0;
		while (preg_match("/\"([^\"]*)\"/",$query,$pt))
		{
			$res = $pt[1];
			if(strlen(trim($pt[1]))>0)
			{
				$trimpt = $bdcnt."cut5";
				$this->m_kav[$trimpt] = $res;
				$query = str_replace("\"".$pt[1]."\"", " ".$trimpt." ", $query);
			}
			else
			{
				$query = str_replace("\"".$pt[1]."\"", " ", $query);
			}
			$bdcnt++;
			if($bdcnt>100) break;
		}

		$bdcnt = 0;
		while (preg_match("/'([^']*)'/",$query,$pt))
		{
			$res = $pt[1];
			if(strlen(trim($pt[1]))>0)
			{
				$trimpt = $bdcnt."cut6";
				$this->m_kav[$trimpt] = $res;
				$query = str_replace("'".$pt[1]."'", " ".$trimpt." ", $query);
			}
			else
			{
				$query = str_replace("'".$pt[1]."'", " ", $query);
			}
			$bdcnt++;
			if($bdcnt>100) break;
		}
		return $query;
	}

	function ParseQ($q)
	{
		$q = trim($q);
		if(strlen($q) <= 0)
			return '';

		$q=$this->ParseStr($q);

		$q = str_replace(
			array("&"   , "|"   , "~"  , "("  , ")"),
			array(" && ", " || ", " ! ", " ( ", " ) "),
			$q
		);
		$q="( $q )";
		$q = preg_replace("/\\s+/".BX_UTF_PCRE_MODIFIER, " ", $q);

		return $q;
	}

	function ParseStr($qwe)
	{
		$qwe=trim($qwe);

		$qwe=preg_replace("/ {0,}\\+ {0,}/", "&", $qwe);

		$qwe=preg_replace("/ {0,}([()|~]) {0,}/", "\\1", $qwe);

		// default query type is and
		if(strtolower($this->default_query_type) == 'or')
			$default_op = "|";
		else
			$default_op = "&";

		$qwe=preg_replace("/( {1,}|\\&\\|{1,}|\\|\\&{1,})/", $default_op, $qwe);

		// remove unnesessary boolean operators
		$qwe=preg_replace("/\\|+/", "|", $qwe);
		$qwe=preg_replace("/\\&+/", "&", $qwe);
		$qwe=preg_replace("/\\~+/", "~", $qwe);
		$qwe=preg_replace("/\\|\\&\\|/", "&", $qwe);
		$qwe=preg_replace("/[|&~]+$/", "", $qwe);
		$qwe=preg_replace("/^[|&]+/", "", $qwe);

		// transform "w1 ~w2" -> "w1 default_op ~ w2"
		// ") ~w" -> ") default_op ~w"
		// "w ~ (" -> "w default_op ~("
		// ") w" -> ") default_op w"
		// "w (" -> "w default_op ("
		// ")(" -> ") default_op ("

		$qwe=preg_replace("/([^&~|()]+)~([^&~|()]+)/", "\\1".$default_op."~\\2", $qwe);
		$qwe=preg_replace("/\\)~{1,}/", ")".$default_op."~", $qwe);
		$qwe=preg_replace("/~{1,}\\(/", ($default_op=="|"? "~|(": "&~("), $qwe);
		$qwe=preg_replace("/\\)([^&~|()]+)/", ")".$default_op."\\1", $qwe);
		$qwe=preg_replace("/([^&~|()]+)\\(/", "\\1".$default_op."(", $qwe);
		$qwe=preg_replace("/\\) *\\(/", ")".$default_op."(", $qwe);

		// remove unnesessary boolean operators
		$qwe=preg_replace("/\\|+/", "|", $qwe);
		$qwe=preg_replace("/\\&+/", "&", $qwe);

		// remove errornous format of query - ie: '(&', '&)', '(|', '|)', '~&', '~|', '~)'
		$qwe=preg_replace("/\\(\\&{1,}/", "(", $qwe);
		$qwe=preg_replace("/\\&{1,}\\)/", ")", $qwe);
		$qwe=preg_replace("/\\~{1,}\\)/", ")", $qwe);
		$qwe=preg_replace("/\\(\\|{1,}/", "(", $qwe);
		$qwe=preg_replace("/\\|{1,}\\)/", ")", $qwe);
		$qwe=preg_replace("/\\~{1,}\\&{1,}/", "&", $qwe);
		$qwe=preg_replace("/\\~{1,}\\|{1,}/", "|", $qwe);

		$qwe=preg_replace("/\\(\\)/", "", $qwe);
		$qwe=preg_replace("/^[|&]{1,}/", "", $qwe);
		$qwe=preg_replace("/[|&~]{1,}\$/", "", $qwe);
		$qwe=preg_replace("/\\|\\&/", "&", $qwe);
		$qwe=preg_replace("/\\&\\|/", "|", $qwe);

		// remove unnesessary boolean operators
		$qwe=preg_replace("/\\|+/", "|", $qwe);
		$qwe=preg_replace("/\\&+/", "&", $qwe);

		return($qwe);
	}

	function PrepareQuery($q)
	{
		$state = 0;
		$qu = "";
		$n = 0;
		$this->error = "";

		$t=strtok($q," ");

		while (($t!="") && ($this->error==""))
		{
			switch ($state)
			{
			case 0:
				if(($t=="||") || ($t=="&&") || ($t==")"))
				{
					$this->error=GetMessage("FILTER_ERROR2")." ".$t;
					$this->errorno=2;
				}
				elseif($t=="!")
				{
					$state=0;
					$qu="$qu NOT ";
					break;
				}
				elseif($t=="(")
				{
					$n++;
					$state=0;
					$qu="$qu(";
				}
				else
				{
					$state=1;
					$qu="$qu ".$this->BuildWhereClause($t)." ";
				}
				break;

			case 1:
				if(($t=="||") || ($t=="&&"))
				{
					$state=0;
					if($t=='||') $qu="$qu OR ";
					else $qu="$qu AND ";
				}
				elseif($t==")")
				{
					$n--;
					$state=1;
					$qu="$qu)";
				}
				else
				{
					$this->error=GetMessage("FILTER_ERROR2")." ".$t;
					$this->errorno=2;
				}
				break;
			}
			$t=strtok(" ");
		}

		if(($this->error=="") && ($n != 0))
		{
			$this->error=GetMessage("FILTER_ERROR1");
			$this->errorno=1;
		}
		if($this->error!="") return 0;

		return $qu;
	}
}

class CAllLang extends CAllSite
{
}

class CSiteTemplate
{
	function GetList($arOrder=array(), $arFilter=array(), $arSelect=false)
	{
		global $APPLICATION;

		if(isset($arFilter["ID"]) && !is_array($arFilter["ID"]))
			$arFilter["ID"] = array($arFilter["ID"]);

		$arRes = array();
		$path = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates";
		$handle  = opendir($path);
		if($handle)
		{
			while(($file = readdir($handle)) !== false)
			{
				if($file == "." || $file == ".." || !is_dir($path."/".$file))
					continue;

				if($file == ".default")
					continue;

				if(isset($arFilter["ID"]) && !in_array($file, $arFilter["ID"]))
					continue;

				$arTemplate = array("DESCRIPTION"=>"");

				if(file_exists(($fname = $path."/".$file."/lang/".LANGUAGE_ID."/description.php")))
					__IncludeLang($fname, false, true);
				elseif(file_exists(($fname = $path."/".$file."/lang/".LangSubst(LANGUAGE_ID)."/description.php")))
					__IncludeLang($fname, false, true);

				if(file_exists(($fname = $path."/".$file."/description.php")))
					include($fname);

				$arTemplate["ID"] = $file;
				if(!isset($arTemplate["NAME"]))
					$arTemplate["NAME"] = $file;

				if($arSelect === false || in_array("SCREENSHOT", $arSelect))
				{
					if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/lang/".LANGUAGE_ID."/screen.gif"))
						$arTemplate["SCREENSHOT"] = BX_PERSONAL_ROOT."/templates/".$file."/lang/".LANGUAGE_ID."/screen.gif";
					elseif(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/screen.gif"))
						$arTemplate["SCREENSHOT"] = BX_PERSONAL_ROOT."/templates/".$file."/screen.gif";
					else
						$arTemplate["SCREENSHOT"] = false;

					if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/lang/".LANGUAGE_ID."/preview.gif"))
						$arTemplate["PREVIEW"] = BX_PERSONAL_ROOT."/templates/".$file."/lang/".LANGUAGE_ID."/preview.gif";
					elseif(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/preview.gif"))
						$arTemplate["PREVIEW"] = BX_PERSONAL_ROOT."/templates/".$file."/preview.gif";
					else
						$arTemplate["PREVIEW"] = false;
				}

				if($arSelect === false || in_array("CONTENT", $arSelect))
					$arTemplate["CONTENT"] = $APPLICATION->GetFileContent($path."/".$file."/header.php")."#WORK_AREA#".$APPLICATION->GetFileContent($path."/".$file."/footer.php");

				if($arSelect === false || in_array("STYLES", $arSelect))
				{
					if(file_exists($path."/".$file."/styles.css"))
					{
						$arTemplate["STYLES"] = $APPLICATION->GetFileContent($path."/".$file."/styles.css");
						$arTemplate["STYLES_TITLE"] = CSiteTemplate::__GetByStylesTitle($path."/".$file."/.styles.php");
					}

					if(file_exists($path."/".$file."/template_styles.css"))
						$arTemplate["TEMPLATE_STYLES"] = $APPLICATION->GetFileContent($path."/".$file."/template_styles.css");
				}

				$arRes[] = $arTemplate;
			}
			closedir($handle);
		}
		$db_res = new CDBResult;
		$db_res->InitFromArray($arRes);

		return $db_res;
	}

	function __GetByStylesTitle($file)
	{
		if(file_exists($file))
			return include($file);
		return false;
	}

	function GetByID($ID)
	{
		return CSiteTemplate::GetList(array(), array("ID"=>$ID));
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if($ID===false)
		{
			if(strlen($arFields["ID"])<=0)
				$this->LAST_ERROR .= GetMessage("MAIN_ENTER_TEMPLATE_ID")." ";
			elseif(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]))
				$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_ID_EX")." ";

			if(!is_set($arFields, "CONTENT"))
				$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_CONTENT_NA")." ";
		}

		if(is_set($arFields, "CONTENT") && strlen($arFields["CONTENT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_CONTENT_NA")." ";
			$arMsg[] = array("id"=>"CONTENT", "text"=> GetMessage("MAIN_TEMPLATE_CONTENT_NA"));
		}
		elseif(is_set($arFields, "CONTENT") && strpos($arFields["CONTENT"], "#WORK_AREA#")===false)
		{
			$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_WORKAREA_NA")." ";
			$arMsg[] = array("id"=>"CONTENT", "text"=> GetMessage("MAIN_TEMPLATE_WORKAREA_NA"));
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}

	function Add($arFields)
	{
		if(!$this->CheckFields($arFields))
			return false;

		global $APPLICATION;
		CheckDirPath($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]);
		if(is_set($arFields, "CONTENT"))
		{
			$p = strpos($arFields["CONTENT"], "#WORK_AREA#");
			$header = substr($arFields["CONTENT"], 0, $p);
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/header.php", $header);
			$footer = substr($arFields["CONTENT"], $p + strlen("#WORK_AREA#"));
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/footer.php", $footer);
		}
		if(is_set($arFields, "STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/styles.css", $arFields["STYLES"]);
		}

		if(is_set($arFields, "TEMPLATE_STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/template_styles.css", $arFields["TEMPLATE_STYLES"]);
		}

		if(is_set($arFields, "NAME") || is_set($arFields, "DESCRIPTION"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/description.php",
				'<'.'?'.
				'$arTemplate = Array("NAME"=>"'.EscapePHPString($arFields['NAME']).'", "DESCRIPTION"=>"'.EscapePHPString($arFields['DESCRIPTION']).'");'.
				'?'.'>'
				);
		}

		return $arFields["ID"];
	}


	function Update($ID, $arFields)
	{
		global $APPLICATION;

		if(!$this->CheckFields($arFields, $ID))
			return false;

		CheckDirPath($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID);
		if(is_set($arFields, "CONTENT"))
		{
			$p = strpos($arFields["CONTENT"], "#WORK_AREA#");
			$header = substr($arFields["CONTENT"], 0, $p);
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/header.php", $header);
			$footer = substr($arFields["CONTENT"], $p + strlen("#WORK_AREA#"));
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/footer.php", $footer);
		}
		if(is_set($arFields, "STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/styles.css", $arFields["STYLES"]);
		}

		if(is_set($arFields, "TEMPLATE_STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/template_styles.css", $arFields["TEMPLATE_STYLES"]);
		}

		if(is_set($arFields, "NAME") || is_set($arFields, "DESCRIPTION"))
		{
			$db_t = CSiteTemplate::GetList(array(), array("ID"=>$ID));
			$ar_t = $db_t->Fetch();
			if(!is_set($arFields, "NAME"))
				$arFields["NAME"] = $ar_t["NAME"];
			if(!is_set($arFields, "DESCRIPTION"))
				$arFields["DESCRIPTION"] = $ar_t["DESCRIPTION"];
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/description.php",
				'<'.'?'.
				'$arTemplate = Array("NAME"=>"'.EscapePHPString($arFields['NAME']).'", "DESCRIPTION"=>"'.EscapePHPString($arFields['DESCRIPTION']).'");'.
				'?'.'>'
				);
		}

		return true;
	}

	function Delete($ID)
	{
		global $DB;
		if($ID==".default")
			return false;
		DeleteDirFilesEx(BX_PERSONAL_ROOT."/templates/".$ID);
		return true;
	}



	function GetContent($ID)
	{
		if(strlen($ID)<=0)
			$arRes = Array();
		else
			$arRes = CSiteTemplate::DirsRecursive($ID);
		$db_res = new CDBResult;
		$db_res->InitFromArray($arRes);
		return $db_res;
	}


	function DirsRecursive($ID, $path="", $depth=0, $maxDepth=1)
	{
		$arRes = Array();
		$depth++;

		GetDirList(BX_PERSONAL_ROOT."/templates/".$ID."/".$path, $arDirsTmp, $arResTmp);
		foreach($arResTmp as $file)
		{
			switch($file["NAME"])
			{
			case "chain_template.php":
				$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_NAV");
				break;
			case "":
				$file["DESCRIPTION"] = "";
				break;
			default:
				if(($p=strpos($file["NAME"], ".menu_template.php"))!==false)
					$file["DESCRIPTION"] = str_replace("#MENU_TYPE#", substr($file["NAME"], 0, $p), GetMessage("MAIN_TEMPLATE_MENU"));
				elseif(($p=strpos($file["NAME"], "authorize_registration.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_AUTH_REG");
				elseif(($p=strpos($file["NAME"], "forgot_password.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_SEND_PWD");
				elseif(($p=strpos($file["NAME"], "change_password.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_CHN_PWD");
				elseif(($p=strpos($file["NAME"], "authorize.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_AUTH");
				elseif(($p=strpos($file["NAME"], "registration.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_REG");
			}
			$arRes[] = $file;
		}

		$nTemplateLen = strlen(BX_PERSONAL_ROOT."/templates/".$ID."/");
		foreach($arDirsTmp as $dir)
		{
			$arDir = $dir;
			$arDir["DEPTH_LEVEL"] = $depth;
			$arRes[] = $arDir;

			if($depth < $maxDepth)
			{
				$dirPath = substr($arDir["ABS_PATH"], $nTemplateLen);
				$arRes = array_merge($arRes, CSiteTemplate::DirsRecursive($ID, $dirPath, $depth, $maxDepth));
			}
		}
		return $arRes;
	}
}

class CApplicationException
{
	var $msg, $id;
	function CApplicationException($msg, $id = false)
	{
		$this->msg = $msg;
		$this->id = $id;
	}

	function GetString()
	{
		return $this->msg;
	}

	function GetID()
	{
		return $this->id;
	}
}

class CAdminException extends CApplicationException
{
	var $messages;
	function CAdminException($messages, $id = false)
	{
		//array("id"=>"", "text"=>""), array(...), ...
		$this->messages = $messages;
		$s = "";
		foreach($this->messages as $msg)
			$s .= $msg["text"]."<br>";
		parent::CApplicationException($s, $id);
	}

	function GetMessages()
	{
		return $this->messages;
	}

	function AddMessage($message)
	{
		$this->messages[]=$message;
		$this->msg.=$message["text"]."<br>";
	}
}

class CCaptchaAgent
{
	function DeleteOldCaptcha($sec = 3600)
	{
		global $DB;

		$sec = intval($sec);

		$time = $DB->CharToDateFunction(GetTime(time()-$sec,"FULL"));
		if (!$DB->Query("DELETE FROM b_captcha WHERE DATE_CREATE <= ".$time))
			return false;

		return "CCaptchaAgent::DeleteOldCaptcha(".$sec.");";
	}
}

class CDebugInfo
{
	var $start_time, $cnt_query, $query_time;
	var $cache_size;
	var $arQueryDebugSave;
	var $arResult;
	static $level = 0;
	var $is_comp = true;

	function __construct($is_comp = true)
	{
		$this->is_comp = $is_comp;
	}

	function Start()
	{
		global $DB;
		if($this->is_comp)
			self::$level++;

		$this->cache_size = $GLOBALS["CACHE_STAT_BYTES"];
		$GLOBALS["CACHE_STAT_BYTES"] = 0;

		$this->start_time = getmicrotime();
		if($DB->ShowSqlStat)
		{
			$this->cnt_query = $DB->cntQuery;
			$DB->cntQuery = 0;
			$this->query_time = $DB->timeQuery;
			$DB->timeQuery = 0;
			$this->arQueryDebugSave = $DB->arQueryDebug;
			$DB->arQueryDebug = array();
		}
	}

	function Stop($rel_path="", $path="", $cache_type="")
	{
		global $DB, $APPLICATION;
		if($this->is_comp)
			self::$level--;

		$result = "";

		$this->arResult = array(
			"PATH" => $path,
			"REL_PATH" => $rel_path,
			"QUERY_COUNT" => 0,
			"QUERY_TIME" => 0,
			"QUERIES" => array(),
			"TIME" => (getmicrotime() - $this->start_time),
			"BX_STATE" => $GLOBALS["BX_STATE"],
			"CACHE_TYPE" => $cache_type,
			"CACHE_SIZE" => $GLOBALS["CACHE_STAT_BYTES"],
			"LEVEL" => self::$level,
		);
		$GLOBALS["CACHE_STAT_BYTES"] += $this->cache_size;

		if($DB->ShowSqlStat)
		{
			if($DB->cntQuery)
			{
				$this->arResult["QUERY_COUNT"] = $DB->cntQuery;
				$this->arResult["QUERY_TIME"] = $DB->timeQuery;
				$this->arResult["QUERIES"] = $DB->arQueryDebug;
			}
			$DB->arQueryDebug = $this->arQueryDebugSave;
			$DB->cntQuery = $this->cnt_query;
			$DB->timeQuery = $this->query_time;
		}

		$APPLICATION->arIncludeDebug[] = $this->arResult;
	}

	function Output($rel_path="", $path="", $cache_type="")
	{
		global $DB, $APPLICATION;

		$this->Stop($rel_path, $path, $cache_type);
		$result = "";

		$result .= '<div class="bx-component-debug">';
		$result .= ($rel_path<>""? $rel_path.": ":"")."<nobr>".round($this->arResult["TIME"], 4)." ".GetMessage("main_incl_file_sec")."</nobr>";
		if($this->arResult["QUERY_COUNT"])
		{
				$result .= '; <a title="'.GetMessage("main_incl_file_sql_stat").'" href="javascript:BX_DEBUG_INFO_'.(count($APPLICATION->arIncludeDebug)-1).'.Show(); BX_DEBUG_INFO_'.(count($APPLICATION->arIncludeDebug)-1).'.ShowDetails(\'BX_DEBUG_INFO_'.(count($APPLICATION->arIncludeDebug)-1).'_1\'); ">'.GetMessage("main_incl_file_sql").' '.($this->arResult["QUERY_COUNT"]).' ('.round($this->arResult["QUERY_TIME"], 4).' '.GetMessage("main_incl_file_sec").')</a>';
				//$result .= '; <a title="'.GetMessage("main_incl_file_sql_stat").'" href="javascript:jsDebugWindow.Show(\'BX_DEBUG_INFO_'.(count($APPLICATION->arIncludeDebug)-1).'\')">'.GetMessage("main_incl_file_sql").' '.($this->arResult["QUERY_COUNT"]).' ('.round($this->arResult["QUERY_TIME"], 4).' '.GetMessage("main_incl_file_sec").')</a>';
		}
		if($this->arResult["CACHE_SIZE"])
			$result .= "<nobr>; ".GetMessage("main_incl_cache_stat")." ".CFile::FormatSize($this->arResult["CACHE_SIZE"], 0)."</nobr>";
		$result .= "</div>";

		return $result;
	}
}
?>
