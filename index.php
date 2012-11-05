<?

$dialog=false;
if(!isset($_COOKIE['myTB_popup'])&&!$_COOKIE['myTB_popup']=="ok"){
SetCookie("myTB_popup","ok",time()+86400*100);
  $dialog=true;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "клуб, бар, ресторан, кафе");
$APPLICATION->SetPageProperty("description", "MyTb.ru - Первыми узнаем об акциях, событиях и новостях ваших любимых заведений, клубах и барах.");
$APPLICATION->SetTitle("MyTb.ru - Первыми узнаем об акциях, событиях и новостях клубов, баров, ресторанов.");
?>

<?$APPLICATION->IncludeComponent("mytb:club.list", "home", array(
        "CACHE_TIME"=>600,
        "PAGEN_1"=>intval($_GET['PAGEN_1'])
    ),
    false
);?>

<?if($dialog):?>

<style type="text/css">
    .modal{
        width: 800px !important;
        margin: -250px 0 0 -420px !important;
    }
    .modal-header h3
    {
        text-align: center;
    }
    .step{
        display: inline-block;
        width: 185px;
        height: 220px;
        background: url('/img/Pop-up%202-1.png') no-repeat 0px 0px;
    }
    .step1{
        background: url('/img/Pop-up%202-1.png') no-repeat 0px 0px;
    }
    .step2{
        background: url('/img/Pop-up%202-1.png') no-repeat -215px 0px;
    }
    .step3{
        background: url('/img/Pop-up%202-1.png') no-repeat -410px 0px;
    }
    .step4{
        background: url('/img/Pop-up%202-1.png') no-repeat -650px 0px;
    }
    .text-popup div{
        padding:0px 10px;
    }
</style>
<div class="modal hide fade width_800" id="popup" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>MyTb.ru - как это работает</h3>
    </div>
    <div class="modal-body">
        <p>
        <table>
            <tr>
                <td>
                    <div class="step step1"></div>
                </td>
                <td>
                    <div class="step step2"></div>
                </td>
                <td>
                    <div class="step step3"></div>
                </td>
                <td>
                    <div class="step step4"></div>
                </td>
            </tr>
            <tr class="text-popup">
                <td><div>Вы постоянно ходите в любимые заведения?</div></td>
                <td><div>Найдите их на нашем сайте</div></td>
                <td><div>Подпишитесь на акции выбранных заведений</div></td>
                <td><div>Получайте уведомления об акциях, проводимых этими заведениями</div></td>
            </tr>
        </table>

        </p>
    </div>
    <div class="modal-footer">

        <a href="#" class="btn btn-primary" id="reg">Зарегистрироваться</a> или <a href="#" data-dismiss="modal" aria-hidden="true">закрыть</a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#popup').modal({width:'940',left:'36%'});

        $("#reg").click(function(){
            $('#popup').modal("hide");
            $('#modal_auth').modal();
        });


    });
</script>

<input id="redirect" type="hidden" value="/">
<? $APPLICATION->IncludeComponent("mytb:auth", "",  array("AUTH_URL"=>"/"),false); ?>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>