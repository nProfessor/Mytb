<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico"/>

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
        <div class="12">

