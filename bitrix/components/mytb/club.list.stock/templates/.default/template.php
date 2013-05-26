<? if (count($arResult['stockList']) > 0): ?>
<div id="list">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val => $var): ?>

        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];

        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        $partner = Kupon::getDataServise($var['TAGS']);

        ?>
        <tr>
            <td>
                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300))?>"/>
                </div>


                <a href="/club/stock/<?=$var["ID"]?>" class="stock_title"  title="<?=$partner['name']?>: <?=$var["NAME"]?>"><?=$var["NAME"]?></a>
<div class="stock_info">
    <strong>Скидка представлена на сайте <?=$partner['name']?></strong><br/>
                <p style="font-size: 12px;"> <?=str_replace("\n","<br/>",cut_string(trim($var["PREVIEW_TEXT"]),400))?></p>
    <a href="/club/stock/<?=$var["ID"]?>" class="button right" title="<?=$partner['name']?>: <?=$var["NAME"]?>">Подробней</a>
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