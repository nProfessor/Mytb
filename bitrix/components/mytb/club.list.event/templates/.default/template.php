<h1>События клуба  «<?=$arResult['club']['NAME']?>»</h1>
    <br/>
    <?if(count($arResult['eventList'])>0):?>
<div id="list" class="span12">
    <table class="table table-striped">
        <?foreach ($arResult['eventList'] as $val=> $var): ?>
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

</div>
<?else:?>
    На данный момент событий нет.
    <?endif;?>