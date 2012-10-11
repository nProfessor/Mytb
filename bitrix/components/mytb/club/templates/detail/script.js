/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 16.09.12
 * Time: 14:55
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function () {
    $("#subs_stock").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/stock/");
        $('#redirect').val("/club/" + clubID + "/stock/");
        $('#subs_ok_modal').attr("href", "/club/" + clubID + "/stock/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "stock");
return false;
    });

    $("#subs_event").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/event/");
        $('#redirect').val("/club/" + clubID + "/event/");
        $('#subs_ok').attr("href", "/club/" + clubID + "/event/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "event");

    });

    $("#subs_news").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/news/");
        $('#redirect').val("/club/" + clubID + "/news/");
        $('#subs_ok').attr("href", "/club/" + clubID + "/news/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "news");
    });


    $("#subs_ok").live("click", function () {
        var clubID = $("#clubID").val();
        var auth = $(this).data("auth");
        if (auth == "no") {
            $('#modal_subs').modal("hide");
            $('#modal_auth').modal();
            return false;
        }

        location="/club/" + clubID + "/stock/?subscribe=ok";
        return false;
    });

    $("#subs_ok_modal").live("click", function () {

        var auth = $(this).data("auth");
        if (auth == "no") {
            $('#modal_subs').modal("hide");
            $('#modal_auth').modal();
            return false;
        }

        return false;
    });

    $('#subs_stock').tooltip();

    if($('.club_info_content p').css("height").replace("px","")<=220){
        $('.club_info_content div').hide();
    }

    $('.club_info_content div').toggle(function(){
        var height=$('.club_info_content p').css("height");
        $('.club_info_content').css("height",parseInt(height.replace("px",""))+48+"px");
        $(this).text("скрыть");
    },function(){

        $('.club_info_content').css("height",200+"px");
        $(this).text("смотреть полностью");
    });


});