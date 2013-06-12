<div id="breadcrumb">
    <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
        "START_FROM" => "1",
        "PATH" => "",
        "SITE_ID" => SITE_ID
    ),
    false
);?>

</div>
<ul class="article_items">
    <?foreach ($arResult["ARTICLE"] as $article): ?>
    <li class="article_item">
        <?$arFile = CFile::GetFileArray($article["PREVIEW_PICTURE"]);?>
        <a href="/article/<?=$article['SECTION']['CODE']?>/<?=$article['ID']?>" title="<?=$article['NAME']?>"><?=$article['NAME']?></a>
        <a href="/article/<?=$article['SECTION']['CODE']?>/<?=$article['ID']?>" title="<?=$article['NAME']?>"><img src="<?=imgurl($arFile['SRC'], array("w" => 300))?>" alt="<?=$article['NAME']?>"></a>
        <p><?=$article['PREVIEW_TEXT']?></p>
    </li>
    <? endforeach;?>
</ul>

