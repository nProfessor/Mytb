<div class="home_search page">
    <div class="fixed">
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
        <div class="baner_map">
            <object width="231" height="256" data="/i/home/f_72551c81248e51eb.swf" type="application/x-shockwave-flash">
                <param name="src" value="/i/home/f_72551c81248e51eb.swf" />
                <param name="pluginspage" value="http://www.macromedia.com/go/getflashplayer" />
                <param name="quality" value="best" />
                <param name="wmode" value="opaque" />
                <param name="flashvars" value="clickTAG=http://mytb.ru/page/map/" /></object>
            <a href="/page/map/" title="Все заведения с акциями на карте" alt="Все заведения с акциями на карте"></a>
        </div>

    </div>

</div>