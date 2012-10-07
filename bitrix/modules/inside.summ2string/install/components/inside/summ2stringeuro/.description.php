<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("summ2string"),
	"DESCRIPTION" => GetMessage("summ2string_DESC"),
	"ICON" => "/images/summ2stringeuro.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "inside",
        "NAME" => GetMessage("DEVELOPER_FOLDER"),
		"CHILD" => array(
			"ID" => "summ2string",
			"NAME" => GetMessage("summ2string_COMPONENT_FOLDER"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "summ2stringeuro",
			),
		),
	),
);
?>