<?
IncludeModuleLangFile(__FILE__);

class TrCSocServMyMailRu extends TrCSocServAuth
{
	const ID = "MyMailRu";
	
	public function GetSettings()
	{
		return array(
			array("mailru_id", GetMessage("socserv_mailru_id"), "", Array("text", 40)),
			array("mailru_private_key", GetMessage("socserv_mailru_key"), "", Array("text", 40)),
			array("mailru_secret_key", GetMessage("socserv_mailru_secret"), "", Array("text", 40)),
			array("note"=>GetMessage("socserv_mailru_sett_note")." ".GetMessage("socserv_mailru_opt_note")),
		);
	}

	public function GetFormHtml($arParams,$onclickJs)
	{
		$mailru_id = self::GetOption("mailru_id");
		$mailru_private_key = self::GetOption("mailru_private_key");

		$aRemove = array("logout", "auth_service_error", "auth_service_id");
		$url_err = $GLOBALS['APPLICATION']->GetCurPageParam('auth_service_id='.self::ID.'&auth_service_error=1', $aRemove);
		$url_ok = $GLOBALS['APPLICATION']->GetCurPageParam('', $aRemove);

		$script = '
<script type="text/javascript" src="//cdn.connect.mail.ru/js/loader.js"></script>
<script type="text/javascript">
if(typeof(mailru) != "undefined"){
BX.ready(function(){mailru.loader.require("api", 
	function() 
	{
		mailru.connect.init(\''.CUtil::JSEscape($mailru_id).'\', \''.CUtil::JSEscape($mailru_private_key).'\');
		mailru.events.listen(mailru.connect.events.login, function(sess){mailru.common.users.getInfo(function(res){BxMailRuAuthInfo(sess, res);});});
	}
);});
}

function BxMailRuAuthInfo(sess, response) 
{
	var url_err = \''.CUtil::JSEscape($url_err).'\';
	if(sess && response && response[0]) 
	{
		var url_post = \''.CUtil::JSEscape($arParams["~AUTH_URL"]).'\';
		var url_ok = \''.CUtil::JSEscape($url_ok).'\';
		var data = {
			"auth_service_id": "'.self::ID.'",
			"mailru_user": response[0],
			"mailru_sess": sess
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
		CUtil::InitJSCore(array("ajax"));
		$GLOBALS['APPLICATION']->AddHeadString($script, true);
		if($onclickJs) {
			$s = 'mailru.connect.login();';
		} else {
			if($arParams['NO_TEXT'] == 'Y'){
				$s = '<a href="javascript:void(0)" onclick="mailru.connect.login();" class="bx-ss-button mymailru-button"></a>';	
			}else{
				$s = '<a href="javascript:void(0)" onclick="mailru.connect.login();" class="bx-ss-button mymailru-button"></a><span class="bx-spacer"></span><span>'.GetMessage("socserv_mailru_note").'</span>';
			}
			
		}
		return $s;
	}
	
	public function Authorize()
	{
		$GLOBALS["APPLICATION"]->RestartBuffer();
		
		if(isset($_REQUEST["mailru_sess"]["sig"]) && isset($_REQUEST["mailru_user"]["uid"]))
		{
			if(self::CheckUserData($_REQUEST["mailru_sess"]["sig"]))
			{
				CUtil::decodeURIComponent($_REQUEST);
				$arFields = array(
					'EXTERNAL_AUTH_ID' => self::ID,
					'XML_ID' => $_REQUEST["mailru_user"]["uid"],
					'LOGIN' => $_REQUEST["mailru_user"]["email"],
					'EMAIL' => $_REQUEST["mailru_user"]["email"],
					'NAME'=> $_REQUEST["mailru_user"]["first_name"],
					'LAST_NAME'=> $_REQUEST["mailru_user"]["last_name"],
				);

				if($this->AuthorizeUser($arFields))
					die("OK");
			}
		}
		die("FAILURE");
	}
	
	protected function CheckUserData($control_sign)
	{
		$APP_SECRET = self::GetOption("mailru_secret_key");

		$app_cookie = $_COOKIE['mrc'];
		if($app_cookie == '') 
			return false;

		$session = array();
		parse_str(urldecode($app_cookie), $session);

    	ksort($session);

		$sign = '';
		foreach($session as $key=>$value) 
			if($key <> 'sig') 
				$sign .= ($key.'='.$value);

		$sign .= $APP_SECRET;
		$sign = md5($sign);

		if($control_sign === $sign && $control_sign === $session['sig']) 
			return true;

  		return false;
	}
}
?>
