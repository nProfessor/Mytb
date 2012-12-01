<?
IncludeModuleLangFile(__FILE__);

class TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($var === false)
			$var = 'OPENID_IDENTITY_OPENID';

		$step = TrCOpenIDClient::GetOpenIDAuthStep($var);
		if($step > 0)
		{
			$obOpenID = new TrCOpenIDClient();
		
			if($step == 2)
			{
				return $obOpenID->Authorize();
			}
			elseif($step == 1)
			{
				if($identity === false)
					$identity = $_POST['OPENID_IDENTITY_OPENID'];
		
				$return_to = TrCSocServUtil::GetCurUrl("auth_service_id=".urlencode($_REQUEST["auth_service_id"]));

				if($url = $obOpenID->GetRedirectUrl($identity, $return_to))
					LocalRedirect($url, true);
				else
					return false;
			}
		}
		return false;
	}
	
	public function GetFormHtml($arParams)
	{
		return '
<span class="bx-ss-icon openid"></span>
<span>'.'OpenID:'.'</span>
<input type="text" name="OPENID_IDENTITY_OPENID" value="'.$arParams["LAST_LOGIN"].'" size="40" />
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServYandex extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = "http://openid.yandex.ru/".$_POST['OPENID_IDENTITY_YANDEX'];
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_YANDEX');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#openid.yandex.ru/([^/$]+)#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_YANDEX" value="'.htmlspecialchars($login).'" size="20" />
<span>@yandex.ru</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServMailRu extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = "http://openid.mail.ru/mail/".$_POST['OPENID_IDENTITY_MAILRU'];
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_MAILRU');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#openid.mail.ru/mail/([^/$]+)#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_MAILRU" value="'.htmlspecialchars($login).'" size="20" />
<span>@mail.ru</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServLivejournal extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = $_POST['OPENID_IDENTITY_LIVEJOURNAL'].".livejournal.com";
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_LIVEJOURNAL');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#([^\.]+).livejournal.com#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_LIVEJOURNAL" value="'.htmlspecialchars($login).'" size="20" />
<span>.livejournal.com</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServLiveinternet extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = "http://www.liveinternet.ru/users/".$_POST['OPENID_IDENTITY_LIVEINTERNET']."/";
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_LIVEINTERNET');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#www.liveinternet.ru/users/([^/$]+)#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<span>http://www.liveinternet.ru/users/</span>
<input type="text" name="OPENID_IDENTITY_LIVEINTERNET" value="'.htmlspecialchars($login).'" size="20" />
<span>/</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServBlogger extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = "http://".$_POST['OPENID_IDENTITY_BLOGGER'].".blogspot.com/";
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_BLOGGER');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#([^\.]+).blogspot.com#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_BLOGGER" value="'.htmlspecialchars($login).'" size="20" />
<span>.blogspot.com</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

class TrCSocServRambler extends TrCSocServOpenID
{
	public function Authorize($identity=false, $var=false)
	{
		if($identity === false)
			$identity = "http://id.rambler.ru/users/".$_POST['OPENID_IDENTITY_RAMBLER'];
			
		return parent::Authorize($identity, 'OPENID_IDENTITY_RAMBLER');
	}

	public function GetFormHtml($arParams)
	{
		$login = '';
		if(preg_match('#id.rambler.ru/users/([^/$]+)#i', $arParams["~LAST_LOGIN"], $matches))
			$login = $matches[1];
		return '
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_RAMBLER" value="'.htmlspecialchars($login).'" size="20" />
<span>@rambler.ru</span>
<input type="submit" class="button" name="" value="'.GetMessage("socserv_openid_login").'" />
';
	}
}

?>
