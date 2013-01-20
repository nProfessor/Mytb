<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<?
$userRes=new User($USER::GetID());
$userInfo=$userRes->getInfo();
?>
<div class="header">
    <div class="header_conten">
        <div class="menu_top pull-right">
            <a href="/personal/" title="<?=$userInfo["LAST_NAME"]?> <?=$userInfo["NAME"]?>"><span><?=$userInfo["LAST_NAME"]?> <?=$userInfo["NAME"]?></span></a>
            <a href="/?logout=yes" title="Партнерство для клубов, купонных сервисов, рефералов"><span>Выйти</span></a>
        </div>
        <a href="/" class="logo"></a>
        <div class="logo_title">
            <span>— первыми сообщим тебе о скидках</span>
        </div>
    </div>
</div>


