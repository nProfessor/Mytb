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
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>

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
<div class="content index">



    <div class="sidebar pull-right">
            <div style="padding-left:60px;margin-top: -30px;">
                    <?$APPLICATION->IncludeComponent("mytb:auth", "soc", array(
                        "START_FROM" => "1",
                        "PATH" => "",
                        "SITE_ID" => SITE_ID
                    ),
                    FALSE
                );?>

                    <?$APPLICATION->IncludeComponent("mytb:baner.home", "akcia", array(
                        "START_FROM" => "1",
                        "PATH" => "",
                        "SITE_ID" => SITE_ID
                    ),
                    FALSE
                );?>
            </div>
        <div class="clear_both"></div>
        <div class="instruction">
            <span>Инструкция:</span>
            <ul>
                <li>Найди любимые заведения;</li>
                <li>Подпишись на акции;</li>
                <li>Получай уведомления о скидках;</li>
            </ul>
        </div>
        <div class="clear_both"></div>


    </div>

    <div id="banner" class="login_show" >
        <div>
            <span>Мы — сервис подписки на акции и события</span><br/><span>ваших любимых заведений</span><br/>
            <span class="small">Мы первыми сообщим тебе об акции!</span>
        </div>
    </div>


    <div class="home_search">
        <?$APPLICATION->IncludeComponent("bitrix:search.form", "flat", array(
            "PAGE" => "#SITE_DIR#search/index.php"
        ),
        FALSE
    );?>
        <?$APPLICATION->IncludeComponent("mytb:search.filter", "", array(),
        FALSE
    );?>

    </div>



    <div class="content_news">
        <div class="home_block_news">
            <div class="block_title"><span class="stock"><i></i>Акции</span></div>
            <?$APPLICATION->IncludeComponent("mytb:home.stocks.club", "", array(
                "LIMIT" => 4,
                ""
            ),
            FALSE
        );?>
            <div class="clear_both"></div>
        </div>
        <div class="home_block_news">
            <div class="block_title"><span class="event"><i></i>События</span></div>
            <?$APPLICATION->IncludeComponent("mytb:home.event.club", "", array(
                "LIMIT" => 4
            ),
            FALSE
        );?>
            <div class="clear_both"></div>
        </div>
        <div class="home_block_news">
            <div class="block_title"><span class="news"><i></i>Новости</span></div>
            <?$APPLICATION->IncludeComponent("mytb:home.news.club", "", array(
                "LIMIT" => 4
            ),
            FALSE
        );?>
            <div class="clear_both"></div>
        </div>
        <div class="clear_both"></div>
    </div>

    <div>
        <div class="pull-right">
            <script type="text/javascript" src="//vk.com/js/api/openapi.js?75"></script>

            <!-- VK Widget -->
            <div id="vk_groups"></div>
            <script type="text/javascript">
                VK.Widgets.Group("vk_groups", {mode: 0, width: "480", height: "300"}, 45144570);
            </script>
            <div class="clear_both"></div>
        </div>
        <div class="home_seo_text" id="text_baner">
            <h3>MyTB.ru — первыми сообщим тебе о скидках </h3>
            <p>
                <ul>
            <li>— Постоянно посещаешь одни и те же заведения?</li>
            <li>— Или просто есть любимое кафе, клуб или любимый бар?</li>
            <li>— Но слишком поздно узнаешь об акциях, проводимых в них?</li>
                </ul>
            </p>
            <p>
                Тогда тебе будет полезен наш сервис. Все что тебе нужно, это найти любимое заведение у нас и <strong>подписаться на акции</strong>. После чего ты начнешь получать уведомления по СМС или Email о новых акциях, событиях и новостях данного заведения.
            </p>
            <p>
            Какую информацию, каким способом и в какое время её получать, выбираешь ты сам.
            </p>
            <p>
                <b>Чем больше подписчиков у заведения, тем чаще будут проводится в нем акции!</b>
            </p>
        </div>

        <div class="clear_both"></div>
    </div>




    <div class="about_us">
        <noindex>
        <div>
        <i></i>
        <span>О нас пишут:</span>
        </div>

            <a href="http://www.forbes.ru/video/192288-zayavka-na-konkurs-startapov-forbes-mytb" class="forbes" target="_blank"></a>
            <a href="http://www.towave.ru/content/mytb-resurs-uvedomitel-ob-aktsiyakh-klubov-barov-restoranov.html" class="towave" target="_blank"></a>
            <a href="http://nom.premiaruneta.ru/2012/site/approved/" class="premia" target="_blank"></a>
        </noindex>
    </div>