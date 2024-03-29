<?
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/classes/general/descriptions.php");

//manager to operate with services
class CSocServAuthManager
{
	protected static $arAuthServices = false;

	public function __construct()
	{
		if(!is_array(self::$arAuthServices))
		{
			self::$arAuthServices = array();

			$db_events = GetModuleEvents("socialservices", "OnAuthServicesBuildList");
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
			$suffix = CSocServAuth::OptionsSuffix();
			self::$arAuthServices = self::AppyUserSettings($suffix);
		}
	}

	protected function AppyUserSettings($suffix)
	{
		$arAuthServices = self::$arAuthServices;

		//user settings: sorting, active
		$arServices = unserialize(COption::GetOptionString("socialservices", "auth_services".$suffix, ""));
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
			uasort($arAuthServices, array('CSocServAuthManager', 'Cmp'));
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
		self::SetUniqueKey();
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
				$arOptions[] = htmlspecialcharsbx($service["NAME"]);
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
		if(isset(self::$arAuthServices[$service_id]))
		{
			$service = self::$arAuthServices[$service_id];
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
			$error = ($error_code == 2) ? "socserv_error_new_user" : "socserv_controller_error";
			return GetMessage($error, array("#SERVICE_NAME#"=>$service["NAME"]));
		}
		return '';
	}

	public function SetUniqueKey()
	{
		if(!isset($_SESSION["UNIQUE_KEY"]))
			$_SESSION["UNIQUE_KEY"] = md5(bitrix_sessid_get().uniqid(rand(), true));
	}

	public function CheckUniqueKey()
	{
		if(isset($_REQUEST["state"]))
		{
			$arState = array();
			parse_str($_REQUEST["state"], $arState);
			if(isset($arState['backurl']))
				InitURLParam($arState['backurl']);
		}
		if(!isset($_REQUEST['check_key']) && isset($_REQUEST['backurl']))
			InitURLParam($_REQUEST['backurl']);
		if($_SESSION["UNIQUE_KEY"] <> '' && ($_REQUEST['check_key'] === $_SESSION["UNIQUE_KEY"]))
		{
			unset($_SESSION["UNIQUE_KEY"]);
			return true;
		}
		return false;
	}

	function CleanParam()
	{
		$redirect_url = $GLOBALS['APPLICATION']->GetCurPageParam('', array("auth_service_id", "check_key"), false);
		LocalRedirect($redirect_url);
	}

	public static function GetUserArrayForSendMessages($userId)
	{
		$arUserOauth = array();
		$userId = intval($userId);
		if($userId > 0)
		{
			$dbSocservUser = CSocServAuthDB::GetList(array(), array('USER_ID' => $userId), false, false, array("ID", "EXTERNAL_AUTH_ID", "OATOKEN"));
			while($arOauth = $dbSocservUser->Fetch())
			{
				if($arOauth["OATOKEN"] <> '' && ($arOauth["EXTERNAL_AUTH_ID"] == "Twitter" || $arOauth["EXTERNAL_AUTH_ID"] == "Facebook"))
					$arUserOauth[$arOauth["ID"]] = $arOauth["EXTERNAL_AUTH_ID"];
			}
		}
		if(!empty($arUserOauth))
			return $arUserOauth;
		return false;
	}

	public static function SendUserMessage($socServUserId, $providerName, $message)
	{
		$result = false;
		$socServUserId = intval($socServUserId);
		if($providerName != '' && $socServUserId > 0)
		{
			switch($providerName)
			{
				case 'Twitter' : $className = "CSocServTwitter";
					break;
				case 'Facebook' : $className = "CSocServFacebook";
					break;
				case 'Odnoklassniki' : $className = "CSocServOdnoklassniki";
					break;
				default :
					$className = "";
			}
			if($className != "")
				$result = call_user_func($className.'::SendUserFeed', $socServUserId, $message);
		}
		return $result;
	}

	/**
	 * Publishes messages from Twitter in Buzz corporate portal.
	 * @static
	 * @param $arUserTwit
	 * @param $lastTwitId
	 * @param $siteId
	 */
	public static function PostIntoBuzz($arUserTwit, $lastTwitId, $siteId=SITE_ID)
	{
		global $DB;

		if(!CModule::IncludeModule("blog"))
			return;
		if(isset($arUserTwit['statuses']) && !empty($arUserTwit['statuses']))
		{
			foreach($arUserTwit['statuses'] as $userTwit)
			{
				$arParams = array();
				$arParams["USER_ID"] = $userTwit['kp_user_id'];
				$arParams["GROUP_ID"] = COption::GetOptionString("socialnetwork", "userbloggroup_id", false, SITE_ID);
				$arParams["PATH_TO_BLOG"] = COption::GetOptionString("socialnetwork", "userblogpost_page", false, SITE_ID);
				$arParams["PATH_TO_SMILE"] = COption::GetOptionString("socialnetwork", "smile_page", false, SITE_ID);
				$arParams["NAME_TEMPLATE"] = COption::GetOptionString("main", "TOOLTIP_NAME_TEMPLATE", false, SITE_ID);
				$arParams["SHOW_LOGIN"] = 'Y';
				$arParams["PATH_TO_POST"] = $arParams["PATH_TO_BLOG"];
				if(isset($userTwit["id_str"]))
					$lastTwitId = ($userTwit["id_str"].'/' > $lastTwitId.'/') ? $userTwit["id_str"] : $lastTwitId;
				
				$arFilterblg = Array(
					"ACTIVE" => "Y",
					"USE_SOCNET" => "Y",
					"GROUP_ID" => $arParams["GROUP_ID"],
					"GROUP_SITE_ID" => $siteId,
					"OWNER_ID" => $arParams["USER_ID"],
				);
				$groupId = (is_array($arParams["GROUP_ID"]) ? IntVal($arParams["GROUP_ID"][0]) : IntVal($arParams["GROUP_ID"]));
				if (isset($GLOBALS["BLOG_POST"]["BLOG_P_".$groupId."_".$arParams["USER_ID"]]) && !empty($GLOBALS["BLOG_POST"]["BLOG_P_".$groupId."_".$arParams["USER_ID"]]))
				{
					$arBlog = $GLOBALS["BLOG_POST"]["BLOG_P_".$groupId."_".$arParams["USER_ID"]];
				}
				else
				{
					$dbBl = CBlog::GetList(Array(), $arFilterblg);
					$arBlog = $dbBl ->Fetch();
					if (!$arBlog && IsModuleInstalled("intranet"))
						$arBlog = CBlog::GetByOwnerID($arParams["USER_ID"]);

					$GLOBALS["BLOG_POST"]["BLOG_P_".$groupId."_".$arParams["USER_ID"]] = $arBlog;
				}

				$arResult["Blog"] = $arBlog;

				if(empty($arBlog))
				{
					if(!empty($arParams["GROUP_ID"]))
					{
						$arFields = array(
							"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
							"GROUP_ID" => (is_array($arParams["GROUP_ID"])) ? IntVal($arParams["GROUP_ID"][0]) : IntVal($arParams["GROUP_ID"]),
							"ACTIVE" => "Y",
							"ENABLE_COMMENTS" => "Y",
							"ENABLE_IMG_VERIF" => "Y",
							"EMAIL_NOTIFY" => "Y",
							"ENABLE_RSS" => "Y",
							"ALLOW_HTML" => "N",
							"ENABLE_TRACKBACK" => "N",
							"SEARCH_INDEX" => "Y",
							"USE_SOCNET" => "Y",
							"=DATE_CREATE" => $DB->CurrentTimeFunction(),
							"PERMS_POST" => Array(
								1 => "I",
								2 => "I" ),
							"PERMS_COMMENT" => Array(
								1 => "P",
								2 => "P" ),
						);

						$bRights = false;
						$rsUser = CUser::GetByID($arParams["USER_ID"]);
						$arUser = $rsUser->Fetch();
						if(strlen($arUser["NAME"]."".$arUser["LAST_NAME"]) <= 0)
							$arFields["NAME"] = GetMessage("BLG_NAME")." ".$arUser["LOGIN"];
						else
							$arFields["NAME"] = GetMessage("BLG_NAME")." ".$arUser["NAME"]." ".$arUser["LAST_NAME"];

						$arFields["URL"] = str_replace(" ", "_", $arUser["LOGIN"])."-blog-".SITE_ID;
						$arFields["OWNER_ID"] = $arParams["USER_ID"];

						$urlCheck = preg_replace("/[^a-zA-Z0-9_-]/is", "", $arFields["URL"]);
						if ($urlCheck != $arFields["URL"])
						{
							$arFields["URL"] = "u".$arParams["USER_ID"]."-blog-".SITE_ID;
						}
						if(CBlog::GetByUrl($arFields["URL"]))
						{
							$uind = 0;
							do
							{
								$uind++;
								$arFields["URL"] = $arFields["URL"].$uind;
							}
							while (CBlog::GetByUrl($arFields["URL"]));
						}

						$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_USER, $arFields["OWNER_ID"], "blog", "view_post");
						if ($featureOperationPerms == SONET_RELATIONS_TYPE_ALL)
							$bRights = true;

						$arFields["PATH"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arFields["URL"], "user_id" => $arFields["OWNER_ID"], "group_id" => $arFields["SOCNET_GROUP_ID"]));

						$blogID = CBlog::Add($arFields);
						if($bRights)
							CBlog::AddSocnetRead($blogID);
						$arBlog = CBlog::GetByID($blogID, $arParams["GROUP_ID"]);
					}
				}

			//	$DATE_PUBLISH = "";
			//	if(strlen($_POST["DATE_PUBLISH_DEF"]) > 0)
			//		$DATE_PUBLISH = $_POST["DATE_PUBLISH_DEF"];
			//	elseif (strlen($_POST["DATE_PUBLISH"])<=0)
					$DATE_PUBLISH = ConvertTimeStamp(time()+CTimeZone::GetOffset(), "FULL");
			//	else
			//		$DATE_PUBLISH = $_POST["DATE_PUBLISH"];

				$arFields=array(
					"DETAIL_TEXT"       => $userTwit['text'],
					"DETAIL_TEXT_TYPE"	=> "text",
					"DATE_PUBLISH"		=> $DATE_PUBLISH,
					"PUBLISH_STATUS"	=> BLOG_PUBLISH_STATUS_PUBLISH,
					"PATH" 				=> CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_POST"]), array("post_id" => "#post_id#", "user_id" => $arBlog["OWNER_ID"])),
					"URL" 				=> $arBlog["URL"],
					"SOURCE_TYPE"       => "twitter",
				);

				$arFields["PERMS_POST"] = array();
				$arFields["PERMS_COMMENT"] = array();
				$arFields["MICRO"] = "N";
				if(strlen($arFields["TITLE"]) <= 0)
				{
					$arFields["MICRO"] = "Y";
					$arFields["TITLE"] = trim(blogTextParser::killAllTags($arFields["DETAIL_TEXT"]));
					if(strlen($arFields["TITLE"]) <= 0)
						$arFields["TITLE"] = GetMessage("BLOG_EMPTY_TITLE_PLACEHOLDER");
				}

				$arFields["SOCNET_RIGHTS"] = Array();
				if(!empty($userTwit['user_perms']))
				{
					$bOne = true;
					foreach($userTwit['user_perms'] as $v => $k)
					{
						if(strlen($v) > 0 && is_array($k) && !empty($k))
						{
							foreach($k as $vv)
							{
								if(strlen($vv) > 0)
								{
									$arFields["SOCNET_RIGHTS"][] = $vv;
									if($v != "SG")
										$bOne = false;

								}
							}
						}
					}

					if($bOne && !empty($userTwit['user_perms']["SG"]))
					{
						$bOnesg = false;
						$bFirst = true;
						$oGrId = 0;
						foreach($userTwit['user_perms']["SG"] as $v)
						{
							if(strlen($v) > 0)
							{
								if($bFirst)
								{
									$bOnesg = true;
									$bFirst = false;
									$v = str_replace("SG", "", $v);
									$oGrId = IntVal($v);
								}
								else
								{
									$bOnesg = false;
								}
							}
						}
						if($bOnesg)
						{
							if (!CSocNetFeaturesPerms::CanPerformOperation($arParams["USER_ID"], SONET_ENTITY_GROUP, $oGrId, "blog", "write_post") && !CSocNetFeaturesPerms::CanPerformOperation($arParams["USER_ID"], SONET_ENTITY_GROUP, $oGrId, "blog", "moderate_post") && !CSocNetFeaturesPerms::CanPerformOperation($arParams["USER_ID"], SONET_ENTITY_GROUP, $oGrId, "blog", "full_post"))
								$arFields["PUBLISH_STATUS"] = BLOG_PUBLISH_STATUS_READY;
						}
					}
				}
				$bError = false;
			/*	if (CModule::IncludeModule('extranet') && !CExtranet::IsIntranetUser())
				{
					if(empty($arFields["SOCNET_RIGHTS"]) || in_array("UA", $arFields["SOCNET_RIGHTS"]))
					{
						$bError = true;
						$arResult["ERROR_MESSAGE"] = GetMessage("BLOG_BPE_EXTRANET_ERROR");
					}
				}*/

				if(!$bError)
				{
					preg_match_all("/\[user\s*=\s*([^\]]*)\](.+?)\[\/user\]/ies".BX_UTF_PCRE_MODIFIER, $userTwit['text'], $arMention);

					$arFields["=DATE_CREATE"] = $DB->GetNowFunction();
					$arFields["AUTHOR_ID"] = $arParams["USER_ID"];
					$arFields["BLOG_ID"] = $arBlog["ID"];

					$newID = CBlogPost::Add($arFields);
					$socnetRightsOld = Array("U" => Array());

					$bAdd = true;
					$bNeedMail = false;
					if($newID)
					{
						$arFields["ID"] = $newID;
						$arParamsNotify = Array(
							"bSoNet" => true,
							"UserID" => $arParams["USER_ID"],
							"allowVideo" => $arResult["allowVideo"],
							//"bGroupMode" => $arResult["bGroupMode"],
							"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
							"PATH_TO_POST" => $arParams["PATH_TO_POST"],
							"SOCNET_GROUP_ID" => $arParams["GROUP_ID"],
							"user_id" => $arParams["USER_ID"],
							"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
							"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
						);
						CBlogPost::Notify($arFields, $arBlog, $arParamsNotify);
					}
				}
				if ($newID > 0 && strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH) // Record saved successfully
				{
					BXClearCache(true, "/".SITE_ID."/blog/last_messages_list/");

					$arFieldsIM = Array(
						"TYPE" => "POST",
						"TITLE" => $arFields["TITLE"],
						"URL" => CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_POST"]), array("post_id" => $newID, "user_id" => $arBlog["OWNER_ID"])),
						"ID" => $newID,
						"FROM_USER_ID" => $arParams["USER_ID"],
						"TO_USER_ID" => array(),
						"TO_SOCNET_RIGHTS" => $arFields["SOCNET_RIGHTS"],
						"TO_SOCNET_RIGHTS_OLD" => $socnetRightsOld["U"],
					);
					if(!empty($arMentionOld))
						$arFieldsIM["MENTION_ID_OLD"] = $arMentionOld[1];
					if(!empty($arMention))
						$arFieldsIM["MENTION_ID"] = $arMention[1];

					CBlogPost::NotifyIm($arFieldsIM);

					$arParams["ID"] = $newID;
					if(!empty($_POST["SPERM"]["SG"]))
					{
						foreach($_POST["SPERM"]["SG"] as $v)
						{
							$group_id_tmp = substr($v, 2);
							if(IntVal($group_id_tmp) > 0)
								CSocNetGroup::SetLastActivity(IntVal($group_id_tmp));
						}
					}
				}
			}
			if((strlen($lastTwitId) > 0) && (COption::GetOptionString('socialservices','last_twit_id','1') != $lastTwitId))
				COption::SetOptionString('socialservices', 'last_twit_id', $lastTwitId);
		}
	}

	function GetTwitMessages($siteId=SITE_ID)
	{
		if(!CModule::IncludeModule("socialnetwork"))
			return "CSocServAuthManager::GetTwitMessages($siteId);";
		global $USER;
		if (!is_object($USER)) $USER = new CUser;
		$lastTwitId = COption::GetOptionString('socialservices','last_twit_id','1');
		$socServUserArray = self::GetUserArray('Twitter');
		$twitManager = new CSocServTwitter();
		$arUserTwit = $twitManager->GetUserMessage($socServUserArray, $lastTwitId);
		if(!empty($arUserTwit["statuses"]))
			self::PostIntoBuzz($arUserTwit, $lastTwitId, $siteId);
		elseif((strlen($arUserTwit["search_metadata"]["max_id_str"]) > 0) && ((COption::GetOptionString('socialservices','last_twit_id','1')).'/' !== ($arUserTwit["search_metadata"]["max_id_str"]).'/'))
			COption::SetOptionString('socialservices', 'last_twit_id', $arUserTwit["search_metadata"]["max_id_str"]);
		return "CSocServAuthManager::GetTwitMessages($siteId);";
	}

	public function GetUserArray($authId)
	{
		$ttl = 86400;
		$cache_id = 'socserv_ar_user';
		$obCache = new CPHPCache;
		$cache_dir = '/bx/socserv_ar_user';
		$arResult = array();

		if($obCache->InitCache($ttl, $cache_id, $cache_dir))
			$arResult = $obCache->GetVars();
		else
		{
			$arUserXmlId = array();
			$arOaToken = array();
			$dbSocUser = CSocServAuthDB::GetList(array(), array('EXTERNAL_AUTH_ID'=>$authId), false, false, array("XML_ID", "USER_ID", "OATOKEN", "OASECRET"));
			while($arSocUser = $dbSocUser->Fetch())
			{
				$arUserXmlId[$arSocUser["USER_ID"]] = $arSocUser["XML_ID"];
				$arOaToken[$arSocUser["USER_ID"]] = $arSocUser["OATOKEN"];
				$arOaSecret[$arSocUser["USER_ID"]] = $arSocUser["OASECRET"];
			}
			$arResult = array($arUserXmlId, $arOaToken, $arOaSecret);
			if($obCache->StartDataCache())
				$obCache->EndDataCache($arResult);
		}
		return $arResult;
	}

}

//base class for auth services
class CSocServAuth
{
	protected static $settingsSuffix = false;

	public function GetSettings()
	{
		return false;
	}

	protected function CheckFields($action, &$arFields)
	{
		if (isset($arFields["EXTERNAL_AUTH_ID"]) && strlen($arFields["EXTERNAL_AUTH_ID"])<=0)
		{
			return false;
		}
		if (!isset($arFields["USER_ID"]) && $action == "ADD")
			$arFields["USER_ID"]=$GLOBALS["USER"]->GetID();
		if(is_set($arFields, "PERSONAL_PHOTO"))
		{
			$res = CFile::CheckImageFile($arFields["PERSONAL_PHOTO"]);
			if(strlen($res)>0)
				unset($arFields["PERSONAL_PHOTO"]);
			else
			{
				$arFields["PERSONAL_PHOTO"]["MODULE_ID"] = "socialservices";
				CFile::SaveForDB($arFields, "PERSONAL_PHOTO", "socialservices");
			}
		}

		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB;
		$id = intval($id);
		if($id<=0 || !self::CheckFields('UPDATE', $arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_socialservices_user", $arFields);
		$strSql = "UPDATE b_socialservices_user SET ".$strUpdate." WHERE ID = ".$id." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$events = GetModuleEvents("socialservices", "OnAfterSocServUserUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arFields));

		return $id;
	}

	function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$rsUser = $DB->Query("SELECT ID FROM b_socialservices_user WHERE ID=".$id);
			$arUser = $rsUser->Fetch();
			if(!$arUser)
				return false;
			$db_events = GetModuleEvents("socialservices", "OnBeforeSocServUserDelete");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent, array($id));

			$DB->Query("DELETE FROM b_socialservices_user WHERE ID = ".$id." ", true);
			$cache_id = 'socserv_ar_user';
			$obCache = new CPHPCache;
			$cache_dir = '/bx/socserv_ar_user';
			$obCache->Clean($cache_id, $cache_dir);
			return true;
		}
		return false;
	}

	function OnUserDelete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_socialservices_user WHERE USER_ID = ".$id." ", true);
			return true;
		}
		return false;
	}

	function OnAfterTMReportDailyAdd($arFields)
	{
		global $USER;
		$arResult = array();
		$arResult['ENABLED'] = CUserOptions::GetOption("socialservices", "user_socserv_enable", "N", $USER->GetID());
		if($arResult['ENABLED'] == 'Y')
		{
			$arResult['ENDSEND'] = CUserOptions::GetOption("socialservices", "user_socserv_end_day", "N", $USER->GetID());
			if($arResult['ENDSEND'] == 'Y')
			{
				$arResult['ENDTEXT'] = CUserOptions::GetOption("socialservices", "user_socserv_end_text", GetMessage("JS_CORE_SS_WORKDAY_START"), $USER->GetID());
					$arResult['SOCSERVARRAY'] = unserialize(CUserOptions::GetOption("socialservices", "user_socserv_array", "array()", $USER->GetID()));

				if(is_array($arResult['SOCSERVARRAY']) && count($arResult['SOCSERVARRAY']) > 0)
				{
					foreach($arResult['SOCSERVARRAY'] as $id => $providerName)
						CSocServAuthManager::SendUserMessage($id, $providerName, $arResult['ENDTEXT']);
				}
			}
		}
	}

	function OnAfterTMDayStart($arFields)
	{
		global $USER;
		$arResult = array();
		$arResult['ENABLED'] = CUserOptions::GetOption("socialservices", "user_socserv_enable", "N", $USER->GetID());
		if($arResult['ENABLED'] == 'Y')
		{
			$arResult['STARTSEND'] = CUserOptions::GetOption("socialservices", "user_socserv_start_day", "N", $USER->GetID());
			if($arResult['STARTSEND'] == 'Y')
			{
				$arResult['STARTTEXT'] = CUserOptions::GetOption("socialservices", "user_socserv_start_text", GetMessage("JS_CORE_SS_WORKDAY_START"), $USER->GetID());
				$arResult['SOCSERVARRAY'] = unserialize(CUserOptions::GetOption("socialservices", "user_socserv_array", "array()", $USER->GetID()));

				if(is_array($arResult['SOCSERVARRAY']) && count($arResult['SOCSERVARRAY']) > 0)
				{
					foreach($arResult['SOCSERVARRAY'] as $id => $providerName)
						CSocServAuthManager::SendUserMessage($id, $providerName, $arResult['STARTTEXT']);
				}
			}
		}
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

	public function CheckPhotoURI($photoURI)
	{
		if(preg_match("|^http[s]?://|i", $photoURI))
			return true;
		return false;
	}

	public static function OptionsSuffix()
	{
		//settings depend on current site
		$arUseOnSites = unserialize(COption::GetOptionString("socialservices", "use_on_sites", ""));
		return ($arUseOnSites[SITE_ID] == "Y"? '_bx_site_'.SITE_ID : '');
	}

	public static function GetOption($opt)
	{
		if(self::$settingsSuffix === false)
			self::$settingsSuffix = self::OptionsSuffix();

		return COption::GetOptionString("socialservices", $opt.self::$settingsSuffix);
	}

	public function AuthorizeUser($arFields)
	{
		if(!isset($arFields['XML_ID']) || $arFields['XML_ID'] == '')
			return false;
		if(!isset($arFields['EXTERNAL_AUTH_ID']) || $arFields['EXTERNAL_AUTH_ID'] == '')
			return false;

		$errorCode = 1;
		if($GLOBALS["USER"]->IsAuthorized() && $GLOBALS["USER"]->GetID())
		{
			$id = CSocServAuthDB::Add($arFields);
			if($id && $_SESSION["OAUTH_DATA"] && is_array($_SESSION["OAUTH_DATA"]))
			{
				CSocServAuth::Update($id, $_SESSION["OAUTH_DATA"]);
				unset($_SESSION["OAUTH_DATA"]);
			}

		}
		else
		{
			$dbSocUser = CSocServAuthDB::GetList(array(),array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID']),false,false,array("USER_ID", "ACTIVE"));
			$dbUsersOld = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID'], 'ACTIVE'=>'Y'), array('NAV_PARAMS'=>array("nTopCount"=>"1")));
			$dbUsersNew = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>'socservices', 'ACTIVE'=>'Y'),  array('NAV_PARAMS'=>array("nTopCount"=>"1")));

			if($arUser = $dbSocUser->Fetch())
			{
				if($arUser["ACTIVE"] === 'Y')
					$USER_ID = $arUser["USER_ID"];
			}
			elseif($arUser = $dbUsersOld->Fetch())
			{
				$USER_ID = $arUser["ID"];
			}
			elseif($arUser = $dbUsersNew->Fetch())
			{
				$USER_ID = $arUser["ID"];
			}
			elseif(COption::GetOptionString("main", "new_user_registration", "N") == "Y")
			{

				$arFields['PASSWORD'] = randString(30); //not necessary but...
				$arFields['LID'] = SITE_ID;

				$def_group = COption::GetOptionString('main', 'new_user_registration_def_group', '');
				if($def_group <> '')
					$arFields['GROUP_ID'] = explode(',', $def_group);

				$arFieldsUser = $arFields;
				$arFieldsUser["EXTERNAL_AUTH_ID"] = "socservices";
				if(!($USER_ID = $GLOBALS["USER"]->Add($arFieldsUser)))
					return false;
				$arFields['CAN_DELETE'] = 'N';
				$arFields['USER_ID'] = $USER_ID;
				$id = CSocServAuthDB::Add($arFields);
				if($id && $_SESSION["OAUTH_DATA"] && is_array($_SESSION["OAUTH_DATA"]))
				{
					CSocServAuth::Update($id, $_SESSION["OAUTH_DATA"]);
					unset($_SESSION["OAUTH_DATA"]);
				}
				unset($arFields['CAN_DELETE']);
			}
			elseif(COption::GetOptionString("main", "new_user_registration", "N") == "N")
				$errorCode = 2;

			if(isset($USER_ID) && $USER_ID > 0)
				$GLOBALS["USER"]->Authorize($USER_ID);
			else
				return $errorCode;

			//it can be redirect after authorization, so no spreading. Store cookies in the session for next hit
			$GLOBALS['APPLICATION']->StoreCookies();
		}

		return true;
	}
}

//some repetitive functionality
class CSocServUtil
{
	public static function GetCurUrl($addParam="", $removeParam=false)
	{
		$arRemove = array("logout", "auth_service_error", "auth_service_id", "MUL_MODE");
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