<h1>Действующие акции клуба  «<?=$arResult['club']['NAME']?>» </h1>
    <br/>
    <?if(count($arResult['stockList'])>0):?>
<div id="list" class="span12">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val=> $var): ?>
        <tr>
            <th colspan="3"><?=$val?></th>
        </tr>
        <? foreach ($var as $stock): ?>
            <?
            $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];
            ?>
            <tr>
                <td></td>
                <td><a href="#" data-toggle="modal" data-target="#<?=$stock["ID"]?>"
                       data-title="<?=strip_tags($stock["NAME"])?>"
                       data-description="<?=strip_tags($stock["PREVIEW_TEXT"])?>"
                       data-img="<?=$stock["DETAIL_PICTURE"]?>"
                       class="stock_info"><?=$stock["NAME"]?></a><br/>
                    <small>до <?=$stock['DATE_ACTIVE_TO']?></small>
                </td>
                <td>
                </td>
                <td>
                    <a href="/club/<?=$club['ID']?>" target="_blank"><?=$club["NAME"]?></a>

                </td>
            </tr>
            <? endforeach; ?>
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
<?else:?>
    На данный момент акций нет
    <?endif;?>