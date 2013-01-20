<div class="home_search page">
    <div class="filter">
        <div class="clear_left"></div>
        <form action="/search/filter/">
            <div class="filter_block">
                <div class="item-filter">
                    <span>Музыка:</span>

                    <div>
                        <select name="MUSIC">
                            <option value=""> </option>
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
                            <option value=""> </option>
                            <?foreach ($arResult["KIND_CLUB_LIST"] as $val => $var): ?>
                            <option value="<?=$var['ID']?>" <?if($arResult["FILTER"]['KIND_CLUB']==$var['ID']){?>selected="selected"<?}?>><?=$var['VALUE']?></option>
                            <? endforeach;?>
                        </select>
                    </div>
                </div>

                <div class="item-filter">

                    <span>Наличие событий:</span>

                    <div>
                        <select name="EVENT">
                            <option value=""> </option>
                            <option value="true" <?if($arResult["FILTER"]['EVENT']=="true"){?>selected="selected"<?}?>>Есть</option>
                            <option value="false" <?if($arResult["FILTER"]['EVENT']=="false"){?>selected="selected"<?}?>>Нет</option>
                        </select>
                    </div>
                </div>
                <div class="item-filter">

                    <span>Наличие акций:</span>

                    <div>
                        <select name="STOCKS">
                            <option value=""> </option>
                            <option value="true" <?if($arResult["FILTER"]['STOCKS']=="true"){?>selected="selected"<?}?>>Есть</option>
                            <option value="false" <?if($arResult["FILTER"]['STOCKS']=="false"){?>selected="selected"<?}?>>Нет</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clear_left"></div>
            <br/>
            <input type="submit" value="Подобрать" class="button"/>
        </form>
    </div>
</div>