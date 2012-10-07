<h2><?=$arResult['arTable']['NAME']?></h2>

<table>
    <tr>
        <td width="200px">
            <img src="<?=$arResult['arTable']['PREVIEW_PICTURE']?>" width="300px">

        </td>
        <td>
            <table>
                <tr>
                    <td>Название</td>
                    <td><input type="text" VALUE="<?=$arResult['arTable']['NAME']?>" ></td>
                </tr>
                <tr>
                    <td>Ценовая группа</td>
                    <td><select name="group_price" id="group_price">
                        <options>8888</options>
                    </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>