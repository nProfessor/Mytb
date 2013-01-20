/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 16.09.12
 * Time: 14:55
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function () {
    $(".auth_stocks").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", $(this).data("url"));
        $('#no_subs').attr("target", "_blank");
        $('#redirect').val("/club/" + clubID + "/stock/?subscribe=ok");
        $('#subs_ok_modal').attr("href", "/club/" + clubID + "/stock/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "stock");
        return false;
    });


    $("#subs_stock").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/stock/");
        $('#redirect').val("/club/" + clubID + "/stock/?subscribe=ok");
        $('#subs_ok_modal').attr("href", "/club/" + clubID + "/stock/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "stock");
        return false;
    });

    $("#subs_event").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/event/");
        $('#redirect').val("/club/" + clubID + "/event/?subscribe=ok");
        $('#subs_ok').attr("href", "/club/" + clubID + "/event/?subscribe=ok").addClass("subsribe-ok").attr("data-subs", "event");

    });

    $("#subs_news").click(function () {
        var clubID = $("#clubID").val();
        $('#modal_subs').modal();
        $('#no_subs').attr("href", "/club/" + clubID + "/news/");
        $('#redirect').val("/club/" + clubID + "/news/?subscribe=ok");
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


    $(".menu_club a").click(function(){
        var id=$(this).data("block");
        $(".menu_club a").removeClass("active");
        $(this).addClass("active");
        $(".block_info").hide();
        $("#b_"+id).show();
        $('html,body').stop();

    });


    function show_block(){
        var anc = window.location.hash.replace("#","");
        if(anc!=""){
            $("#a_"+anc).click();
        }else{
            if($(".stock_title").length>0){
                $("#a_stock").click();
            }else{
                $("#a_map").click();
            }
        }

    }
    show_block();
});