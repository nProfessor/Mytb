<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/kabinet-menedzhera/club_temp/table/([0-9]+)#",
		"RULE"	=>	"ID=$1",
		"PATH"	=>	"/kabinet-menedzhera/club_temp/table/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/([0-9]+)/booking/([0-9]+)/payment/#",
		"RULE"	=>	"CLUB_ID=$1&TABLE_ID=$2",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/club/booking/payment/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/([0-9]+)/subscribe/#",
		"RULE"	=>	"ID=$1",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/club/subscribe/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/booking/([0-9]+)#",
		"RULE"	=>	"ID=$1",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/club/booking/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/([0-9]+)/stock/#",
		"RULE"	=>	"ID=$1",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/club/stock/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/([0-9]+)/event/#",
		"RULE"	=>	"ID=$1",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/club/event/index.php",
	),
	array(
		"CONDITION"	=>	"#^/club/([0-9]+)#",
		"RULE"	=>	"ID=$1&CLUB=true",
		"ID"	=>	"mytb:club",
		"PATH"	=>	"/club/index.php",
	),
	array(
		"CONDITION"	=>	"#^/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/news/index.php",
	),
);

?>