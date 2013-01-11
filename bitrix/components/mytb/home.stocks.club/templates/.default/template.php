<? if (count($arResult['clubList']) > 0): ?>

<ul>
        <?foreach ($arResult['clubList'] as $val=> $var): ?>

       <li><a href="/club/<?=$var['ID']?>/#stocks">
           <?if(count($var['PROPERTY_TYPE_FACILITY_VALUE'])>0):?>
           <?=implode("/",$var['PROPERTY_TYPE_FACILITY_VALUE']);?>
        <?else:?>
           <?$var['PROPERTY_TYPE_FACILITY_VALUE'][0]?>
        <?endif;?>
           <b><?=$var['NAME']?></b></a></li>
        <? endforeach;?>
</ul>
    <div class="all_list">
<a href="">Все заведения с акциями</a>
    </div>
<? else: ?>
На данный момент акций нет
<?endif; ?>