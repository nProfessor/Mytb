<div class="button_subsribe right"  data-id="<?=$arResult['CLUB_ID']?>" data-original-title="Вы сможете моментально узнавать о появлении акций проводимых в  «<?=$arResult['NAME']?>»"><span>Подписаться на акции</span><br/>«<?=$arResult['NAME']?>»</div>
<?if(!$USER->IsAuthorized()):?>
<noindex>
<?
$APPLICATION->IncludeComponent(
        "mytb:auth",
        "subscribe",
        Array(
            "CLUB_ID"  => $clubInfo['ID'],
        )
);
?>
</noindex>
<?endif;?>