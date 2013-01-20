<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
IncludeTemplateLangFile(__FILE__);
global $USER;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico"/>

    <link rel="stylesheet" type="text/css" href="/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="/css/styles.css"/>
<?if($USER->IsAuthorized()){
    $APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");

    $APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery.ui.core.min.js");
    $APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery.ui.datepicker.js");
    $APPLICATION->AddHeadScript("/jslibs/bootstrap/js/bootstrap.min.js");
    $APPLICATION->SetAdditionalCSS("/jslibs/bootstrap/css/bootstrap.min.css");
};?>

    <?$APPLICATION->ShowHead();?>

    <!--[if lte IE 6]>
    <style type="text/css">

        #support-question {
            background-image: none;
            filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src = './images/question.png', sizingMethod = 'crop');
        }

        #support-question {
            left: -9px;
        }

        #banner-overlay {
            background-image: none;
            filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src = './images/overlay.png', sizingMethod = 'crop');
        }

    </style>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="/css/colors.css"/>

</head>
<body>
<?if($USER->IsAuthorized()):?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a href="/" class="brand">Сайт</a>

            <ul class="nav">
                <li><a href="/personal/">Кабинет пользователя</a></li>
                <li class="dropdown">
                    <a href="#"
                       class="dropdown-toggle"
                       data-toggle="dropdown">
                        Возможности
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/staff/moderator.auto.club/">Модерация клубов</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="nav pull-right">
                <li><a href="#">Вы вошли как: <?=$USER::GetFullName()?></a></li>
                <li><a href="/auth/?logout=yes">Выйти</a></li>
            </ul>
        </div>
    </div>
</div>
<?endif;?>
<div id="page-wrapper">

    <div id="panel"><?$APPLICATION->ShowPanel();?></div>
    <div id="header">

    </div>




    <div id="personal_content">