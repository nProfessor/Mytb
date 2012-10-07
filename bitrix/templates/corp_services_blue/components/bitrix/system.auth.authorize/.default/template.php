<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();
?>
<div>
    <div class="fields">
        <?
        ShowMessage($arParams["~AUTH_RESULT"]);
        ShowMessage($arResult['ERROR_MESSAGE']);
        ?>

        <form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

            <input type="hidden" name="AUTH_FORM" value="Y"/>
            <input type="hidden" name="TYPE" value="AUTH"/>
            <?if (strlen($arResult["BACKURL"]) > 0): ?>
            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>"/>
            <? endif?>
            <?
            foreach ($arResult["POST"] as $key => $value) {
                ?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
                <?
            }
            ?>
            <div class="span4" style="width:246px !important;">
                <div class="field">
                    <label class="field-title"><?=GetMessage("AUTH_LOGIN")?></label>

                    <div class="input-prepend input-append">
                        <span class="add-on">@</span><input type="text" name="USER_LOGIN" maxlength="50"
                                                            placeholder="Email"
                                                            value="<?=$arResult["LAST_LOGIN"]?>"
                                                            class="input-field span3"/>
                    </div>
                </div>
                <div class="field">
                    <label class="field-title"><?=GetMessage("AUTH_PASSWORD")?></label>

                    <div>
                        <div class="input-prepend input-append">
                            <span class="add-on"><i class="icon-lock"></i></span><input type="password"
                                                                                        name="USER_PASSWORD"
                                                                                        placeholder="Пароль"
                                                                                        maxlength="50"
                                                                                        class="input-field span3"/>
                        </div>
                    </div>
                </div>


                    <input type="submit" name="Login" class="btn btn-primary pull-right" value="<?=GetMessage("AUTH_AUTHORIZE")?>"/>

            </div>
            <div class="span4">
                <?if ($arResult["AUTH_SERVICES"]): ?>
                <?
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                    array(
                         "AUTH_SERVICES"  => $arResult["AUTH_SERVICES"],
                         "CURRENT_SERVICE"=> $arResult["CURRENT_SERVICE"],
                         "AUTH_URL"       => $arResult["AUTH_URL"],
                         "POST"           => $arResult["POST"],
                    ),
                    $component,
                    array("HIDE_ICONS"=> "Y")
                );
                ?>
                <? endif?>
            </div>


        </form>
        <script type="text/javascript">
            <?
            if (strlen($arResult["LAST_LOGIN"]) > 0) {
                ?>
            try {
                document.form_auth.USER_PASSWORD.focus();
            } catch (e) {
            }
                <?
            } else {
                ?>
            try {
                document.form_auth.USER_LOGIN.focus();
            } catch (e) {
            }
                <?
            }
            ?>
        </script>

    </div>


</div>