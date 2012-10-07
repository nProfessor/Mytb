<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arCountValues = array('5' => 5, '10' => 10, '15' => 15, '20' => 20);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"APP_ID" => array(
			"NAME"=>GetMessage("APP_ID"),
			"TYPE" => "NUMBER",
			"DEFAULT"=>'',
			"PARENT" => 'BASE',
		),
		"COUNT" => array(
			"NAME"=>GetMessage("COUNT"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT"=>10,
			"VALUES" => $arCountValues,
			"PARENT" => 'BASE',
		),
		"ALLOW_GRAFFITI" => array(
			"NAME"=>GetMessage("ALLOW_GRAFFITI"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'N',
			"PARENT" => 'BASE',
		),
		"ALLOW_PHOTOS" => array(
			"NAME"=>GetMessage("ALLOW_PHOTOS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'N',
			"PARENT" => 'BASE',
		),
		"ALLOW_VIDEOS" => array(
			"NAME"=>GetMessage("ALLOW_VIDEOS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'N',
			"PARENT" => 'BASE',
		),
		"ALLOW_AUDIO" => array(
			"NAME"=>GetMessage("ALLOW_AUDIO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'N',
			"PARENT" => 'BASE',
		),
		"ALLOW_LINKS" => array(
			"NAME"=>GetMessage("ALLOW_LINKS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'N',
			"PARENT" => 'BASE',
		),
		"WIDTH" => array(
			"NAME"=>GetMessage("WIDTH"),
			"TYPE" => "NUMBER",
			"DEFAULT"=>496,
			"PARENT" => 'BASE',
		)
	)
);

?>