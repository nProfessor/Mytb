<div class="button_subsribe_ok right"  data-id="<?=$arResult['CLUB_ID']?>" data-original-title="Вы можете моментально узнавать о появлении акций проводимых в  «<?=$arResult['NAME']?>»"><span>Ты уже подписан на</span><br/>«<?=$arResult['NAME']?>»</div>
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