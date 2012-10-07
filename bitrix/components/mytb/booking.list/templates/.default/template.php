<?

$APPLICATION->AddHeadScript("/jslibs/jquery/jquery.dataTables.js");
$bookingList = $arResult["BookingList"];
$userList = $arResult["UsersList"];
$arTableList = $arResult["TableList"];

$date = $arResult["date"];

?>
<div class="manager">
    <div id='datepicker'>
    </div>
</div>
    <br/>
        <div id="table_list_booling">
<table class="table table-bordered table-striped" id="example">
    <thead>
    <tr>
        <th width="50px">Код брони</th>
        <th>Столик</th>
        <th>Пользователь</th>
        <th>Время визита</th>
        <th>Телефон</th>
        <th>Тип брони</th>
    </tr>
    </thead>
    <tbody>
    <?foreach ($bookingList as $var): ?>
        <? $user = $userList[$var["PROPERTY_USER_VALUE"]] ?>

    <tr>
        <td><?=$var["ID"]?></td>
        <td>
            <?=$arTableList[$var["PROPERTY_TABLE_VALUE"]]['NAME']?></td>
        <td><?=$var["PROPERTY_FIO_VALUE"]?></td>
        <td><?=$var["PROPERTY_TIME_VALUE"]?></td>
        <td><?=$var["PROPERTY_PHONE_VALUE"]?></br></td>
        <td><?=$var["PROPERTY_TYPE_VALUE"]?></td>
    </tr>
        <? endforeach;?>
    </tbody>
</table>
        </div>