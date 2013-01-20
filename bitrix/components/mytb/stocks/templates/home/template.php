<? if (count($arResult['stockList']) > 0): ?>
<ul>
        <?foreach ($arResult['stockList'] as $val=> $var): ?>

       <li><a href="#"><?=$var['NAME']?></a></li>
        <? endforeach;?>
</ul>
<? else: ?>
На данный момент акций нет
<?endif; ?>