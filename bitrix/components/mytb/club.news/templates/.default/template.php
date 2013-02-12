<?$type=is_array($arResult['club']['PROPERTY_TYPE_FACILITY_VALUE'])?implode("/",$arResult['club']['PROPERTY_TYPE_FACILITY_VALUE']):$arResult['club']['PROPERTY_TYPE_FACILITY_VALUE'];


$arFile = CFile::GetFileArray($arResult['stockInfo']["DETAIL_PICTURE"]);

?>
<div class="stock">
    <h2><?=$arResult['news']['NAME']?></h2>

    <div class="m_left  w10">
    <table class="table">
        <tr>
            <th class="w1">Заведение</th>
            <td>«<a href="/club/<?=$arResult['club']['ID']?>/#news" title="<?=$type?> <?=$arResult['club']['NAME']?>"><?=$type?> <?=$arResult['club']['NAME']?></a>»</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th class="w1">Дата</th>
            <td><?=date("d.m.Y",strtotime($arResult['news']['ACTIVE_FROM']))?></td>
            <th class="w1">Поделиться</th>
            <td><script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
                <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"
                     data-yashareQuickServices="yaru,vkontakte,facebook,twitter,moimir,lj,gplus"></div></td>
        </tr>
    </table>
    </div>
    <div class="clear_both"></div>

    <?if(!empty($arFile["SRC"])):?>
                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300, "h" => 200))?>"/>
                </div>
<?endif;?>
                <?=str_replace("\n","<br/>",$arResult['news']['~DETAIL_TEXT'])?></td>
        </tr>
</div>



