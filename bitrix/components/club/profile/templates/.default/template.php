<?
$clubInfo = $arResult['arFields'];
$rating = intval($arResult['arFields']['PROPERTY_RATING_VALUE']);
$subStocs = $arResult['subs']['stock'];
$subEvent = $arResult['subs']['event'];

?>
<div class="right w8">
    <div class="padding_l_25">
        <h1><?=$clubInfo['NAME']?></h1>
        <div><a href="/club/<?=$clubInfo['ID']?>" target="_blank">Перейти в карточку заведения</a></div><br/>
        <table class="table">

            <tr>
                <th colspan="2">Количество подписчиков</th>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Акции</td>
                <td><?=$subStocs?></td>
            </tr>
            <tr>
                <td></td>
                <td>События</td>
                <td><?=$subEvent?></td>
            </tr>
            <tr>
                <th colspan="2">Рейтинг заведения</th>
                <td></td>
            <tr>
                <td></td>
                <td>Голосов</td>
                <td><?=$rating?></td>
            </tr>
        </table>

    </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("club:menu.left", "", array(
        "CLUB_ID" => $clubInfo['ID'],
    ),
    false
);?>
</div>