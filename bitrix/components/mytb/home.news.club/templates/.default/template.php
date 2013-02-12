<? if (count($arResult['clubList']) > 0): ?>

<ul>
        <?foreach ($arResult['clubList'] as $val=> $var): ?>

       <li><a href="/club/<?=$var['ID']?>/#news">
           <?if(count($var['PROPERTY_TYPE_FACILITY_VALUE'])>0):?>
           <?=implode("/",$var['PROPERTY_TYPE_FACILITY_VALUE']);?>
        <?else:?>
           <?$var['PROPERTY_TYPE_FACILITY_VALUE'][0]?>
        <?endif;?>
           <b><?=$var['NAME']?></b></a></li>
        <? endforeach;?>
</ul>
    <div class="all_list">
        <a href="/sub/news/" title="Показать ве клубы, бары, рестораны и кафе у которых есть новости">Все заведения с новостями</a>
    </div>
<? else: ?>
<div class="all_list">
    На данный момент нет заведений с новостями
</div>

<?endif; ?>