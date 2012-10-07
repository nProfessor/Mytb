<?
$APPLICATION->AddHeadScript("/jslibs/jquery/eTextTimer.js");
?>
<h1>Действующие акции клуба «<?=$arResult['club']['NAME']?>» </h1>
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
            <th colspan="2">
                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="stock_info"><?=$var["NAME"]?></a>
            </th>
        </tr>
        <tr>
            <td>
                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w"=> 300, "h"=> 200))?>"/>
                </a>
                <div class="time_stok">

                    <span class="clock">

                    </span>
                    <div class="pull-right" style="padding-top: 15px;">
                    до конца акции:<br/>
                    <span class="timer" data-active="<?=date("d F, Y, H:m:s",strtotime($var["ACTIVE_TO"]))?>">
                    <span class="timed">88</span>д: <span class="timeh">88</span>ч: <span class="timem">88</span>м: <span class="times">88</span>c
                    </span>
                    </div>
                    <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-right">купить купон</a>
                </div>
                <input type="hidden" id="active_to" ">


                <p style="font-size: 12px;"> <?=$var["PREVIEW_TEXT"]?></p>
                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-right">Узнать больше</a>


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