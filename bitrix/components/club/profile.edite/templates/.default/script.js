/**
 *
 * User: professor
 * Date: 20.12.12 18:47
 *
 */

$(document).ready(function () {
    tinyMCE.init({
        mode : "exact",
        elements : "elm1",
        theme : "advanced",
        skin : "o2k7",
        plugins : "inlinepopups,paste",
        // Theme options
        theme_advanced_buttons1 : "pasteword,|, bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,",
           theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true
    });



    $(".add_new_address").click(function(){
        $(".club_address_edite:first").clone().show().addClass("new").appendTo($("#address_list"));
    });

    $("#save").on("click",{},function(){

        var music = [];
        var kind_club = [];
        var addres = [];
        var addres_new = [];

        $("input[name='MUSIC[]']:checked").each(function(){
            music.push($(this).val());
        });

        $("input[name='KIND_CLUB[]']:checked").each(function(){
            kind_club.push($(this).val());
        });

        $(".club_address_edite:not(.new)").each(function(){
            if($(this).find(".addres_text").val()!=""){
            var obj={
                addres:$(this).find(".addres_text").val(),
                sity:$(this).find("select").val(),
                phone:$(this).find(".phone").val(),
                id:$(this).attr("id").replace("club_address_","")
            };

            addres.push(obj);
            }
        });


        $(".club_address_edite.new").each(function(){
            if($(this).find(".addres_text").val()!=""){
                var obj={
                    addres:$(this).find(".addres_text").val(),
                    sity:$(this).find("select").val(),
                    phone:$(this).find(".phone").val()
                };

                addres_new.push(obj);
            }
        });
        $("#message_errors").html("");
        $("#message_errors").addClass("loading_left");
        $("#message_errors").removeClass("red green");

        $.ajax({
            url:"/bitrix/ui/ajax/staff/club/saveinfo.php",
            type:"post",
            data:{
                club_id:$("#club_id").val(),
                NAME:$("input[name='NAME']").val(),
                SITE:$("input[name='SITE']").val(),
                EMAIL_MANAGER:$("input[name='EMAIL_MANAGER']").val(),
                AVERAGE_CHECK:$("input[name='AVERAGE_CHECK']").val(),
                TIME_WORKING:$("textarea[name='TIME_WORKING']").val(),
                DESCR:$("textarea[name='DESCR']").val(),
                MUSIC:music,
                ADDRES:addres,
                ADDRES_NEW:addres_new,
                KIND_CLUB:kind_club

            },
            dataType:"json",
            success:function(data){
                $("#message_errors").removeClass("loading_left");

                if(data.status=="ok"){
                    $("#message_errors").addClass("green");
                    $("#message_errors").html("Информация сохранена");
                }else{
                    $("#message_errors").addClass("red");
                    $("#message_errors").html("Произошла ошибка.  Попробуйте повторить попытку чуть позже.");
                }
            }
        });

    });



});