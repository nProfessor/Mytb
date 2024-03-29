<?
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tr.socialservices/classes/general/descriptions.php");

//manager to operate with services
class TrCSocServAuthManager
{
	protected static $arAuthServices = false;

	public function __construct()
	{
		if(!is_array(self::$arAuthServices))
		{
			self::$arAuthServices = array();

			$db_events = GetModuleEvents("tr.socialservices", "OnAuthServicesBuildList");
			while($arEvent = $db_events->Fetch())
			{
				$res = ExecuteModuleEventEx($arEvent);
				if(is_array($res))
				{
					if(!is_array($res[0]))
						$res = array($res);
					foreach($res as $serv)
						self::$arAuthServices[$serv["ID"]] = $serv;
				}
			}

			//services depend on current site
			$suffix = TrCSocServAuth::OptionsSuffix();
			self::$arAuthServices = self::AppyUserSettings($suffix);
		}
	}
	
	protected function AppyUserSettings($suffix)
	{
		$arAuthServices = self::$arAuthServices;

		//user settings: sorting, active
		$arServices = unserialize(COption::GetOptionString("tr.socialservices", "tr_auth_services".$suffix, ""));
		if(is_array($arServices))
		{
			$i = 0;
			foreach($arServices as $serv=>$active)
			{
				if(isset($arAuthServices[$serv]))
				{
					$arAuthServices[$serv]["__sort"] = $i++;
					$arAuthServices[$serv]["__active"] = ($active == "Y");
				}
			}
			uasort($arAuthServices, array('TrCSocServAuthManager', 'Cmp'));
		}
		return $arAuthServices;
	}

	public function Cmp($a, $b)
	{
		if($a["__sort"] == $b["__sort"])
			 return 0; 
		return ($a["__sort"] < $b["__sort"])? -1 : 1;
	}
	
	public function GetAuthServices($suffix)
	{
		//$suffix indicates site specific or common options
		return self::AppyUserSettings($suffix);
	}

	public function GetActiveAuthServices($arParams)
	{
		$aServ = array();
		foreach(self::$arAuthServices as $key=>$service)
		{
			if($service["__active"] === true && $service["DISABLED"] !== true)
			{
				$cl = new $service["CLASS"];
				if(is_callable(array($cl, "CheckSettings")))
					if(!call_user_func_array(array($cl, "CheckSettings"), array()))
						continue;

				if(is_callable(array($cl, "GetFormHtml")))
					$service["FORM_HTML"] = call_user_func_array(array($cl, "GetFormHtml"), array($arParams));

				$aServ[$key] = $service;
			}
		}
		return $aServ;
	}
	
	public function GetSettings()
	{
		$arOptions = array();
		foreach(self::$arAuthServices as $key=>$service)
		{
			if(is_callable(array($service["CLASS"], "GetSettings")))
			{
				$arOptions[] = htmlspecialchars($service["NAME"]);
				$options = call_user_func_array(array($service["CLASS"], "GetSettings"), array());
				if(is_array($options))
					foreach($options as $opt)
						$arOptions[] = $opt;
			}
		}
		return $arOptions;
	}
	
	public function Authorize($service_id)
	{
	//	$trace=debug_backtrace();
//		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth_trace.txt',var_export($trace,true));
		if(isset(self::$arAuthServices[$service_id]))
		{
			$service = self::$arAuthServices[$service_id];
//			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_au.txt',var_export($service["CLASS"],true));
			if($service["__active"] === true && $service["DISABLED"] !== true)
			{
				$cl = new $service["CLASS"];
				if(is_callable(array($cl, "Authorize")))
					return call_user_func_array(array($cl, "Authorize"), array());
			}
		}
		return false;
	}
	
	public function GetError($service_id, $error_code)
	{
		if(isset(self::$arAuthServices[$service_id]))
		{
			$service = self::$arAuthServices[$service_id];
			if(is_callable(array($service["CLASS"], "GetError")))
				return call_user_func_array(array($service["CLASS"], "GetError"), array($error_code));
			return GetMessage("socserv_controller_error", array("#SERVICE_NAME#"=>$service["NAME"]));
		}
		return '';
	}
}

//base class for auth services
class TrCSocServAuth
{
	protected static $settingsSuffix = false;
	
	public $is_new_user = false;

	public function GetSettings()
	{
		return false;
	}

	public function CheckSettings()
	{
		$arSettings = $this->GetSettings();
		if(is_array($arSettings))
		{
			foreach($arSettings as $sett)
				if(is_array($sett) && !array_key_exists("note", $sett))
					if(self::GetOption($sett[0]) == '')
						return false;
		}
		return true;
	}

	public static function OptionsSuffix()
	{
		//settings depend on current site
		$arUseOnSites = unserialize(COption::GetOptionString("tr.socialservices", "use_on_sites", ""));
		return ($arUseOnSites[SITE_ID] == "Y"? '_bx_site_'.SITE_ID : '');
	}
	
	public static function GetOption($opt)
	{
		if(self::$settingsSuffix === false)
			self::$settingsSuffix = self::OptionsSuffix();

		return COption::GetOptionString("tr.socialservices", $opt.self::$settingsSuffix);
	}
	
	public function AuthorizeUser($arFields)
	{
		if(!isset($arFields['XML_ID']) || $arFields['XML_ID'] == '')
			return false;
		if( (!isset($arFields['EXTERNAL_AUTH_ID']) && !isset($arFields['WORK_NOTES']) ) || ( $arFields['EXTERNAL_AUTH_ID'] == '' && $arFields['WORK_NOTES'] == '' ) )
			return false;

		$dbUsers = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID']));
        
        /** Facebook
         * Достаем акаунт с таким же email (последний посещенный)
         * и если он будет, авторизуем чувака сразу в этот акаунт и пишем ему токен
        */
        if($arFields['WORK_NOTES'] == 'Facebook'){
            $sql = "SELECT * 
            	FROM b_user 
            	WHERE EMAIL = '$arFields[EMAIL]'
            	ORDER BY LAST_LOGIN DESC";
            global $DB;
            $Fbdbres = $DB->Query($sql);
            $arFbUser = $Fbdbres->Fetch();
        }
       
        if($arFields['WORK_NOTES'] == 'VKontakte'){
            $sql = "SELECT *
                        FROM b_user
                        WHERE XML_ID = '$arFields[XML_ID]'
                                AND WORK_NOTES = 'VKontakte'
                        ORDER BY LAST_LOGIN DESC";
            global $DB;
                $VKdbres = $DB->Query($sql);
                $arVKUser = $VKdbres->Fetch();
        }

 
        //Тут вбудуйщем можно будет вынести условие $arFbUser['ID'] наверх
		if($arUser = $dbUsers->Fetch())
		{
			$USER_ID = $arUser["ID"];
		}
        elseif($arFbUser['ID'])
        {
            $USER_ID = $arFbUser["ID"];
        }
		elseif($arVKUser['ID'])
		{
			$USER_ID = $arVKUser["ID"];
		}
		else
		{
			$arFields['PASSWORD'] = randString(30); //not necessary but...
			$arFields['LID'] = SITE_ID;

			$def_group = COption::GetOptionString('main', 'new_user_registration_def_group', '');
			if($def_group <> '')
				$arFields['GROUP_ID'] = explode(',', $def_group);

			if(!($USER_ID = $GLOBALS["USER"]->Add($arFields)))
				return false;

			$this->is_new_user = true;
		}
		$GLOBALS["USER"]->Authorize($USER_ID);
		return true;
	}

	public function AuthorizeUser_TMP($arFields)
	{
		if(!isset($arFields['XML_ID']) || $arFields['XML_ID'] == '')
			return false;
		if(!isset($arFields['EXTERNAL_AUTH_ID']) || $arFields['EXTERNAL_AUTH_ID'] == '')
			return false;

		$dbUsers = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID']));
		if($arUser = $dbUsers->Fetch())
		{
			$USER_ID = $arUser["ID"];
		}
		else
		{
			$arFields['PASSWORD'] = randString(30); //not necessary but...
			$arFields['LID'] = SITE_ID;

			$def_group = COption::GetOptionString('main', 'new_user_registration_def_group', '');
			if($def_group <> '')
				$arFields['GROUP_ID'] = explode(',', $def_group);

			if(!($USER_ID = $GLOBALS["USER"]->Add($arFields)))
				return false;

			$this->is_new_user = true;
		}
		$GLOBALS["USER"]->Authorize($USER_ID);
		return true;
	}
}

//some repetitive functionality
class TrCSocServUtil
{
	public static function GetCurUrl($addParam="", $removeParam=false)
	{
		$arRemove = array("logout", "auth_service_error", "auth_service_id");
		if($removeParam !== false)
			$arRemove = array_merge($arRemove, $removeParam);

		return self::ServerName().$GLOBALS['APPLICATION']->GetCurPageParam($addParam, $arRemove);
	}
	
	public static function ServerName()
	{
		$protocol = (CMain::IsHTTPS() ? "https" : "http");
		$port = ($_SERVER['SERVER_PORT'] > 0 && $_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443? ':'.$_SERVER['SERVER_PORT']:'');

		return $protocol.'://'.$_SERVER['SERVER_NAME'].$port;
	}
}
?>
