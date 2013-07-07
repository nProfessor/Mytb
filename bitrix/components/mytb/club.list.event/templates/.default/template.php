    <?if(count($arResult['eventList'])>0):?>
    <table class="table table-striped">
        <?foreach ($arResult['eventList'] as $val => $var): ?>
            <?
            $arFile = CFile::GetFileArray($var["DETAIL_PICTURE"]);
            ?>
            <tr>
                <td>
                    <div class="pull-left" style="margin:0px 10px 10px 0px">
                        <a itemprop="event" href="/club/event/<?=$var["ID"]?>/" class="stock_title" title="<?=$arResult['club']['NAME']?>: <?=$var['NAME']?>">
                        <img class="thumbnail" alt="Событие в <?=$arResult['club']['NAME']?>" title="<?=$var["NAME"]?>" src="<?=imgurl($arFile["SRC"], array("w" => 300))?>"/>
                        </a>
                    </div>
                    <a href="/club/event/<?=$var["ID"]?>/" class="stock_title"  title="<?=$arResult['club']['NAME']?>: <?=$var['NAME']?>"><?=$var["NAME"]?></a>
                    <div class="stock_info">
                        <strong>Дата события: с <?=date("d.m.Y",strtotime($var['ACTIVE_FROM']))?> по <?=date("d.m.Y",strtotime($var['ACTIVE_TO']))?>
                        </strong>
                        <br/>
                        <br/>
                        <p style="font-size: 12px;"> <?=cut_string($var["DETAIL_TEXT"],400)?></p>
                        <a href="/club/event/<?=$var["ID"]?>/" class="button right"  title="<?=$arResult['club']['NAME']?>: <?=$var['NAME']?>">Подробней</a>
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