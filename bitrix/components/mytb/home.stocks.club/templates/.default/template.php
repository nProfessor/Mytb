<? if (count($arResult['clubList']) > 0): ?>

<ul>
        <?foreach ($arResult['clubList'] as $val=> $var): ?>

       <li><a href="/club/<?=formUrl($var["ID"],implode("-",$var['PROPERTY_TYPE_FACILITY_VALUE'])." ".$var['NAME'])?>/#stock">
           <?if(count($var['PROPERTY_TYPE_FACILITY_VALUE'])>0):?>
           <?=implode("/",$var['PROPERTY_TYPE_FACILITY_VALUE']);?>
        <?else:?>
           <?$var['PROPERTY_TYPE_FACILITY_VALUE'][0]?>
        <?endif;?>
           <b><?=$var['NAME']?></b></a></li>
        <? endforeach;?>
</ul>
    <div class="all_list">
        <a href="/search/filter/?STOCKS=true" title="Показать ве клубы, бары, рестораны и кафе у которых есть акции и скидки">Все заведения с акциями</a>
    </div>
<? else: ?>
    <noindex>
На данный момент акций нет
    </noindex>
<?endif; ?>