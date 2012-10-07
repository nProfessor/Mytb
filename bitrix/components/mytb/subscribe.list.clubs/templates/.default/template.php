<?
$EVENT = $arResult['SUBS']["EVENT"];
$STOK  = $arResult['SUBS']["STOK"];
$NEWS  = $arResult['SUBS']["NEWS"];
?>

<h1>Клубы, на которые вы подписаны</h1>

<br/>
<div id="list" class="span12">
    <?if (count($arResult['club'])): ?>
    <form action="" method="post">
    <table class="table table-striped">
        <tr>
            <th>Клубы</th>
            <th width="40px">Акции</th>
            <th width="40px">События</th>
            <th width="40px">Новости</th>
        </tr>
        <? foreach ($arResult['club'] as $club): ?>
        <tr>
            <td>
                <a href="/club/<?=$club["ID"]?>/" target="_blank"><?=$club["NAME"]?></a>
            </td>
            <th>
                <input type="checkbox" value="<?=$club['ID']?>" name="stok[]" class="right" <?if(in_array($club['ID'],$STOK)){?>checked="checked"<?}?>>
            </th>
            <th>
                <input type="checkbox" value="<?=$club['ID']?>" name="event[]" class="right"  <?if(in_array($club['ID'],$EVENT)){?>checked="checked"<?}?>>
            </th>
            <th>
                <input type="checkbox" value="<?=$club['ID']?>" name="news[]" class="right"  <?if(in_array($club['ID'],$NEWS)){?>checked="checked"<?}?>>
            </th>
        </tr>

        <? endforeach;?>
    </table>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary right" name="save">Сохранить</button>
    </div>
    </form>
    <? else: ?>
    Вы не подписаны ни на один клуб.
    <?endif;?>

</div>