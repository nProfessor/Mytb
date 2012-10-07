<style>
    .table
    {
        position: absolute;
        background: red;
        opacity: 0.4;
        border-radius: 3px;
    }
</style>
<input type="hidden" value="0" id="X">
<input type="hidden" value="0" id="Y">
<input type="hidden" value="0" id="W">
<input type="hidden" value="0" id="H">

<table width="100%">
    <tr>
        <td width="30%" valign="top">
            <div>Список столиков</div>
            <button id="add-table">Создать столик</button>
            <ul><?foreach($arParams['arFields'] as $var):?>
                <li><?=$var["ID"]?> <?=$var["NAME"]?></li>
                    <?endforeach;?>
            </ul>
        </td>
        <td>
            <div style="position: relative;" id="plan_div">
            <img src="/upload/plan/tropikana1.jpg" id="plan_club"/>
            </div></td>
    </tr>
</table>
    <div title="Создание столика" style="display: none;" id="dialog">
        <div>Название</div>
        <div><input type="text" name="name" id="name-table" size="35"></div>
        <div>Описание</div>
        <div><textarea rows="10" cols="35" id="text" ></textarea></div>
    </div>