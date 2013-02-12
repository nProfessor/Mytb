<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="input-append">
    <form action="<?=$arResult["FORM_ACTION"]?>">
    <input class="span7" id="appendedInputButtons" size="16" type="text" placeholder="Текст для поиска..." name="q"><button class="btn" type="submit"><?=GetMessage("BSF_T_SEARCH_BUTTON");?></button>
    </form>
</div>

<!--	<div class="flat">-->
<!---->
<!--	<div id="search-button">-->
<!--    <input type="submit" name="s" id="search-submit-button" value="--><?//=GetMessage("BSF_T_SEARCH_BUTTON");?><!--" onfocus="this.blur();">-->
<!--</div>-->
<!--<div class="search-box"><input type="text" name="q"></div>-->
<!---->
<!--</div>-->