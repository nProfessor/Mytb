<h2>Список новостей  «<?=$arResult['club']['NAME']?>» </h2>
<br/>

<form action="/personal/kabinet-menedzhera/club_news_edite/edite/" method="post">
<div class="input-append">
    <input type="text" class="span5 search-query" placeholder="Навзвание новости" name="NAME"><button type="submit" class="btn btn-primary" name="ADD_NEWS">Добавить новость</button>
</div>
</form>
    <br/>
<br/>
<table class="table table-striped">
    <tr>
        <th>Дата</th>
        <th>Название</th>
        <th></th>
    </tr>

    <?if(count($arResult["NEWS_LIST"])>0):?>
<div id="list" class="span12">
    <?foreach ($arResult["NEWS_LIST"] as $val=> $var): ?>
    <tr>
        <td class="span1"><?=$var["ACTIVE_FROM"]?></td>
        <td><?=$var["NAME"]?></td>
        <td class="span1"><a href="/kabinet-menedzhera/club_news_edite/edite/<?=$var["ID"]?>">редактировать</a></td>
    </tr>

    <? endforeach;?>
<?else:?>
    <tr>
        <td colspan="3">На данный момент новостей нет</td>
    </tr>

    <?endif;?>

</table>