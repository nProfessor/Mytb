<?
$userInfo = $arResult['userinfo'];
?>
<div class="span4">
    <div class="thumbnail">
        <img  src="<?=imgurl(empty($userInfo["PERSONAL_PHOTO"]['ORIGINAL_NAME'])?DEFAULT_USER_PHOTO_PATH:"/upload/profile/{$userInfo["ID"]}/{$userInfo["PERSONAL_PHOTO"]['ORIGINAL_NAME']}",array("w"=>320,"h"=>240))?>">
        <div class="caption">
            <h3><?=$userInfo["LAST_NAME"]?> <?=$userInfo["NAME"]?></h3>
        </div>
    </div>
    <div>

    </div>

</div>
<div class="span8">
<pre>
<h4>Здравствуйте!</h4>
Мы рады видеть Вас на нашем сайте.

Здесь вы можете найти ваши любимые заведения и подписаться на уведомления об
акциях, событиях и новостях этих заведений.

Настроить периоды уведомлений вы можете <a href="/personal/settings/subs-settings/" title="Настроить периуды уведомлений">в настройках</a>.
Посмотреть <a href="/personal/subscribe/stock/" title="Подписка на Акции">Акции</a>, <a href="/personal/subscribe/news/" title="Подписка на новости">Новости</a> и <a href="/personal/subscribe/event/" title="Подписка на События">События</a> на которые вы подписаны,
можно в разделе "<a href="/personal/subscribe/" title="Подписки">Подписки</a>"
</pre>

</div>