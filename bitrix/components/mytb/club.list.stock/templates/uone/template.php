<h1>Действующие акции «<?=trim($arResult['club']['NAME'])?>»</h1>
   <br/>
<?
$APPLICATION->IncludeComponent("mytb:subscribe.button",
    "",
    array(
        "CLUB_ID"=> intval($arResult['club']["ID"]),
        "CLUB_NAME"=> $arResult['club']['NAME'],
    ), false);
?>


<div class="m_left w5">
    <table class="table">
        <tr>
            <th>Заведение</th>
            <td>«<a href="/club/<?=$arResult['club']['ID']?>/" title="<?=$type?> <?=$arResult['club']['NAME']?>"><?=$type?> <?=$arResult['club']['NAME']?></a>»</td>
        </tr>
        <tr>
            <th>Поделиться</th>
            <td><script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
                <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"
                     data-yashareQuickServices="yaru,vkontakte,facebook,twitter,moimir,lj,gplus"></div></td>
        </tr>
    </table>
</div>
    <div class="clear_both"></div>
<br/>
<? if (count($arResult['stockList']) > 0): ?>
<div id="list">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val => $var): ?>

        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];

        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        $partner = Kupon::getDataServise($var['TAGS']);
        ?>
        <tr>
            <td>
                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300))?>"/>
                </div>


                <a href="/club/stock/<?=$var["ID"]?>" class="stock_title"  title="<?=$partner['name']?>: <?=$var["NAME"]?>"><?=$var["NAME"]?></a>
                <div class="stock_info">
                    <strong>Скидка представлена на сайте <?=$partner['name']?></strong><br/>
                    <p style="font-size: 12px;"> <?=str_replace("\n","<br/>",cut_string($var["PREVIEW_TEXT"],400))?></p>
                    <a href="/club/stock/<?=$var["ID"]?>" class="button right" title="<?=$partner['name']?>: <?=$var["NAME"]?>">Подробней</a>
                </div>

            </td>

        </tr>

        <? endforeach;?>
    </table>

</div>
<? else: ?>
<noindex>
    На данный момент акций нет
</noindex>
<?endif; ?>