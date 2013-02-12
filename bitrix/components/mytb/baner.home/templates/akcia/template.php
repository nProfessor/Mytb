<div class="home_subscribe">
    <span>Подпишись</span><br/>
    на акции любимых заведений!
</div>
<div class="akcia pull-right">
    <div class="akcia_text_u_nas">У нас:</div>
    <div class="akcia_count_restoran"><span><a href="/search/filter/" title="Фильтр по заведениям"><?=$arResult["CLUB_COUN"]?></span><?=declOfNum($arResult["CLUB_COUN"],array("заведение","заведения","заведений"));?></a></div>
    <div class="akcia_count_akcia"><span><a href="/sub/stocks/" title="Заведения в которых есть акции"><?=$arResult["CLUB_STOCK"]?></span><?=declOfNum($arResult["CLUB_STOCK"],array("акция","акции","акций"));?></a></div>
</div>
