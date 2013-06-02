<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="search_top">
    <form action="<?=$arResult["FORM_ACTION"]?>">
    <input id="appendedInputButtons" size="16" type="text" value="<?$arParams["REQWEST"]?>" placeholder="Текст для поиска..." name="q"><button class="button orange" style="width:124px" type="submit"><?=GetMessage("BSF_T_SEARCH_BUTTON");?></button>
    </form>
</div>
<?
$url=$APPLICATION->GetCurUri();

$url=preg_replace("#(/)$#i","",$url);

if($url!="/page/map"):?>
<a href="/page/map" class="baner_top_map" title="Все заведения с акциями и скидками на карте"><img src="/images/baners/baner_map_search.jpg"/></a>
<?endif;?>

