<?

$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery.maskedinput-1.3.min.js");

$APPLICATION->SetAdditionalCSS("/jslibs/jqueryui/css/cupertino/jquery-ui-1.8.22.custom.css");


$clubInfo = $arResult['clubInfo'];
$address = $arResult['arFields']['PROPS']["ADDRESS"]['VALUE'];
$name = html_entity_decode($arResult['arFields']['NAME']);
$searh = empty($address) ? $name : $address;


$date = $arResult["date"];

?>

<input type="hidden" value="<?=$clubInfo["ID"]?>" id="clubID"/>

<div class="alert alert-success hide">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
    Спасибо!<br/> Столик успешно забронирован
</div>

<input type="hidden" value="<?=$arResult['arFields']['ID']?>" id="club_id">
<div class="manager">
    <div id='datepicker'>
    </div>
</div>
Выбирете свободный столик:
<div style="position: relative;" id="plan_div">
    <img src="<?=$clubInfo["PROPERTY_PLAN_VALUE"]?>" id="plan_club" width="900px"/>
</div>
<input type="hidden" id="date" value="<?=date("d . m . Y")?>">


<div class="modal hide" id="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss = "modal" >×</button >
        <h3>Забронировать столик</h3>
    </div>
    <div class="modal-body">
        <input type="hidden" value="" id="table_id">
        <table>
            <tr>
                <td valign="top">
                    <a href="#" class="thumbnail"><img src="http://placehold.it/160x120" id="img_table"/></a>
                </td>
                <td valign="top">
                    Бронирование: <span id="namber_table_booking"></span>
                    <br>
                    <br>

                    <div class="alert alert-error none" id="booking-data-errors">
                        Заполните пожалуйста все данные
                    </div>
                    <table>
                        <tr>
                            <td>Ф.И.О</td>
                            <td><input type="text" value="" name="NAME" id="NAME"></td>
                        </tr>
                        <tr>
                            <td>Телефон:</td>
                            <td><input type="text" value="" name="PHONE" id="PHONE"></td>
                        </tr>
                        <tr>
                            <td>Время прихода:</td>
                            <td><input type="text" value="" name="TIME" id="TIME"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-success" id="booking_table">Забронировать</a>
    </div>
</div>

<div class="modal hide" id="dialog_busy">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss = "modal" >×</button >
        <h3>Столик уже занят</h3>
    </div>
    <div class="modal-body">
К сожалению этот столик уже занят. Пожалуйста выбирете другой столик, или другой день.

    </div>

</div>
