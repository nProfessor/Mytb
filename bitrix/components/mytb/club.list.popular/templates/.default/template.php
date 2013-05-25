<center>
<h1><?=count($arResult["CLUB_LIST"])?> самых популярных заведений</h1>
</center>

    <?$i=0;?>
    <?foreach($arResult["CLUB_LIST"] as $var):?>
    <?
        $i++;
    $rating=intval($var["PROPERTY_RATING_VALUE"]);

    $File=CFile::GetFileArray($var["PREVIEW_PICTURE"]);
    $var["PREVIEW_PICTURE"]=imgurl($File['SRC'], array("w" => 200,"h"=>200));
    ?>
<div class="club_popular_list_item img-polaroid">
        <div>
            <div class="img-polaroid-club">
                <a href='/club/<?=$var["ID"]?>' title="<?=html_entity_decode($var["NAME"])?>"
                   style="background: #fff url(<?=$var["PREVIEW_PICTURE"]?>) no-repeat center center">
                </a>
            </div>



            <div class="info-popular-club">
                <strong>
                    <a href='/club/<?=$var["ID"]?>'><?=html_entity_decode($var["NAME"])?></a>
                </strong>
                <table>
                    <tr>
                        <td>Место в рейтинге:</td>
                        <td><?=$i?></td>
                    </tr>

                    <tr>
                        <td>Лояльность:</td>
                        <td><?=$rating?> <?=declOfNum($rating,array("голос","голоса","голосов"))?></td>
                    </tr>

                    <tr>
                        <td>Подписчиков:</td>
                        <td><?=$var['SUBS']?> <?=declOfNum($var['SUBS'],array("человек","человека","человек"))?></td>
                    </tr>

                </table>


            </div>

        </div>
</div>
    <? endforeach; ?>




    <div class="clear_both"></div>