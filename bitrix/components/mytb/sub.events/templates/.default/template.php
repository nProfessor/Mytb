<div class="filter_result">
    <h1>Все заведения с предстоящими и текущими событиями</h1>

    <?foreach($arResult["CLUB_LIST"] as $var):?>
    <?
    $rating=intval($var["PROPERTY_RATING_VALUE"]);
    $url=formUrl($var["ID"],implode("-",$var["PROPERTY_TYPE_FACILITY_VALUE"])." ".$var['NAME']);
    ?>
    <div class="club_list_item">
        <table width="100%" class="table-list-club">
            <tr>
                <td width="200px" valign="middle">
                    <div class="img-polaroid">

                        <a href='/club/<?=$url?>/' title="<?=html_entity_decode($var["NAME"])?>" style="background: #fff url(<?=$var["PREVIEW_PICTURE"]?>) no-repeat center center">
                    </a>
                    </div>
                </td>
                <td>
                    <strong>
                        <span class="right rating_show">
                            <i class="icon-star"></i>
                                <?=$rating?> <?=declOfNum($rating,array("голос","голоса","голосов"))?>
                        </span>


                        <a href='/club/<?=$url?>'><?=html_entity_decode($var["NAME"])?></a>
                    </strong>
                    <?if(intval($arResult["stocksCount"][$var["ID"]])>0):?>
                    <div class="pull-right akcia_count">
                        <a href="/club/<?=$url?>/#stock"><?=$arResult["stocksCount"][$var["ID"]]?> <span><?=declOfNum($arResult["stocksCount"][$var["ID"]],array("акция","акции","акций"))?></span></a>
                    </div>
                    <?endif;?>
                    <table class="club_info_shot">
                        <?if (!empty($var["PROPERTY_METRO_VALUE"])): ?>
                        <tr>
                            <th width="120px;">
                                Метро
                            </th>
                            <td><?=$var["PROPERTY_METRO_VALUE"]?>
                            </td>
                        </tr>
                        <? endif;?>
                        <?if (!empty($var["PROPERTY_TIME_WORKING_VALUE"])): ?>
                        <tr>
                            <th width="120px;">
                                Часы работы
                            </th>
                            <td><?=$var["PROPERTY_TIME_WORKING_VALUE"]?>
                            </td>
                        </tr>
                        <? endif;?>
                        <?if (!empty($var["PROPERTY_PRICE_COCKTAIL_VALUE"])): ?>
                        <tr>
                            <th width="120px;">
                                Цена коктейля
                            </th>
                            <td><?=$var["PROPERTY_PRICE_COCKTAIL_VALUE"]?>
                            </td>
                        </tr>
                        <? endif;?>
                        <?if (!empty($var["PROPERTY_CARDS_VALUE"])): ?>
                        <tr>
                            <th width="120px;">
                                Кредитные карты
                            </th>
                            <td><?=$var["PROPERTY_CARDS_VALUE"]?>
                            </td>
                        </tr>
                        <? endif;?>


                    </table>
                </td>
            </tr>
        </table>
    </div>
    <? endforeach; ?>

</table>
    <?=$arResult["NAV_STRING"] ?>
</div>
    <div class="clear_both"></div>