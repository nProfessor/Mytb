<? if (count($arResult['stockList']) > 0): ?>
<div id="list">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val => $var): ?>

        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];

        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        ?>
        <tr>
            <td>
                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300, "h" => 200))?>"/>
                </div>


                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="stock_title"><?=$var["NAME"]?></a>
<div class="stock_info">
                <p style="font-size: 12px;"> <?=str_replace("\n","<br/>",$var["PREVIEW_TEXT"])?></p>
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
<input type="hidden" id="club_name" value="<?=$arResult['club']['NAME']?>"/>
<?$clubIMG= CFile::GetFileArray($arResult['club']['PREVIEW_PICTURE']);?>
<input type="hidden" id="club_img" value="<?=imgurl($clubIMG["SRC"],array("w"=>200))?>"/>


<input id="redirect" type="hidden" value="/club/<?=$arResult['club']["ID"]?>/stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "", array("AUTH_URL" => "/club/{$arResult['club']["ID"]}/stock/?subscribe=ok&login=yes"), FALSE); ?>
<? $APPLICATION->IncludeComponent("mytb:auth", "stocks", array("AUTH_URL" => "/club/{$arResult['club']["ID"]}/stock/?auth=login"), FALSE); ?>