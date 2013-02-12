    <?if(count($arResult['stockList'])>0):?>
    <table class="table">
        <?foreach ($arResult['stockList'] as $val=> $var): ?>
        <tr>
            <th width="100px"><?=date("d.m.Y",strtotime($var['DATE_ACTIVE_FROM']))?></th>
            <td><a href="/club/news/<?=$var['ID']?>/" title="<?=$var['NAME']?>"><?=$var['NAME']?></a></td>
        </tr>
        <? endforeach;?>
    </table>

<?else:?>
<noindex>
    На данный момент новостей нет
</noindex>
    <?endif;?>