<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>

    <link rel="stylesheet" type="text/css" href="/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="/css/styles.css"/>
    <?if($USER->IsAuthorized()){
    $APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
    $APPLICATION->AddHeadScript("/jslibs/bootstrap/js/bootstrap.min.js");
    $APPLICATION->SetAdditionalCSS("/jslibs/bootstrap/css/bootstrap.min.css");
};?>

    <?$APPLICATION->ShowHead();?>
    <link rel="stylesheet" type="text/css" href="/css/colors.css"/>
</head>
<body>
<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/.default/include/menu_top.php")?>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div id="header">

</div>
<div style="margin:0 auto; width:940px;" >

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <?$APPLICATION->IncludeComponent("bitrix:menu", "tree", array(
                "ROOT_MENU_TYPE" => "left",
                "MENU_CACHE_TYPE" => "Y",
                "MENU_CACHE_TIME" => "36000000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(
                ),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "left",
                "USE_EXT" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        );?>
        </div>
        <div class="span9">


