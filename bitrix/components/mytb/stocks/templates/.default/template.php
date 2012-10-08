<input type="hidden" id="time_now" value="<?=date("d F, Y, H:m:s")?>">
<?
$APPLICATION->AddHeadScript("/jslibs/jquery/eTextTimer.js");
?>
<h1>Действующие акции</h1>
<br/>

<input type="hidden" id="time_now" value="<?=date("d F, Y, H:m:s")?>">

<? if (count($arResult['stockList']) > 0): ?>
<div id="list">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val=> $var): ?>

        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];

        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        ?>
        <tr>
            <th colspan="2" class="stock-header">
                <div class="pull-right">
                    <?
                    if (intval($var["PROPERTY_PRICECOUPON_VALUE"]) > 0):?>
                        <h3>Купить скидку <?=$var["PROPERTY_DISCOUNT_VALUE"]?>%</h3>
                        <? else: ?>
                        <h3>Купить со скидкой <?=$var["PROPERTY_DISCOUNT_VALUE"]?>%</h3>
                        <?endif;?>
                </div>
                <a href="/club/<?=$var["PROPERTY_CLUB_ID_VALUE"]?>" style="font-size:22px;" title="<?=$arResult['clubList'][$var["PROPERTY_CLUB_ID_VALUE"]]['NAME']?>">«<?=$arResult['clubList'][$var["PROPERTY_CLUB_ID_VALUE"]]['NAME']?>»</a>

            </th>
        </tr>
        <tr>
            <td>
                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w"=> 300, "h"=> 200))?>"/>
                </a>

                <div class="time_stok pull-right">

                    <span class="clock">

                    </span>
                    <div class="pull-right" style="padding-top: 7px;">
                      до конца акции:<br>
                    <span class="timer" data-active="<?=date("d F, Y, H:m:s",strtotime($var["ACTIVE_TO"]))?>" data-id="<?=$var['ID']?>">
                    <span class="time<?=$var['ID']?>d">88</span>д: <span class="time<?=$var['ID']?>h">88</span>:<span class="time<?=$var['ID']?>m">88</span>:<span class="time<?=$var['ID']?>s">88</span>
                    </span>
                    </div><br/><br/>
                    <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="btn btn-success pull-right btn-large">купить купон
                        за <?=$var["PROPERTY_PRICECOUPON_VALUE"]?>р.</a>
                </div>

    </div>

            </td>

        </tr>

        <? endforeach;?>
    </table>

    <div class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"></h3>
        </div>
        <div class="modal-body">
            <img src="" class="img-rounded" width="600px">

            <div id="modal-text"></div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
            <a href="" class="btn btn-primary" id="link_stock">Перейти к акции</a>
        </div>
    </div>
</div>
<? else: ?>
На данный момент акций нет
<?endif; ?>