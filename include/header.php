<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="header">
    <div class="header_conten">
        <div class="menu_top pull-right">
            <? if (!$USER->IsAuthorized()): ?>
            <a href="/auth/" class="login_show" data-target="#auth_block_top" data-toggle="modal"  title="Войти в личный кабинет"><span>Вход</span></a>
            <?else:?>
            <a href="/personal/" title="Войти в личный кабинет"><span>Личный кабинет</span></a>
            <? endif;?>
            <a href="/page/partnership/" title="Партнерство для клубов, купонных сервисов, рефералов"><span>Партнерам</span></a>
        </div>
        <div class="pull-right popular_top">
            <a href="/page/popular/" title="Рейтинг самых популярных заведений">Самые популярные<span></span></a>
        </div>
    <a href="/" class="logo"></a>
        <div class="logo_title">
            <span>— первыми сообщим тебе о скидках</span>
        </div>
    </div>
</div>

<?$APPLICATION->IncludeComponent("mytb:auth", "top", array(
        "START_FROM" => "1",
        "PATH" => "",
        "LOGIN_TOP_REDIRECT" => "/personal/",
        "SITE_ID" => SITE_ID
    ),
    FALSE
);?>
