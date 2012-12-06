<?
$APPLICATION->SetTitle("Акции клуба  {$arResult['club']['NAME']}");
$APPLICATION->AddHeadScript("/jslibs/jquery/eTextTimer.js");
?>
<?
global $USER;
$auth = $USER->IsAuthorized();
?>
<h1>Действующие акции «<?=$arResult['club']['NAME']?>» </h1>
<br/>
<a href="#" class="btn btn-danger pull-right btn-large" id="subs_ok"
   data-original-title="Вы сможете моментально узнавать о появлении акций проводимых в  <b>«<?=str_replace('"', "", $arResult['club']['NAME'])?>»</b>"
   data-auth="<?if ($auth) {
       echo "yes";
   } else {
       echo "no";
   }?>">Подписаться на акции «<?=$arResult['club']['NAME']?>»</a>
<a href="/club/<?=$arResult['club']['ID']?>">На страницу «<?=$arResult['club']['NAME']?>»</a>
<br/>
<br/>
<br/>
<input type="hidden" id="time_now" value="<?=date("d F, Y, H:m:s")?>">
<input type="hidden" id="clubID" value="<?=$arResult['club']['ID']?>">

<? if (count($arResult['stockList']) > 0): ?>
<div id="list">
    <table class="table table-striped">
        <?foreach ($arResult['stockList'] as $val => $var): ?>

        <?
        $club = $arResult['club'][$stock["PROPERTY_CLUB_ID_VALUE"]];

        $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
        ?>
        <tr>
            <th colspan="2">
                <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="stock_info"><?=$var["NAME"]?></a>
            </th>
        </tr>
        <tr>
            <td>
                <div class="pull-left" style="margin:0px 10px 10px 0px">
                    <img class="thumbnail" src="<?=imgurl($arFile["SRC"], array("w" => 300, "h" => 200))?>"/>
                </div>

                <div class="time_stok pull-right">
                    <?if (0): ?>
                    <span class="clock">

                    </span>
                    <div class="pull-right" style="padding: 8px 0px 15px 0px;">
                        до конца акции:<br/>
                    <span class="timer" data-active="<?=date("d F, Y, H:m:s", strtotime($var["ACTIVE_TO"]))?>"
                          data-id="<?=$var["ID"]?>">
                    <span class="time<?=$var["ID"]?>d">88</span>д: <span class="time<?=$var["ID"]?>h">88</span>:<span
                            class="time<?=$var["ID"]?>m">88</span>:<span class="time<?=$var["ID"]?>s">88</span>
                    </span>
                    </div>
                    <? endif;?>

                    <?if ($auth): ?>
                    <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="btn btn-success pull-right btn-large"
                       target="_blank">купить купон
                        за <?=$var["PROPERTY_PRICECOUPON_VALUE"]?>р.</a>
                    <br/>
                    <br/>
                    <div class="pull-right">или</div>
                    <br/>
                    <a href="<?=$var["PROPERTY_URL_VALUE"]?>" class="pull-right">Узнать больше</a>
                    <? else: ?>
                    <a href="#"  href="#auth_stocks" data-toggle="modal"  data-target="#auth_stocks" aria-hidden="true" class="btn btn-success pull-right btn-large auth_stocks"
                       target="_blank">купить купон
                        за <?=$var["PROPERTY_PRICECOUPON_VALUE"]?>р.</a>
                    <br/>
                    <br/>
                    <div class="pull-right">или</div>
                    <br/>
                    <a href="#"  href="#auth_stocks" data-toggle="modal"  data-target="#auth_stocks" aria-hidden="true" class="pull-right btn-large auth_stocks">Узнать больше</a>
                    <?endif;?>


                </div>


                <p style="font-size: 12px;"> <?=$var["PREVIEW_TEXT"]?></p>


            </td>

        </tr>

        <? endforeach;?>
    </table>

    <div class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"></h3>
        </div>
        <div class="modal-body">
            <img src="" class="img-rounded" width="600px">

            <div id="modal-text"></div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
            <a href="" class="btn btn-primary" id="link_stock">Перейти к акции</a>
        </div>
    </div>
</div>
<? else: ?>
На данный момент акций нет
<?endif; ?>
<input type="hidden" id="club_name" value="<?=$arResult['club']['NAME']?>"/>
<?$clubIMG= CFile::GetFileArray($arResult['club']['PREVIEW_PICTURE']);?>
<input type="hidden" id="club_img" value="<?=imgurl($clubIMG["SRC"],array("w"=>200))?>"/>


<input id="redirect" type="hidden" value="/club/<?=$arResult['club']["ID"]?>/stock/?subscribe=ok">
<? $APPLICATION->IncludeComponent("mytb:auth", "", array("AUTH_URL" => "/club/{$arResult['club']["ID"]}/stock/?subscribe=ok&login=yes"), FALSE); ?>
<? $APPLICATION->IncludeComponent("mytb:auth", "stocks", array("AUTH_URL" => "/club/{$arResult['club']["ID"]}/stock/?auth=login"), FALSE); ?>