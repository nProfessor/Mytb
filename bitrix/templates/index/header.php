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
<div id="page-wrapper">

    <div id="panel"><?$APPLICATION->ShowPanel();?></div>
    <table id="header">
        <tr>
            <td id="logo"><a href="<?=SITE_DIR?>"
                             title="<?=GetMessage("HDR_GOTO_MAIN")?>"><?$APPLICATION->IncludeFile(
                SITE_DIR . "include/company_name.php",
                Array(),
                Array("MODE" => "html")
            );?></a></td>
            <td id="slogan"><?$APPLICATION->IncludeFile(
                SITE_DIR . "include/company_slogan.php",
                Array(),
                Array("MODE" => "html")
            );?></td>
            <td>

                <ul class="nav nav-list">
                    <?
                    global $USER;
                    if ($USER->IsAuthorized()):?>
                        <?
                        $dbUser = CUser::GetByID($USER->GetID());
                        $arUser = $dbUser->Fetch();
                        ?>
                        <li class="nav-header">
                            Вы вошли как: <?=empty($arUser["NAME"])
                            ? $arUser["LOGIN"]
                            : $arUser["NAME"];?>
                        </li>
                        <li>
                            <a href="<?=CSite::InGroup((array(GROUP_MANAGER)))
                                ? "/kabinet-menedzhera/"
                                : "/personal/";?>" title="Личный кабинет">Личный кабинет</a>
                        </li>
                        <li><a href="/auth/?logout=yes" title="Выход">Выход</a></li>

                        <? else: ?>
                        <li><a href="/auth/" title="Вход/Регистрация" style="font-size:20px;">Вход/Регистрация</a></li>
                        <?endif;?>
                </ul>


            </td>
        </tr>
    </table>


    <div id="content-wrapper">
        <div id="content">
            <?if ($APPLICATION->GetCurPage(FALSE) == SITE_DIR): ?>
            <div id="banner">

            </div>
            <? else: ?>

            <div id="breadcrumb">
                <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
                    "START_FROM" => "1",
                    "PATH" => "",
                    "SITE_ID" => SITE_ID
                ),
                FALSE
            );?>
            </div>
            <?endif?>


            <div class="home_search">
                <?$APPLICATION->IncludeComponent("bitrix:search.form", "flat", array(
                    "PAGE" => "#SITE_DIR#search/index.php"
                ),
                FALSE
            );?>
                <div class="home_filter">
                <span style=" padding:10px 0px;display: block;font-weight: bold;font-size: 16px;">Или подбери заведение по вкусу</span>

                    <div class="folder">
                    <div>
                        <span>Наличие акций</span>
                        <select name="" id="">
                        <option>Неважно</option>
                        <option>Есть</option>
                        <option>Нет</option>
                        </select>
                    </div>
                    <div>
                        <span>Тип заведения</span><select name="" id=""></select>
                    </div>
                    <div>
                        <span>Музыка</span><select name="" id=""></select>
                    </div>
                    <div>
                        <span>Метро</span><select name="" id=""></select>
                    </div>
                        <span class="clear_both"></span>
                    </div>
                    <div class="padder_t_10">
                    <input type="submit" class="btn btn-info pull-right" value="Подобрать"/>
                    </div>
                    <div class="clear_both"></div>
                </div>

            </div>
            <div class="clear_both"></div>



