<?
$NOTICE = $arResult["NOTICE"];
?>
<h1>Уведомления</h1>
<form action="" method="post">
    <br>
    <p>
        Здесь вы можете настроить частоту и тип оповещений Вас об акциях, событиях и новостяк, заведений, на рассылки которых вы подписанны.
<ul>
    <li>0 - оповещения будут приходить, как только событие появилось в нашей системе</li>
    <li>другие цифры - оповещения будут приходить, с промежутком в указанное колличество дней</li>
</ul>
    Всю подписку вы можете видеть в <a href="/personal/subscribe/">личном кабинете</a>
    </p>
    <br>
<table class="table table-striped">
    <tr>
        <th>Способы уведомления</th>
        <th></th>
        <th>СМС</th>
        <th>Email</th>
    </tr>
    <tr>
        <th>Акции</th>
        <td>раз в <input type="text" class="input-mini" value="<?=intval($NOTICE['stock']['count'])?>" name="stock[count]"> дней</td>
        <td><input type="checkbox" name="stock[sms]"
                   value="1" <?if (intval($NOTICE['stock']['sms'])==1) { ?>checked="checked"<? }?>></td>
        <td><input type="checkbox" name="stock[email]"
                   value="1"  <?if (intval($NOTICE['stock']['email'])==1) { ?>checked="checked"<? }?>></td>
    </tr>
    <tr>
        <th>Новости</th>
        <td>раз в <input type="text" class="input-mini" value="<?=intval($NOTICE['news']['count'])?>" name="news[count]"> дней</td>
        <td><input type="checkbox" name="news[sms]"
                   value="1" <?if (intval($NOTICE['news']['sms'])==1) { ?>checked="checked"<? }?>></td>
        <td><input type="checkbox" name="news[email]"
                   value="1" <?if (intval($NOTICE['news']['email'])==1) { ?>checked="checked"<? }?>></td>
    </tr>
    <tr>
        <th>События</th>
        <td>раз в <input type="text" class="input-mini" value="<?=intval($NOTICE['event']['count'])?>" name="event[count]"> дней</td>
        <td><input type="checkbox" name="event[sms]"
                   value="1" <?if (intval($NOTICE['event']['sms'])==1) { ?>checked="checked"<? }?>></td>
        <td><input type="checkbox" name="event[email]"
                   value="1" <?if (intval($NOTICE['event']['email'])==1) { ?>checked="checked"<? }?>></td>
    </tr>

</table>

<fieldset>
    <div class="form-actions">
        <button class="btn btn-primary right" type="submit" id="save_profile" name="save">Сохранить</button>
    </div>
</fieldset>
</form>