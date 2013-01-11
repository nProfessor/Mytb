<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die(); ?>
<?
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico"/>

    <link rel="stylesheet" type="text/css" href="/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="/css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="/css/colors.css"/>
    <?
    $APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
    $APPLICATION->AddHeadScript("/jslibs/bootstrap/js/bootstrap.min.js");
    $APPLICATION->SetAdditionalCSS("/jslibs/bootstrap/css/bootstrap.min.css");
    ?>
    <?$APPLICATION->ShowHead();?>

    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-33379252-1']);
        _gaq.push(['_setDomainName', 'mytb.ru']);
        _gaq.push(['_trackPageview']);

        (function () {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <script type="text/javascript" src="/jslibs/script/analitics.js"></script>

</head>
<body>
<?$APPLICATION->IncludeFile(
    SITE_DIR . "include/header.php",
    Array(),
    Array("MODE" => "html")
);?>
<div class="content">
        <?$APPLICATION->IncludeComponent("bitrix:search.form", "small", array(
            "PAGE" => "#SITE_DIR#search/index.php"
        ),
        FALSE
    );?>

    <br/>


