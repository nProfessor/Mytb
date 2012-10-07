<?if($USER->IsAuthorized()):?>
<div class="navbar navbar-fixed-top">
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a href="/" class="brand span3">MyTB.ru</a>

                <ul class="nav">
                    <li><a href="/">Главная</a></li>
                    <li><a href="/personal/">Профайл</a></li>
                    <li><a href="/personal/subscribe/">Подписки</a></li>
                    <li><a href="/personal/settings/profile-edit/">Настройки</a></li>
                    <!--                    <li class="dropdown">-->
                    <!--                        <a href="/personal/moi-broni/">-->
                    <!--                            Мои брони-->
                    <!--                        </a>-->
                    <!--                    </li>-->
                </ul>

                <ul class="nav pull-right">
                    <li><a href="#">Вы вошли как: <?=$USER::GetFullName()?></a></li>
                    <li><a href="/auth/?logout=yes">выйти</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?endif;?>