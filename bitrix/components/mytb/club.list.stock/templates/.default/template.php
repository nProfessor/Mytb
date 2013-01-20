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


                <a href="/club/stock/<?=$var["ID"]?>" class="stock_title"><?=$var["NAME"]?></a>
<div class="stock_info">
                <p style="font-size: 12px;"> <?=str_replace("\n","<br/>",$var["PREVIEW_TEXT"])?></p>
</div>

            </td>

        </tr>

        <? endforeach;?>
    </table>

</div>
<? else: ?>
<noindex>
    На данный момент акций нет
</noindex>
<?endif; ?>