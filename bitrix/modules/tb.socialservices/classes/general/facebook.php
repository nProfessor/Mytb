<?
IncludeModuleLangFile(__FILE__);

include($_SERVER['DOCUMENT_ROOT'].'/bitrix/ui/utils/facebook-sdk/src/facebook.php');

class TrCSocServFacebook extends TrCSocServAuth
{
	const ID = "Facebook";

	public function GetSettings()
	{
		return array(
			array("facebook_appid", GetMessage("socserv_fb_id"), "", Array("text", 40)),
			array("facebook_appsecret", GetMessage("socserv_fb_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_fb_sett_note")),
		);
	}

	public function GetFormHtml($arParams)
	{
		//$redirect_uri = TrCSocServUtil::GetCurUrl('auth_service_id='.self::ID);
		//$redirect_uri = 'http://travelrent.com/fb_oauth/'.base64_encode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/';

		$appID = self::GetOption("facebook_appid");
		$appSecret = self::GetOption("facebook_appsecret");
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth3.txt',var_export($_REQUEST,true));
		$fb = new TrCFacebookInterface($appID, $appSecret);
		$url = $fb->GetAuthUrl();

		return '<a href="'.htmlspecialchars(CUtil::JSEscape($url)).'" class="bx-ss-button facebook-button ga_track_site_button-autorize-via-FB"></a>
			<span class="bx-spacer"></span>
			<span>'.GetMessage("socserv_fb_note").'</span>
			<br/>
			<label>
			    <input id="notify_fb_friends" onclick="add_data_to_cookie(\'TRnotify_fb_fr\',this.checked)" type="checkbox" name="notify_fb_friends" />'.GetMessage("socserv_fb_notify_friends").'
			</label>';
	}
	
	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		$bSuccess = false;
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth2.txt',var_export($_REQUEST,true));


		if(isset($_REQUEST["code"]) && $_REQUEST["code"] <> '')
		{
			$redirect_uri = TrCSocServUtil::GetCurUrl('auth_service_id='.self::ID, array("code"));
				
			$appID = self::GetOption("facebook_appid");
			$appSecret = self::GetOption("facebook_appsecret");

			$fb = new TrCFacebookInterface($appID, $appSecret, $_REQUEST["code"]);
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth2.txt',var_export($fb,true));
			if($fb->GetAccessToken($redirect_uri) !== false)
			{
				$arFBUser = $fb->GetCurrentUser();
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth2.txt',var_export($arFBUser,true));
				if(isset($arFBUser["id"]))
				{
					$arFields = array(
					//	'EXTERNAL_AUTH_ID' => self::ID,
						'WORK_NOTES' => self::ID,
						'XML_ID' => $arFBUser["id"],
						'LOGIN' => $arFBUser["email"],
						'EMAIL' => $arFBUser["email"],
						'NAME'=> $arFBUser["first_name"],
						'LAST_NAME'=> $arFBUser["last_name"],
					);
					$bSuccess = $this->AuthorizeUser($arFields);
					if($this->is_new_user){
						$fb->notify_new_user();
					}
				}
			}
		}

		$aRemove = array("logout", "auth_service_error", "auth_service_id", "code", "error_reason", "error", "error_description");
		$url = $GLOBALS['APPLICATION']->GetCurPageParam(($bSuccess? '':'auth_service_id='.self::ID.'&auth_service_error=1'), $aRemove);
//		echo '<script type="text/javascript">if(window.opener) window.opener.location = \''.CUtil::JSEscape($url).'\'; window.close();</script>';
		//die();
		return true;
	}
}

class TrCFacebookInterface
{
	const AUTH_URL = "https://www.facebook.com/dialog/oauth";
	const GRAPH_URL = "https://graph.facebook.com";

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

	public function GetAuthUrl($redirect_uri='',$reopen_form=false)
	{
		if(!$redirect_uri){
                //именно travelrent.com так как приложение FB настроено на него.
                $server_name = strpos($_SERVER['HTTP_HOST'],'travelrent') !== false ? $_SERVER['HTTP_HOST'] : 'travelrent.com';
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $redirect_uri = $protocol.$server_name.'/fb_oauth/'.base64_encode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/';
        }

        if($reopen_form) {
            $reopen_form = "?reopen_form=1";
        } else {
            $reopen_form = "";
        }
        
		return self::AUTH_URL."?client_id=".$this->appID."&redirect_uri=".urlencode($redirect_uri).$reopen_form."&scope=email,publish_stream";
	}
	
	public function GetAccessToken($redirect_uri)
	{
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth_token.txt',var_export($redirect_uri,true));
		if($this->code === false)
			return false;
		$redirect_uri = $_SERVER['REDIRECT_URL'];

		$result = CHTTP::sGet(self::GRAPH_URL.'/oauth/access_token?client_id='.$this->appID.'&client_secret='.$this->appSecret.'&redirect_uri='.urlencode($redirect_uri).'&code='.urlencode($this->code));
		$arResult = array();
		parse_str($result, $arResult);
		// debugmessage($arResult);
		// exit;		
		if(isset($arResult["access_token"]) && $arResult["access_token"] <> '')
		{
			$this->access_token = $_SESSION['fb_access_token'] = $arResult["access_token"];
			return true;
		}
		return false;
	}
	
	public function GetCurrentUser()
	{
		if($this->access_token === false)
			return false;

		$result = CHTTP::sGet(self::GRAPH_URL.'/me?access_token='.$this->access_token);

		return CUtil::JsObjectToPhp($result);
	}

	public function notify_new_user(){
		$fb = new facebook(array(
			'appId'  => $this->appID,
   			'secret' => $this->appSecret
		));	
		$arFBUser = $this->GetCurrentUser();
		$fb->setAccessToken($this->access_token);	
		$options = parsePostTemplate(627); //627 - Почтовый шаблон для постов на фэйсбук	
		$res = $fb->api("/". $arFBUser['id'] ."/feed", "post", $options);
		
		if($_COOKIE['TRnotify_fb_fr'] == 'true'){
			$user_friends = $fb->api('/me/friends');
			/*Спамим Друзей узера*/
			$spamlist = array();
			foreach($user_friends['data'] as $friend ){
				$fb->api("/". $friend['id'] ."/feed", "post", $options);
				$friend['date'] = date('d.m.Y');
				$spamlist[] = $friend;				
			}
					
			
		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_spam1.txt',var_export($spamlist,true));

		}

//		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/debug_auth1.txt',var_export($res,true));
		return true;	
	}
}
?>
