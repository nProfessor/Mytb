<?
$ClubList = $arResult["ClubList"];

?>


<? foreach ($ClubList as $var): ?>
    <?
    $rating=intval($var["PROPERTY_RATING_VALUE"]);
    ?>
<div class="club_list_item">
    <table width="100%" class="table-list-club">
        <tr>
            <td width="200px">
                <a href='/club/<?=$var["ID"]?>'>
                <img src="<?=$var["PREVIEW_PICTURE"]?>" width="200px">
                </a></td>
            <td>
                <strong>
                    <span class="label label-info right"><?=$rating?> <?=declOfNum($rating,array("голос","голоса","голосов"))?></span>

                    <a href='/club/<?=$var["ID"]?>'><?=html_entity_decode($var["NAME"])?></a></strong>
                <table class="club_info_shot">
                    <?if (!empty($var["PROPERTY_RATING_VALUE"])): ?>
                    <tr>
                        <th>
                            Рейтинг
                        </th>
                        <td><?=$var["PROPERTY_RATING_VALUE"]?>
                        </td>
                    </tr>
                    <? endif;?>
                    <?if (!empty($var["PROPERTY_METRO_VALUE"])): ?>
                    <tr>
                        <th>
                            Метро
                        </th>
                        <td><?=$var["PROPERTY_METRO_VALUE"]?>
                        </td>
                    </tr>
                    <? endif;?>
                    <?if (!empty($var["PROPERTY_TIME_WORKING_VALUE"])): ?>
                    <tr>
                        <th>
                            Часы работы
                        </th>
                        <td><?=$var["PROPERTY_TIME_WORKING_VALUE"]?>
                        </td>
                    </tr>
                    <? endif;?>
                    <?if (!empty($var["PROPERTY_PRICE_COCKTAIL_VALUE"])): ?>
                    <tr>
                        <th>
                            Цена коктейля
                        </th>
                        <td><?=$var["PROPERTY_PRICE_COCKTAIL_VALUE"]?>
                        </td>
                    </tr>
                    <? endif;?>
                    <?if (!empty($var["PROPERTY_CARDS_VALUE"])): ?>
                    <tr>
                        <th>
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

<?=$arResult["res"]->NavPrint("",false,"pagination","/include/paginator/home.php");?>