<?
$EVENT = $arResult['SUBS']["EVENT"];
$STOK  = $arResult['SUBS']["STOK"];

$day=$arResult['NOTICE']['day'];
$metod=$arResult['NOTICE']['metod'];
?>

<div class="right w8">
    <div class="padding_l_25">

    <form action="" method="post">

    <h4>Укажите, когда и как вы хотите получать уведомления</h4>
    <table class="table table-striped center">
        <tr>
            <th colspan="3"></th>
            <th width="20px">Пн</th>
            <th width="20px">Вт</th>
            <th width="20px">Ср</th>
            <th width="20px">Чт</th>
            <th width="20px">Пт</th>
            <th width="20px" class="red">Сб</th>
            <th width="20px" class="red">Вс</th>
        </tr>
        <tr>
            <th class="w1"> <input type="checkbox" class="right" name='metod[]' value="email" <?if(in_array("email",$metod)):?>checked="checked"<?endif;?>/>Email</th>
            <th class="w1"> <input type="checkbox" class="right" name='metod[]' value="sms" <?if(in_array("sms",$metod)):?>checked="checked"<?endif;?>/>SMS</th>
            <th></th>
            <?for($i=1;$i<8;$i++):?>
            <th><input type="checkbox" value="<?=$i?>" name="day[]" <?if(in_array($i,$day)):?>checked="checked"<?endif;?>/></th>
            <?endfor;?>
        </tr>

    </table>
    <?if (count($arResult['club'])): ?>

    <table class="table table-striped">
        <tr>
            <th>Клубы</th>
            <th width="40px">Акции</th>
            <th width="40px">События</th>
        </tr>
        <? foreach ($arResult['club'] as $club): ?>
        <tr>
            <td>
                <a href="/club/<?=$club["ID"]?>/" target="_blank"><?=$club["NAME"]?></a>
            </td>
            <th>
                <input type="checkbox" value="<?=$club['ID']?>" name="stok[]" class="right" <?if(in_array($club['ID'],$STOK)){?>checked="checked"<?}?>>
            </th>
            <th>
                <input type="checkbox" value="<?=$club['ID']?>" name="event[]" class="right"  <?if(in_array($club['ID'],$EVENT)){?>checked="checked"<?}?>>
            </th>
        </tr>

        <? endforeach;?>
    </table>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary right" name="save">Сохранить</button>
    </div>
    </form>

        <? else: ?>
    Вы не подписаны ни на один клуб.
    <?endif;?>

</div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("profile:menu.left", "", array(
        "USER_INFO"=>$userInfo
    ),
    false
);?>
</div>