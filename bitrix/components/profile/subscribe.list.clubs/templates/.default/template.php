Я подписан:

<? if (count($arResult['club']) > 0): ?>
<div class="list_clubs">
    <?foreach ($arResult['club'] as $val => $var): ?>
    <div class="item_content">
        <div data-id="<?=$var["ID"]?>" title="<?=$var["NAME"]?>"
             style="background:#fff url(<?=$var["PREVIEW_PICTURE"]?>) no-repeat center center" class="item">

        </div>
        <div class="clear_both"></div>
    </div>
    <? endforeach;?>
</div>
<div class="clear_both">
    <br/>
    Акции и события <b class="name_club">Все</b>
</div>


<? else: ?>
    <div>
        Вы пока не подписались ни на одно заведение. Найдите то место, которое вам нравится:
        <br>
        <br>

        <?$APPLICATION->IncludeComponent("bitrix:search.form", "small", array(
            "PAGE" => "#SITE_DIR#search/index.php"
        ),
        FALSE
    );?>
    </div>
<?endif; ?>

