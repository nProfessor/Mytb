/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 04.10.12
 * Time: 21:01
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {


    $(".timer").each(function () {
        new eTextTimer2({
            targetDate:$(this).data("active"),
            nowDate:$("#time_now").val(),
            leadingZero:true,
            finishMessage:"Акция завершена!",
            dstprefix:"time"+$(this).data("id")
        });
    });

    $('#subs_stock').tooltip();


    $("#subs_ok").live("click", function () {
        var clubID = $("#clubID").val();
        var auth = $(this).data("auth");
        if (auth == "no") {
            $('#modal_auth').modal();
            return false;
        }

        location="/club/" + clubID + "/stock/?subscribe=ok";
        return false;
    });



});