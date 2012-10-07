<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("inside.summ2string")) return;

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "SUMM_VALUE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("summ2string_summ"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
    "SUMM_CAPITALIZE" => Array(
  			"PARENT" => "DATA_SOURCE",
  			"NAME" => GetMessage("summ2string_capitalize"),
  			"TYPE" => "CHECKBOX",
  			"DEFAULT" => "N",
  		),
    )
);