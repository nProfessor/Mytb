<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 07.09.12
 * Time: 18:17
 * To change this template use File | Settings | File Templates.
 */


?>


<div class="<?=$StyleText?>">
    <ul>
        <li class="disabled">
            <?if ($this->NavPageNomer > 1): ?>
            <a
                href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum?>=1<?=$strNavQueryString?>#nav_start<?=$add_anchor?>">«</a>
            <? else: ?>
            <a href="#">«</a>
            <? endif;?>
        </li>



        <?
        $NavRecordGroup = $nStartPage;
        while ($NavRecordGroup <= $nEndPage) {
            if ($NavRecordGroup == $this->NavPageNomer):?>
                <li class="active">
                    <a href="#"><?=$NavRecordGroup?></a>
                </li>
                <? else: ?>
                <li class="disabled">
                    <a href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum ?>=<?=
                        $NavRecordGroup . $strNavQueryString ?>#nav_start<?=$add_anchor?>"><?=$NavRecordGroup?></a>
                </li>
                <?endif;
            $NavRecordGroup++;
        }
        ?>

        <li class="disabled">
            <?if ($this->NavPageNomer < $this->NavPageCount): ?>
            <a href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum?>=<?=
                $this->NavPageCount . $strNavQueryString?>#nav_start<?=$add_anchor?>">»</a>
            <? else: ?>
            <a href="#">»</a>
            <? endif;?>
        </li>

    </ul>
</div>
