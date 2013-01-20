<div id="list">
    <?if (count($arResult['stockList'])): ?>

        <?foreach ($arResult['stockList'] as $val => $stock): ?>
        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];
        ?>
        <div class="block">
        <table>
            <tr>
                <td>
                    <div class="<?=$stock["IBLOCK_ID"] == IB_SUB_STOCK_ID ? "stock" : "event"?>"></div>
                </td>
                <td>
                    <div><b>до <?=dateShowTextMonth($stock['DATE_ACTIVE_TO'])?></b></div>
                    <a href="/club/<?=$stock["IBLOCK_ID"] == IB_SUB_STOCK_ID ? "stock" : "event"?>/<?=$stock["ID"]?>"  target="_blank"><?=$stock["NAME"]?></a>
                </td>
            </tr>
        </table>
                </div>
        <? endforeach; ?>

    <? else: ?>

    <?endif;?>
</div>

<div class="template hide">
    <table>
        <tr>
            <td>
                <div class="img_stock"></div>
            </td>
            <td>
                <div><b class="time_stock"></b></div>
                <a href="" class="link_stock" target="_blank"></a>
            </td>
        </tr>
    </table>
</div>