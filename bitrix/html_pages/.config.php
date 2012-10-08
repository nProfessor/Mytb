<?
$arHTMLPagesOptions = array(
	"INCLUDE_MASK" => "*.php;*/",
	"EXCLUDE_MASK" => "/bitrix/*;/404.php;/",
	"FILE_QUOTA" => "100",
	"~INCLUDE_MASK" => array(
		"0" => "'^.*?\\.php\$'",
		"1" => "'^.*?/\$'",
	),
	"~EXCLUDE_MASK" => array(
		"0" => "'^/bitrix/.*?\$'",
		"1" => "'^/404\\.php\$'",
		"2" => "'^/\$'",
	),
	"~FILE_QUOTA" => "104857600",
	"COMPRESS" => "1",
	"STORE_PASSWORD" => "Y",
	"COOKIE_LOGIN" => "MYTB_SM_LOGIN",
	"COOKIE_PASS" => "MYTB_SM_UIDH",
);
?>
