<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 23.06.12
 * Time: 18:57
 * To change this template use File | Settings | File Templates.
 */
$clubInfo=$arParams['arFields'];
?>
<h1>Редактируем информацию о клубе</h1>
<table>
    <tr>
        <th>Название</th>
        <td><input type="text" value="<?=$clubInfo["NAME"]?>" size="60" name="NAME"></td>
    </tr>
    <tr>
        <th>
            Часы работы
        </th>
        <td><input type="text" value="<?=$clubInfo["PROPS"]["TIME_WORKING"]['VALUE']?>" size="60" name="TIME_WORKING"></td>
    </tr>
    <tr>
        <th>
            Цена коктеля
        </th>
        <td><input type="text" value="<?=$clubInfo["PROPS"]["PRICE_COCKTAIL"]['VALUE']?>" size="60" name="PRICE_COCKTAIL"></td>
    </tr>
    <tr>
        <th>
            Телефон
        </th>
        <td>

            <? foreach ($clubInfo["PROPS"]["PHONE"]['VALUE'] as $phone): ?>
           <input type="text" value="<?=$phone?>" size="60" name="PHONE">
                <? endforeach; ?>
        </td>
    </tr>
    <tr>
        <th>
            Музыка
        </th>
        <td>
            <?if (count($clubInfo["PROPS"]["MUSIC"]['VALUE'])): ?>
            <?= implode(", ", $clubInfo["PROPS"]["MUSIC"]['VALUE']) ?>
            <? else: ?>
            нет информации
            <?endif;?>
        </td>
    </tr>
    <tr>
        <th>
            Фейсконтроль
        </th>
        <td>
            <?=empty($clubInfo["PROPS"]["FACE_CONTROL"]['VALUE']) ? "нет информации" : $clubInfo["PROPS"]["FACE_CONTROL"]['VALUE'];?>
        </td>
    </tr>
    <tr>
        <th>
            Дресс-код
        </th>
        <td>
            <?=empty($clubInfo["PROPS"]["DRESS_CODE"]['VALUE']) ? "нет информации" : $clubInfo["PROPS"]["DRESS_CODE"]['VALUE'];?>
        </td>
    </tr>
</table>
</table>  