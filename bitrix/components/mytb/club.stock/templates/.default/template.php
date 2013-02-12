<?$type=is_array($arResult['club']['PROPERTY_TYPE_FACILITY_VALUE'])?implode("/",$arResult['club']['PROPERTY_TYPE_FACILITY_VALUE']):$arResult['club']['PROPERTY_TYPE_FACILITY_VALUE'];


$arFile = CFile::GetFileArray($arResult['stockInfo']["DETAIL_PICTURE"]);
$partner = Kupon::getDataServise($arResult['stockInfo']['TAGS']);
?>
<div class="stock">
    <h2><?=$arResult['stockInfo']['NAME']?></h2>

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
            <th>Даты проведения</th>
            <td>с <?=date("d.m.Y",strtotime($arResult['stockInfo']['ACTIVE_FROM']))?> по <?=date("d.m.Y",strtotime($arResult['stockInfo']['ACTIVE_TO']))?></td>
        </tr>
        <tr>
            <th>Партнер акции</th>
            <td><?=$partner['name']?></td>
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

                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300, "h" => 200))?>"/>
                </div>
                <strong>Описание акции</strong><br/>
                <?=str_replace("\n","<br/>",$arResult['stockInfo']['~PREVIEW_TEXT'])?></td>
        </tr>
</div>
    <div class="clear_both link_cupon">
Для покупки купона на скидку и получения полной информации об акции необходимо <strong><span data-link='<?=$arResult['stockInfo']['PROPERTY_URL_VALUE']?>' class="linc_coupon">перейти на сайт партнера <?=$partner['site']?></span></strong>
    </div>



