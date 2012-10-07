<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul id="top-menu">
<?foreach($arResult as $arItem):?>
	<?if($arItem["SELECTED"]):?>
		<li class="selected"><b><?=$arItem["TEXT"]?></b></li>
	<?else:?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	<?endif?>
	
<?endforeach?>
</ul>		
<?endif?>