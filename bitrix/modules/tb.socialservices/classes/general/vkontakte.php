<?
IncludeModuleLangFile(__FILE__);

class TrCSocServVKontakte extends TrCSocServAuth
{
	const ID = "VKontakte";
	
	public function GetSettings()
	{
		return array(
			array("vkontakte_appid", GetMessage("socserv_vk_id"), "", Array("text", 40)),
			array("vkontakte_appsecret", GetMessage("socserv_vk_key"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_vk_sett_note")),
		);
	}

	public function GetAuthUrl($reopen_form=false){
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$vk_auth_url = 'http://oauth.vk.com/authorize';
		$tr_auth_url = $protocol.'travelrent.com/vk_oauth/'.base64_encode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        
        if($reopen_form) {
            $reopen_form = "?reopen_form=1";
        } else {
            $reopen_form = "";
        }
        
		return $vk_auth_url.'?client_id='.CUtil::JSEscape(self::GetOption("vkontakte_appid")).'&scope=friends,photos,notify,groups,offline&redirect_uri='.$tr_auth_url."/".$reopen_form.'&response_type=code';
	}	

	public function GetFormHtml($arParams)
	{
		$aRemove = array("logout", "auth_service_error", "auth_service_id");
		$url_err = $GLOBALS['APPLICATION']->GetCurPageParam('auth_service_id='.self::ID.'&auth_service_error=1', $aRemove);
		$url_ok = $GLOBALS['APPLICATION']->GetCurPageParam('', $aRemove);

		$script = '
<script type="text/javascript" src="http://vkontakte.ru/js/api/openapi.js"></script>
<script type="text/javascript">
BX.ready(function(){VK.init({apiId: \''.CUtil::JSEscape(self::GetOption("vkontakte_appid")).'\'});});

function BxVKAuthInfo(response) 
{
	var url_err = \''.CUtil::JSEscape($url_err).'\';
	if(response.session) 
	{
		var url_post = \''.CUtil::JSEscape($arParams["~AUTH_URL"]).'\';
		var url_ok = \''.CUtil::JSEscape($url_ok).'\';
		var data = {
			"auth_service_id": "'.self::ID.'",
			"vk_session": response.session
		};
		BX.ajax.post(url_post, data, function(res){window.location = (res == "OK"? url_ok : url_err);});
	} 
	else 
	{
		window.location = url_err;
	}
}
</script>
';
//		CUtil::InitJSCore(array("ajax"));
//		$GLOBALS['APPLICATION']->AddHeadString($script, true);

		//$s = '<a href="javascript:void(0)" onclick="VK.Auth.login(BxVKAuthInfo);" class="bx-ss-button vkontakte-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_vk_note").'</span>';
		$s = '<a href="'.$this->GetAuthUrl().'" class="bx-ss-button vkontakte-button ga_track_site_button-autorize-via-VK"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_vk_note").'</span>';
		return $s;
	}
	
	public function Authorize()
	{	
	//	var_dump( $skip_check_user_data);
		$GLOBALS["APPLICATION"]->RestartBuffer();
		
		if(isset($_REQUEST["vk_session"]["user"]["id"]))
		{	
			global $skip_check_user_data;
			if(self::CheckUserData($_REQUEST["vk_session"]["sig"]) || $skip_check_user_data)
			{
				CUtil::decodeURIComponent($_REQUEST);

				$u_id = $_REQUEST["vk_session"]["user"]["id"];
				$arFields = array(
					'EXTERNAL_AUTH_ID' => self::ID,
					'WORK_NOTES' => self::ID, //это для того, чтоб можно было избавится от EXTERNAL_AUTH_ID и разрешить авторизовываться несколькими способами одновременно
					'XML_ID' => $u_id,
					'LOGIN' => "id".$u_id,
					'NAME'=> $_REQUEST["vk_session"]["user"]["first_name"],
					'LAST_NAME'=> $_REQUEST["vk_session"]["user"]["last_name"],
				);
				
				if($this->AuthorizeUser($arFields)){
					if($skip_check_user_data){
						return true;		
					}
					die("OK");
				}	
			}
		}
		die("FAILURE");
	}
	
	protected function CheckUserData($control_sign)
	{
		$APP_ID = self::GetOption("vkontakte_appid");
		$APP_SECRET = self::GetOption("vkontakte_appsecret");

		$app_cookie = $_COOKIE['vk_app_'.$APP_ID];
		if($app_cookie == '') 
			return false;

		$session = array();
		parse_str($app_cookie, $session);

		static $valid_keys = array('expire'=>1, 'mid'=>1, 'secret'=>1, 'sid'=>1, 'sig'=>1);
		foreach($valid_keys as $key=>$v) 
			if(!isset($session[$key])) 
				return false;
    	
    	ksort($session);

		$sign = '';
		foreach($session as $key=>$value) 
			if($key <> 'sig' && array_key_exists($key, $valid_keys)) 
				$sign .= ($key.'='.$value);

		$sign .= $APP_SECRET;
		$sign = md5($sign);

		if($control_sign === $sign && $control_sign === $session['sig'] && $session['expire'] > time()) 
			return true;

  		return false;
	}
}

?>
