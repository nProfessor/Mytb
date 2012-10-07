<?$APPLICATION->AddHeadScript("/jslibs/jquery/eTextTimer.js");?>
<input type="hidden" id="time_now" value="<?=date("d F, Y, H:m:s")?>">

<div id="home-carusel" class="carousel slide">
    <div class="carousel-inner">

        <?foreach ($arResult['stockList'] as $val=> $var): ?>
        <div class="item <?if ($val == 0): ?>active<? endif;?>">
            <div style="display:block;float:left;width:400px">
                <?
                $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
                ?>

                        <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-left">
                            <img src="<?=imgurl($arFile["SRC"], array("w"=> 400, "h"=> 270))?>" width="400px" height="270px"/>
                        </a>

                       </div><div style="display: inline-block;width:190px">
                        <div class="tr-back-shadow">

                        <?
                        if (intval($var["PROPERTY_PRICECOUPON_VALUE"]) > 0):?>
                            <h3>Купить скидку <?=$var["PROPERTY_DISCOUNT_VALUE"]?>%</h3>
                            <? else: ?>
                            <h3>Купить со скидкой <?=$var["PROPERTY_DISCOUNT_VALUE"]?>%</h3>
                            <?endif;?>
                            <a href="/club/<?=$var["PROPERTY_CLUB_ID_VALUE"]?>" title="<?=$arResult['clubList'][$var["PROPERTY_CLUB_ID_VALUE"]]['NAME']?>">«<?=$arResult['clubList'][$var["PROPERTY_CLUB_ID_VALUE"]]['NAME']?>»</a>
                        <div class="time_stok">
                            <div>
                                до конца акции:<br/>
                    <span class="timer" data-active="<?=date("d F, Y, H:m:s", strtotime($var["ACTIVE_TO"]))?>"  data-id="<?=$var["ID"]?>">
                    <span class="time<?=$var["ID"]?>d">88</span>д: <span class="time<?=$var["ID"]?>h">88</span>:<span
                            class="time<?=$var["ID"]?>m">88</span>:<span class="time<?=$var["ID"]?>s">88</span>
                    </span>
                            </div>

                        </div>
                        <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="btn btn-success">купить купон
                            за <?=$var["PROPERTY_PRICECOUPON_VALUE"]?>р.</a>
                        </div>
        </div>
            <div class="carousel-caption">
                <p class="stock-name">
                                    <?=$var["NAME"]?>
                </p>
            </div>
        </div>
        <? endforeach;?>
    </div>
    <a class="stoks" href="/stocks">все акции</a>
    <a class="carousel-control left" href="#home-carusel" data-slide="prev">&lsaquo;</a>
    <a class="carousel-control right" href="#home-carusel" data-slide="next">&rsaquo;</a>
</div>