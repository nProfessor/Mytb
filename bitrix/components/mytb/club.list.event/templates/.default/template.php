    <?if(count($arResult['eventList'])>0):?>
    <table class="table table-striped">
        <?foreach ($arResult['eventList'] as $val => $var): ?>
            <?
            $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
            ?>
            <tr>
                <td>
                    <div class="pull-left" style="margin:0px 10px 10px 0px">
                        <a href="/event/<?=$var["ID"]?>/" class="stock_title">
                        <img class="thumbnail" alt="Событие в <?=$arResult['club']['NAME']?>" title="<?=$var["NAME"]?>" src="<?=imgurl($arFile["SRC"], array("w" => 300))?>"/>
                        </a>
                    </div>
                    <a href="/event/<?=$var["ID"]?>/" class="stock_title"><?=$var["NAME"]?></a>
                    <div class="stock_info">
                        <p style="font-size: 12px;"> <?=str_replace("\n","<br/>",$var["DETAIL_TEXT"])?></p>
                    </div>
                </td>
            </tr>
        <? endforeach;?>
    </table>

<?else:?>
<noindex>
    На данный момент событий нет
</noindex>
<?endif;?>