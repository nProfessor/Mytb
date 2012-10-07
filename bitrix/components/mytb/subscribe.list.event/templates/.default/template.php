<h1>События</h1>


<select class="pull-right" id="filter_club">
    <option value="0">Все клубы</option>
    <?foreach ($arResult['club'] as $val=> $var): ?>
    <option value="<?=$var["ID"]?>" <?if ($arResult['clubID'] == $var["ID"]
    ) {
        ?>selected<? }?>><?=$var["NAME"]?></option>
    <? endforeach;?>
</select>
<div id="list" class="span12">
    <?if (count($arResult['eventList'])): ?>
    <table class="table table-striped">
        <?foreach ($arResult['eventList'] as $val=> $var): ?>
        <tr>
            <th colspan="2"><?=$val?> <?if (strtotime($val) < time()): ?><span class="label label-important pull-right">прошли</span><? endif;?>
            </th>
        </tr>
        <? foreach ($var as $event): ?>
            <? //
            $club = $arResult['club'][$event["PROPERTY_CLUB_ID_VALUE"]];
            ?>
            <tr>
                <td width="180px">
                    <img src="<?=imgurl($event["DETAIL_PICTURE"], array("w"=> 160, "h"=> 120))?>" class="img-rounded"
                         width="160px">
                </td>
                <td>
                    <h5><?=$event["NAME"]?></h5><br>

                    <p><?=$event["PREVIEW_TEXT"]?></p>
                    <small>Клуб: <a href="/club/<?=$club['ID']?>" target="_blank"><?=$club["NAME"]?></a></small>
                </td>
            </tr>
            <? endforeach; ?>
        <? endforeach;?>
    </table>

    <? else: ?>
    Нет событий
    <?endif;?>

</div>