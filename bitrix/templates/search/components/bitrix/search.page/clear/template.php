<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="search-page">
	<form action="" method="get">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr>
				<td style="width: 100%;">
                    <div class="input-append">
                        <input class="span11" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" /><button class="button orange span2" type="submit"><?echo GetMessage("CT_BSP_GO")?>!</button>
                    </div>

				</td>
			</tr>
		</tbody>
        </table>
	</form>


	<div class="search-result">
	<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
	<?elseif($arResult["ERROR_CODE"]!=0):?>
		<p>Ничего не найдено</p>

	<?elseif(count($arResult["SEARCH"])>0):?>

		<?foreach($arResult["SEARCH"] as $arItem):?>
			<div class="search-item">
				<h4><a href="<?echo $arItem["URL"]?>"><?echo $arItem["TITLE_FORMATED"]?></a></h4>
				<div class="search-preview"><?echo substr(strip_tags($arItem["~BODY"]),0,300)?></div>
			</div>
		<?endforeach;?>
        <?if($arParams["DISPLAY_TOP_PAGER"] != "N") echo $arResult["NAV_STRING"]?>
	<?else:?>
		<?ShowNote(GetMessage("CT_BSP_NOTHING_TO_FOUND"));?>
	<?endif;?>

	</div>
</div>