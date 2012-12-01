<?
$arClasses = array(
	"TrCSocServAuthManager" => "classes/general/authmanager.php",
	"TrCSocServUtil" => "classes/general/authmanager.php",
	"TrCSocServAuth" => "classes/general/authmanager.php",
	"TrCSocServFacebook" => "classes/general/facebook.php",
	"TrCFacebookInterface" => "classes/general/facebook.php",
	"TrCSocServLiveID" => "classes/general/liveid.php",
	"TrCSocServMyMailRu" => "classes/general/mailru.php",
	"TrCSocServOpenID" => "classes/general/openid.php",
	"TrCSocServYandex" => "classes/general/openid.php",
	"TrCSocServMailRu" => "classes/general/openid.php",
	"TrCSocServLivejournal" => "classes/general/openid.php",
	"TrCSocServLiveinternet" => "classes/general/openid.php",
	"TrCSocServBlogger" => "classes/general/openid.php",
	"TrCSocServRambler" => "classes/general/openid.php",
	"TrCSocServTwitter" => "classes/general/twitter.php",
	"TrCTwitterInterface" => "classes/general/twitter.php",
	"TrCSocServVKontakte" => "classes/general/vkontakte.php",
	"TrCSocServGoogleOAuth" => "classes/general/google.php",
	"TrCOpenIDClient" => "classes/general/openidclient.php",
);

CModule::AddAutoloadClasses("tr.socialservices", $arClasses);
?>