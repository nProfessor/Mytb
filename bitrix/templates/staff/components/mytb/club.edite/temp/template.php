<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");
$APPLICATION->AddHeadScript("/jslibs/jquery.imgareaselect-0.9.8/scripts/jquery.imgareaselect.min.js");
$APPLICATION->SetAdditionalCSS("/jslibs/jquery.imgareaselect-0.9.8/css/imgareaselect-animated.css");
$APPLICATION->SetAdditionalCSS("/jslibs/jqueryui/css/ui-lightness/jquery-ui-1.8.21.custom.css");
?>


<input type="hidden" value="0" id="X">
<input type="hidden" value="0" id="Y">
<input type="hidden" value="0" id="W">
<input type="hidden" value="0" id="H">
Перейти к странице клуба <a href="/club/<?=$arResult['arFields']["ID"]?>/"><?=$arResult['arFields']["NAME"]?></a>

<div style="position: relative;" id="plan_div">
    <img src="<?=$arResult['arFields']["PROPERTY_PLAN_VALUE"]?>" id="plan_club" width="900px"/>
</div>
<div class="form-actions">
    <button id="add-table" class="btn btn-primary">Создать столик</button>
</div>
<br/>
<h3>Список столиков</h3>
<div>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Фото</th>
            <th>Название</th>
            <th>Стоимость</th>
            <th width="50px">Людей</th>
            <th></th>
            <th width="130px"></th>
        </tr>
        <?foreach ($arResult['arTableList'] as $table): ?>
        <tr id="table_ID_<?=$table["ID"]?>">
            <td width="165px" align="center"><img src="<?=$table["PREVIEW_PICTURE"]?>" width="160px"/></td>
            <td class="table_name">
                <?=$table["NAME"]?>
            </td>
            <td class="table_group">
                <?=empty($arResult['arGroupPrice'][$table["PROPERTY_PRICE_GROUP_VALUE"]]) ? "Нет группы" : $arResult['arGroupPrice'][$table["PROPERTY_PRICE_GROUP_VALUE"]] . " руб.";?>
            </td>
            <td class="table_count">
                <?=$table["PROPERTY_COUNT_VALUE"]?>
            </td>
            <td>
            </td>
            <td>
                <a href="#" class="btn btn-primary btn-small table_edit" data-id="<?=$table["ID"]?>"><i
                    class="icon-pencil icon-white"></i> Редактировать</a><br/><br/><br/>
                <a href="#" class="btn btn-danger btn-small table_delete" data-id="<?=$table["ID"]?>"><i
                    class="icon-trash icon-white"></i> Удалить</a><br/>
            </td>
        </tr>
        <? endforeach;?>
    </table>

</div>
<div title="Создание столика" style="display: none;" id="dialog">
    <table>
        <tr>
            <td>Номер</td>
            <td><input type="text" name="number" id="number-table" size="12"/></td>
        </tr>
        <tr>
            <td>Количество человек</td>
            <td><input type="text" name="count" id="count-persone"/></td>
        </tr>
        <td>Ценовая группа</td>
        <td>
            <select id="price_group" class="price_group" style="width: 347px;padding:2px">
                <?foreach ($arResult['arGroupPrice'] as $val => $var): ?>
                <option value="<?=$val?>"><?=$var?></option>
                <? endforeach;?>
            </select>
        </td>
        </tr>
    </table>
</div>


<div class="modal hide"  id="dialog_table_edit">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Редактирование столика</h3>
    </div>
    <div class="modal-body">
        <input type="hidden" value="" id="table_id">
        <table>
            <tr>
                <td></td>
                <td>
                    <table>
                        <tr>
                            <td>Номер</td>
                            <td><input type="text" id="number-table-edite" size="12"/></td>
                        </tr>
                        <tr>
                            <td>Количество человек</td>
                            <td><input type="text" name="count" id="count-persone-edite"/></td>
                        </tr>
                        <td>Ценовая группа</td>
                        <td>
                            <select id="price_group-edite" class="price_group" style="width: 347px;padding:2px">
                                <?foreach ($arResult['arGroupPrice'] as $val => $var): ?>
                                <option value="<?=$val?>"><?=$var?></option>
                                <? endforeach;?>
                            </select>
                        </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Отмена</a>
        <a href="#" class="btn btn-primary" id="table_save_info">Сохранить</a>
    </div>
</div>

