<h2>Список событий  «<?=$arResult['club']['NAME']?>» </h2>
<br/>

<form action="/personal/kabinet-menedzhera/club_event_edite/edite/" method="post">
<div class="input-append">
    <input type="text" class="span5 search-query" placeholder="Навзвание события" name="NAME"><button type="submit" class="btn btn-primary" name="ADD_EVENT">Добавить событие</button>
</div>
</form>
    <br/>
<br/>
<table class="table table-striped">
    <tr>
        <th>Дата начала</th>
        <th>Дата окончания</th>
        <th>Название</th>
        <th></th>
    </tr>

    <?if(count($arResult["EVENT_LIST"])>0):?>
<div id="list" class="span12">
    <?foreach ($arResult["EVENT_LIST"] as $val=> $var): ?>
    <tr>
        <td class="span1"><?=$var["ACTIVE_FROM"]?></td>
        <td class="span1"><?=$var["ACTIVE_TO"]?></td>
        <td><?=$var["NAME"]?></td>
        <td class="span1"><a href="/kabinet-menedzhera/club_event_edite/edite/<?=$var["ID"]?>">редактировать</a></td>
    </tr>

    <? endforeach;?>
<?else:?>
    <tr>
        <td colspan="3">На данный момент новостей нет</td>
    </tr>

    <?endif;?>

</table>