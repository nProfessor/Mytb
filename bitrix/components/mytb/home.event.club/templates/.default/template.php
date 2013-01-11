<? if (count($arResult['clubList']) > 0): ?>

<ul>
        <?foreach ($arResult['clubList'] as $val=> $var): ?>

       <li><a href="/club/<?=$var['ID']?>/#event">
           <?if(count($var['PROPERTY_TYPE_FACILITY_VALUE'])>0):?>
           <?=implode("/",$var['PROPERTY_TYPE_FACILITY_VALUE']);?>
        <?else:?>
           <?$var['PROPERTY_TYPE_FACILITY_VALUE'][0]?>
        <?endif;?>
           <b><?=$var['NAME']?></b></a></li>
        <? endforeach;?>
</ul>
    <div class="all_list">
<a href="">Все заведения с событиями</a>
    </div>
<? else: ?>
        <div class="all_list">
На данный момент события нет ни в одном заведении
        </div>
<?endif; ?>