<?

//$APPLICATION->AddHeadScript("/jslibs/script/ajaxupload.js");



$clubInfo=$arResult['club'];
$stock=$arResult['STOCK'];

$stock['ACTIVE_FROM']=$stock['ACTIVE_FROM']=="01.01.1970"?"":$stock['ACTIVE_FROM'];
$stock['ACTIVE_TO']=$stock['ACTIVE_TO']=="01.01.1970"?"":$stock['ACTIVE_TO'];
?>


<div class="right w8">
    <div class="padding_lr_10">
        <b>Редактируем:</b> <a href="/personal/club/list-stocks">Все события</a> >> <?=$stock['NAME']?>
<br/>
<br/>
        <div class="alert alert-block alert-error fade in">
    
            <h4 class="alert-heading">Вы не можете редактировать это событие!</h4>
            <p>Это событие уже прошло. Вы можете добавить фотографии отчета о событи.</p>
        </div>

    <input type="hidden" id="stockID" value="<?=$stock['ID']?>">
<table class="table">
    <tr>
        <th>Дата начала</th>
        <td>
            <?=empty($stock['ACTIVE_FROM'])?date("d.m.Y"):$stock['ACTIVE_FROM'];?>
        </td>
    </tr>
    <tr>
        <th>Дата окончания</th>
        <td>
           <?=empty($stock['ACTIVE_TO'])?date("d.m.Y"):$stock['ACTIVE_TO'];?>
        </td>
    </tr>
    <tr>
        <th>Название</th>
        <td><?=$stock['NAME']?></td>
    </tr>
    <tr>
        <th>Описание</th>
        <td><?=$stock['~DETAIL_TEXT']?></td>
    </tr>
    <tr>
        <th>
            Фотография
        </th>
        <td>
                <img src="<?=$stock["DETAIL_PICTURE"]?>" >
        </td>
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