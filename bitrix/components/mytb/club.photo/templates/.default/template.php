<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/fancybox/jquery.fancybox-1.2.1.pack.js");
$APPLICATION->SetAdditionalCSS("/jslibs/fancybox/jquery.fancybox.css");


$clubID = $arResult['clubID'];
$clubName = $arResult['clubName'];

?>

<? if (count($arResult['photo']) > 0): ?>
<ul class="gallery_100x100">
    <?$i=1;?>
    <?foreach ($arResult['photo'] as $var): ?>
    <li>
        <a  class="gallery" rel="group"  title="Фото <?=$clubName?> №<?=$i?>" href="<?=imgurl($var["PATH"], array("w" => 800, "h" => 600))?>">
        <img src="<?=imgurl($var["PATH"], array("w" => 100, "h" => 100))?>" alt="Фотография <?=$clubName?> №<?=$i?>" title="фото <?=$clubName?>  №<?=$i?>"/>
        </a>
    </li>
    <?$i++;?>
    <? endforeach;?>
</ul>
<? else: ?>
<noindex>
    На данный момент нет фото и видео
</noindex>
<?endif; ?>
<div class="clear_both"></div>
