<?
IncludeModuleLangFile(__FILE__);

class TrCSocServGoogleOAuth extends TrCSocServAuth
{
	const ID = "GoogleOAuth";

	public function GetSettings()
	{
		return array(
			array("google_appid", GetMessage("socserv_google_client_id"), "", Array("text", 40)),
			array("google_appsecret", GetMessage("socserv_google_client_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_google_note", array('#URL#'=>TrCSocServUtil::ServerName()."/bitrix/tools/oauth/google.php"))),
		);
	}

	public function GetFormHtml($arParams)
	{
		$appID = self::GetOption("google_appid");
		$appSecret = self::GetOption("google_appsecret");

		$gAuth = new TrCGoogleOAuthInterface($appID, $appSecret);

		$redirect_uri = TrCSocServUtil::ServerName()."/bitrix/tools/oauth/google.php";
		$state = 'site_id='.SITE_ID.'&backurl='.urlencode($GLOBALS["APPLICATION"]->GetCurPageParam('', array("logout", "auth_service_error", "auth_service_id")));

		$url = $gAuth->GetAuthUrl($redirect_uri, $state);
		if($arParams['NO_TEXT'] == 'Y'){
			return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialchars(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button google-button"></a>';
		}else{
			return '<a href="javascript:void(0)" onclick="BX.util.popup(\''.htmlspecialchars(CUtil::JSEscape($url)).'\', 580, 400)" class="bx-ss-button google-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_google_form_note").'</span>';
		}
	}
	
	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;
		if(isset($_REQUEST["code"]) && $_REQUEST["code"] <> '')
		{
			$redirect_uri = TrCSocServUtil::ServerName()."/bitrix/tools/oauth/google.php";
			$appID = self::GetOption("google_appid");
			$appSecret = self::GetOption("google_appsecret");

			$gAuth = new TrCGoogleOAuthInterface($appID, $appSecret, $_REQUEST["code"]);

			if($gAuth->GetAccessToken($redirect_uri) !== false)
			{
				$arGoogleUser = $gAuth->GetCurrentUser();

				if($arGoogleUser['feed']['author']['0']['email']['$t'] <> '')
				{
					$first_name = $last_name = "";
					if($arGoogleUser['feed']['author']['0']['name']['$t'] <> '')
					{
						$aName = explode(" ", $arGoogleUser['feed']['author']['0']['name']['$t']);
						$first_name = $aName[0];
						if(isset($aName[1]))
							$last_name = $aName[1];
					}
					$email = $arGoogleUser['feed']['author']['0']['email']['$t'];
	
					$arFields = array(
						'EXTERNAL_AUTH_ID' => self::ID,
						'XML_ID' => $email,
						'LOGIN' => $email,
						'EMAIL' => $email,
						'NAME'=> $first_name,
						'LAST_NAME'=> $last_name,
					);
					$bSuccess = $this->AuthorizeUser($arFields);
				}
			}
		}

		$url = '/auth/';
		if(isset($_REQUEST["state"]))
		{
			$arState = array();
			parse_str($_REQUEST["state"], $arState);
		
			if(isset($arState['backurl']))
				$url = $arState['backurl'];
		}
		if(!$bSuccess)
			$url .= (strpos($url, '?') === false? '?':'&').'auth_service_id='.self::ID.'&auth_service_error=1';
	
		echo '
<script type="text/javascript">
if(window.opener)
	window.opener.location = \''.CUtil::JSEscape($url).'\';
window.close();
</script>
';
		die();
	}
}

class TrCGoogleOAuthInterface
{
	const AUTH_URL = "https://accounts.google.com/o/oauth2/auth";
	const TOKEN_URL = "https://accounts.google.com/o/oauth2/token";
	const CONTACTS_URL = "https://www.google.com/m8/feeds/";

	protected $appID;
	protected $appSecret;
	protected $code = false;
	protected $access_token = false;
	
	public function __construct($appID, $appSecret, $code=false)
	{
		$this->appID = $appID;
		$this->appSecret = $appSecret;
		$this->code = $code;
	}

	public function GetAuthUrl($redirect_uri, $state='')
	{
		return self::AUTH_URL.
			"?client_id=".urlencode($this->appID).
			"&redirect_uri=".urlencode($redirect_uri).
			"&scope=".urlencode(self::CONTACTS_URL).
			"&response_type=code".
			($state <> ''? '&state='.urlencode($state):'');
	}
	
	public function GetAccessToken($redirect_uri)
	{
		if($this->code === false)
			return false;

		$result = CHTTP::sPost(self::TOKEN_URL, array(
			"code"=>$this->code,
			"client_id"=>$this->appID,
			"client_secret"=>$this->appSecret,
			"redirect_uri"=>$redirect_uri,
			"grant_type"=>"authorization_code",
		));

		$arResult = CUtil::JsObjectToPhp($result);

		if(isset($arResult["access_token"]) && $arResult["access_token"] <> '')
		{
			$this->access_token = $arResult["access_token"];
			return true;
		}
		return false;
	}
	
	public function GetCurrentUser()
	{
		if($this->access_token === false)
			return false;

		$result = CHTTP::sGet(self::CONTACTS_URL.'contacts/default/full?v=3.0&alt=json&oauth_token='.urlencode($this->access_token));

		return CUtil::JsObjectToPhp($result);
	}
}
?>
