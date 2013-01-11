<div class="filter">
    Или подбери заведение по вкусу

    <div class="clear_left"></div>
    <form action="/search/filter/">
    <div class="filter_block">

        <div class="item-filter pull-right">
            <span>Музыка:</span>

            <div>
                <select name="MUSIC">
                    <option value="">Все</option>
                    <?foreach ($arResult["MUSIC_LIST"] as $val => $var): ?>
                    <option value="<?=$var['ID']?>" <?if($arResult["FILTER"]['MUSIC']==$var['ID']){?>selected="selected"<?}?>><?=$var['VALUE']?></option>
                    <? endforeach;?>
                </select>
            </div>
        </div>

        <div class="item-filter">
            <span>Тип:</span>

            <div>
                <select name="KIND_CLUB">
                    <option value="">Все</option>
                    <?foreach ($arResult["KIND_CLUB_LIST"] as $val => $var): ?>
                    <option value="<?=$var['ID']?>"><?=$var['VALUE']?></option>
                    <? endforeach;?>
                </select>
            </div>
        </div>

        <div class="item-filter pull-right">

            <span>Наличие событий:</span>

            <div>
                <select name="EVENT">
                    <option value="">Все</option>
                    <option value="1">Есть</option>
                    <option value="0">Нет</option>
                </select>
            </div>
        </div>
        <div class="item-filter">

            <span>Наличие акций:</span>

            <div>
                <select name="STOCKS">
                    <option value="">Все</option>
                    <option value="1">Есть</option>
                    <option value="0">Нет</option>
                </select>
            </div>
        </div>


        <!--    <div class="item-filter">-->
        <!--        <span>Метро:</span>-->
        <!---->
        <!--        <div>-->
        <!--            <select name="" id="">-->
        <!--                <option>Бар</option>-->
        <!--                <option>Ресторан</option>-->
        <!--            </select>-->
        <!--        </div>-->
        <!--    </div>-->
    </div>
    <div class="clear_left"></div>
    <input type="submit" value="Подобрать" class="button"/>
    </form>
</div>