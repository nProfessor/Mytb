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
                <strong>Описание события</strong><br/>
                <?=$arResult['stockInfo']['~DETAIL_TEXT']?></td>
        </tr>
</div>



