<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery.maskedinput-1.3.min.js");
$APPLICATION->SetAdditionalCSS("/jslibs/jqueryui/css/cupertino/jquery-ui-1.8.22.custom.css");


$clubInfo = $arResult['arFields'];
$address  = $arResult['arFields']["PROPERTY_ADDRESS_VALUE"];
$name     = html_entity_decode($arResult['arFields']['NAME']);
$searh    = empty($address)
    ? $name
    : $address;

?>

<h1>
    <?=$name?>
</h1>
<br/>
<div class="alert alert-success hide">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
    Спасибо!<br/> Столик успешно забронирован
</div>

<input type="hidden" value="<?=$arResult['arFields']['ID']?>" id="club_id">
<input type="hidden" value="<?=date("d.m.Y")?>" id="date">
<div id='datepicker-user' class="span8">
    <div id='datepicker'>
    </div>
</div>
<div class="span3">
    <a data-target="#myModal" data-toggle="modal" class="how_work" href="#myModal">Как это работает</a>
    <br/>
    <br/>
    <br/>
    <table>
        <tr>
            <td><span class="label_table_free"></span></td>
            <td>столик свободен</td>
        </tr>
        <tr>
            <td><span class="label_table_busy"></span></td>
            <td>столик занят</td>
        </tr>
    </table>
</div>

<div style="position: relative;" id="plan_div" class="span11">
    <img src="<?=$arResult['arFields']["PROPERTY_PLAN_VALUE"]?>" id="plan_club" width="870px"/>
</div>


<div class="modal hide" id="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Забронировать столик</h3>
    </div>
    <div class="modal-body">
        <input type="hidden" value="" id="table_id">
        <table>
            <tr>
                <td>
                    <span class="thumbnail"><img src="#" id="img_table" width="160px"/></span>
                </td>
                <td valign="top">
                    <table>
                        <tr>
                            <td>Дата визита:</td>
                            <td><span id="date_vizit"></span></td>
                        </tr>
                        <tr>
                            <td>Количество человек:</td>
                            <td><span id="count_vizit"></span></td>
                        </tr>
                        <tr>
                            <td>Телефон:</td>
                            <td>
                                <?
                                if (!empty($arResult['userInfo']['PERSONAL_MOBILE'])):
                                    echo $arResult['userInfo']['PERSONAL_MOBILE']; elseif (!empty($arResult['userInfo']['PERSONAL_PHONE'])):
                                    echo $arResult['userInfo']['PERSONAL_PHONE']; else:?>
                                    <input type="text">
                                    <?endif;
                                //если телефон подтвержден
                                //если телефон не подтвержден
                                //если телефон не ууказан

                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Стоимость депозита:</td>
                            <td id="price_deposit"></td>
                        </tr>
                        <tr>
                            <td>Время визита:</td>
                            <td><input type="text" value="" name="time" id="time" size="10"></td>
                        </tr>
                        <tr>
                            <td>Комментарий к заказу:</td>
                            <td><textarea id="comments"></textarea></td>
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


<div class="modal hide" id="payment">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Оплата столика</h3>
    </div>
    <div class="modal-body">

    </div>
</div>

<div class="modal hide" id="boocing_none">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Бронирование столика</h3>
    </div>
    <div class="modal-body">
        На этот день столик забронирован. Выбирете пожалуйста другой столик или другой день.
    </div>
</div>