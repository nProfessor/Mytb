<?
$APPLICATION->AddHeadScript("/jslibs/jquery/eTextTimer.js");
?>
<h4>Действующие акции «<?=$arResult['club']['NAME']?>» </h4>
<br/>
    <input type="hidden" id="time_now" value="<?=date("d F, Y, H:m:s")?>">
    <input type="hidden" id="clubID" value="<?=$arResult['club']['ID']?>">

<? if (count($arResult['stockList']) > 0): ?>
<div id="list">

        <?foreach ($arResult['stockList'] as $val=> $var): ?>
<div class="item">
        <?
        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        ?>

                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="stock_info"><?=$var["NAME"]?></a>
<div class="text-center">
    <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-left" style="margin:0px 10px 10px 0px">
        <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w"=> 240, "h"=> 120))?>"/>
    </a>
</div>





</div>

        <? endforeach;?>

</div>

<? else: ?>
На данный момент акций нет
<?endif; ?>
