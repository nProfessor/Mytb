<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="input-append">
    <form action="<?=$arResult["FORM_ACTION"]?>">
    <input class="span7" id="appendedInputButtons" size="16" type="text" placeholder="Найди заведение скидки и акции в котором тебе интересны" name="q"><button class="btn" type="submit"><?=GetMessage("BSF_T_SEARCH_BUTTON");?></button>
    </form>
</div>