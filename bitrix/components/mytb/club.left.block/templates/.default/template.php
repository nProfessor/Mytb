<style type="text/css">
    .list_right_block_club .header {
        padding: 5px;
        border-radius: 3px;
        background: #eee;
        width: 200px;
        margin: 10px 0px;
        font-size: 16px;
        font-weight: bold;
    }

    .list_right_block_club {
        width: 200px;
        margin-bottom: 30px;
    }

    .content_stock {
        border-bottom: 1px dashed #eee;
        margin-bottom: 10px;;
    }
</style>

<?
global $USER;
$auth = $USER->IsAuthorized();
?>
<h2><?=$arResult['CLUB']['NAME']?></h2>
    <?if(0):?>
<? if ($arResult["LIST"]['event']['show']): ?>
<div class="list_right_block_club">
    <div class="header">Предстоящие события</div>
    <?foreach ($arResult["LIST"]['event']['list'] as $var): ?>
    <div>
        <?$arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);?>
        <a href="/club/<?=$arResult["CLUB_ID"]?>/event/<?=$var["ID"]?>" alt="<?=$var["NAME"]?>"
           title="<?=$var["NAME"]?>">
            <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 200))?>"/>
        </a>
    </div>
    <? endforeach;?>
</div>
<? endif; ?>
<? endif; ?>

<? if ($arResult["LIST"]['stocks']['show']): ?>
<div class="list_right_block_club">
    <div class="header">Действующие акции</div>
    <?foreach ($arResult["LIST"]['stocks']['list'] as $var): ?>
    <div class="content_stock">
        <?$arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);?>
        <?if ($auth): ?>
        <a href="<?=$var['PROPERTY_URL_VALUE']?>" target="_blank" alt="Акция <?=$arResult['CLUB']['NAME']?>" title="<?=$var['NAME']?>">
            <? else: ?>
        <a href="#auth_stocks" class="auth_stocks"  data-toggle="modal"  data-target="#auth_stocks" aria-hidden="true" alt="Акция <?=$arResult['CLUB']['NAME']?>" title="<?=$var['NAME']?>">
        <?endif;?>
        <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 200))?>"/>
    </a>

        <div style="font-size: 11px; width: 200px; padding: 5px;"><?=$var['NAME']?></div>
    </div>
    <? endforeach;?>
</div>


<input type="hidden" id="club_name" value="<?=trim($arResult['CLUB']['NAME'])?>"/>
<?$clubIMG= CFile::GetFileArray($arResult['CLUB']['PREVIEW_PICTURE']);?>
<input type="hidden" id="club_img" value="<?=imgurl($clubIMG["SRC"],array("w"=>200))?>"/>

<? $APPLICATION->IncludeComponent("mytb:auth", "stocks",  array("AUTH_URL"=>"/club/{$arResult["CLUB_ID"]}/?auth=login"), FALSE); ?>
<?else:?>
<?$APPLICATION->IncludeFile(
        SITE_DIR."include/vk.php",
        Array(),
        Array("MODE"=>"html")
    );?>
<? endif; ?>