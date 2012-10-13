<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

?>
<?if($arParams["POPUP"]):?>
<div style="display:none">
<div id="bx_auth_float" class="bx-auth-float">
<?endif?>

<?if($arParams["~CURRENT_SERVICE"] <> ''):?>
<script type="text/javascript">
BX.ready(function(){BxShowAuthService('<?=CUtil::JSEscape($arParams["~CURRENT_SERVICE"])?>', '<?=$arParams["~SUFFIX"]?>')});
</script>
<?endif?>


	<form method="post" name="bx_auth_services<?=$arParams["SUFFIX"]?>" target="_top" action="<?=$arParams["AUTH_URL"]?>">
		<div class="bx-auth-title">Социальные сети</div>
        <div class="bx-auth">
		<div class="bx-auth-service-form" id="bx_auth_serv<?=$arParams["SUFFIX"]?>" >
<?foreach($arParams["~AUTH_SERVICES"] as $service):?>
			<div id="bx_auth_serv_<?=$arParams["SUFFIX"]?><?=$service["ID"]?>" ><?=$service["FORM_HTML"]?></div>
<?endforeach?>
		</div>
<?foreach($arParams["~POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
		<input type="hidden" name="auth_service_id" value="" />
        </div>
	</form>


<?if($arParams["POPUP"]):?>
</div>
</div>
<?endif?>