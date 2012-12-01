<?
IncludeModuleLangFile(__FILE__);

class TrCSocServDescription
{
	public function GetDescription()
	{
		$liveid_disabled = !WindowsLiveLogin::IsAvailable();
		$tw_disabled = !function_exists("hash_hmac");

		return array(
			array(
				"ID" => "Facebook",
				"CLASS" => "TrCSocServFacebook",
				"NAME" => "Facebook",
				"ICON" => "facebook",
			),
			array(
				"ID" => "LiveID",
				"CLASS" => "TrCSocServLiveID",
				"NAME" => "LiveID",
				"ICON" => "liveid",
				"DISABLED" => $liveid_disabled,
			),
			array(
				"ID" => "MyMailRu",
				"CLASS" => "TrCSocServMyMailRu",
				"NAME" => GetMessage("socserv_mailru_name"),
				"ICON" => "mymailru",
			),
			array(
				"ID" => "OpenID",
				"CLASS" => "TrCSocServOpenID",
				"NAME" => "OpenID",
				"ICON" => "openid",
			),
			array(
				"ID" => "YandexOpenID",
				"CLASS" => "TrCSocServYandex",
				"NAME" => GetMessage("socserv_openid_yandex"),
				"ICON" => "yandex",
			),
			array(
				"ID" => "MailRuOpenID",
				"CLASS" => "TrCSocServMailRu",
				"NAME" => "Mail.Ru",
				"ICON" => "openid-mail-ru",
			),
			array(
				"ID" => "Livejournal",
				"CLASS" => "TrCSocServLivejournal",
				"NAME" => "Livejournal",
				"ICON" => "livejournal",
			),
			array(
				"ID" => "Liveinternet",
				"CLASS" => "TrCSocServLiveinternet",
				"NAME" => "Liveinternet",
				"ICON" => "liveinternet",
			),
			array(
				"ID" => "Blogger",
				"CLASS" => "TrCSocServBlogger",
				"NAME" => "Blogger",
				"ICON" => "blogger",
			),
			array(
				"ID" => "Rambler",
				"CLASS" => "TrCSocServRambler",
				"NAME" => "Rambler",
				"ICON" => "rambler",
			),
			array(
				"ID" => "Twitter",
				"CLASS" => "TrCSocServTwitter",
				"NAME" => "Twitter",
				"ICON" => "twitter",
				"DISABLED" => $tw_disabled,
			),
			array(
				"ID" => "VKontakte",
				"CLASS" => "TrCSocServVKontakte",
				"NAME" => GetMessage("socserv_vk_name"),
				"ICON" => "vkontakte",
			),
			array(
				"ID" => "GoogleOAuth",
				"CLASS" => "TrCSocServGoogleOAuth",
				"NAME" => "Google",
				"ICON" => "google",
			),
		);
	}
}

AddEventHandler("tr.socialservices", "OnAuthServicesBuildList", array("TrCSocServDescription", "GetDescription"));
?>