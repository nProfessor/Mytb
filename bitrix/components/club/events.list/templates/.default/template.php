<?
$clubInfo=$arResult['club'];

?>
<div class="right w8">
    <div  style="padding-left:10px;">
    <form class="input-append" action="" method="post">
        <input type="text" value="" class="w6" name="NAME" placeholder="Название события">
        <button class='button'>Добавить событие</button>
    </form>
        <br/>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="/personal/club/list-events/">Действующие события</a></li>
            <li class=""> <a href="/personal/club/list-events/archive/">Архив</a></li>
        </ul>

<? if (count($arResult['arList']) > 0): ?>

<div id="list">
    <table class="table table-striped">
        <tr>
            <th width="100px">
               Дата начала
            </th>
            <th>
               Название события
            </th>
            <td></td>
        </tr>
        <?foreach ($arResult['arList'] as $val => $var): ?>
        <tr>
            <td>
                <?=date("d.m.Y",strtotime($var["ACTIVE_FROM"]))?>
            </td>
            <td>
                <a href="/personal/club/event/<?=$var["ID"]?>"><?=$var["NAME"]?></a>
            </td>
            <td width="150px">
<!--               <a hfer="#" data-id="--><?//=$var["ID"]?><!--"  title="--><?//=$var['ACTIVE']=="N"?"Акция не актива. Нажмите чтобы активировать.":"Акция актива. Нажмите чтобы отключить.";?><!--" class="active_button --><?//=$var['ACTIVE']?><!--"></a>-->
                <?if($var["PROPERTY_PUBLIC_ENUM_ID"]!=PROP_EVENT_PUBLIC):?>
                <a hfer="#" data-id="<?=$var["ID"]?>" class="btn public"><span></span>Опубликовать</a>
                <?endif;?>
            </td>

        </tr>

        <? endforeach;?>
    </table>
    <?=$arResult["NAV_STRING"]?>
</div>
<? else: ?>
<noindex>
    На данный момент событий нет
</noindex>
<?endif; ?>
    </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("club:menu.left", "", array(
        "CLUB_ID" => $clubInfo['ID'],
    ),
    false
);?>
</div>