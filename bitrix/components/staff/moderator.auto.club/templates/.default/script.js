/**
 *
 * User: professor
 * Date: 20.12.12 18:47
 *
 */

$(document).ready(function () {

    /**
     * Начинаем редактировать информацию о клубе
     */
    $(".club_name").click(function () {
        var club_id = $(this).data("id");

        $("#club_id").val(club_id);

        $.ajax({
            url:"/bitrix/ui/ajax/staff/club/getinfo.php",
            type:"post",
            dataType:"json",
            data:{
                club_id:club_id
            },
            success:function (data) {
                if (data.status == "ok") {

                    var club_info = data.result;

                    $("#club_id").val(club_info.ID);

                    $("input[name='NAME']").val(club_info.NAME);
                    $("input[name='SITE']").val(club_info.SITE);
                    $("input[name='EMAIL_MANAGER']").val(club_info.EMAIL_MANAGER);
                    $("input[name='AVERAGE_CHECK']").val(club_info.AVERAGE_CHECK);
                    $("textarea[name='TIME_WORKING']").val(club_info.TIME_WORKING);
                    $("textarea[name='DESCR']").val(club_info.DESCR);


                    for (var key in club_info.KIND_CLUB) {
                        $("input[name='KIND_CLUB[]'][value='" + key + "']").attr("checked", "checked");
                    }


                    $(".club_address_edite:not(:first)").remove();

                    for (var key in club_info.ADDRESS) {
                        var res = club_info.ADDRESS[key];
                        var obj = $(".club_address_edite:first").clone();
                        obj.show();
                        obj.appendTo($("#address_list"));
                        obj.find(".addres_text").val(res.ADDRESS);
                        obj.attr("id","club_address_"+res.ID);

                        var phone="";
                        for (var key2 in res.PHONE) {
                            phone+=res.PHONE[key2]+"\n";

                        }

                        obj.find("textarea").val(phone);
                        obj.find("select").find("option[value='" + res.SITY_ID + "']").attr("selected", "selected");

                    }

                    for (var key in club_info.MUSIC) {
                        $("input[name='MUSIC[]'][value='" + key + "']").attr("checked", "checked");
                    }

                    if (club_info.PREVIEW_PICTURE != null) {
                        $("<img/>").attr("src", club_info.PREVIEW_PICTURE).appendTo($(".logo_edite"));
                    } else {
                        $(".logo_edite img").remove();
                    }

                    $(".btn_content").remove();

                    $("<div/>").addClass("btn_content").appendTo($(".btn_content_all"));

                    $("<div>Загрузить</div>").addClass("btn upload_button_id").appendTo($(".btn_content"));

                    $.ajax_upload('.upload_button_id', {
                        // какому скрипту передавать файлы на загрузку? только на свой домен
                        action: '/bitrix/ui/ajax/upload/club_logo_moderator.php',
                        // имя файла
                        name: 'myfile',
                        // дополнительные данные для передачи
                        data: {
                            club_id : club_info.ID,
                            w:100,
                            h:200
                        },
                        autoSubmit: true,
                        responseType: false,
                        onComplete: function(file, response) {
                            $("<img/>").attr("src", response);
                              }
                    });

                }
            }
        });


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
            success:function(data){

            }
        });

    });


    $("#show").on("click",{},function(){
        $.ajax({
            url:"/bitrix/ui/ajax/staff/club/active.php",
            type:"post",
            data:{
                club_id:$("#club_id").val()
            },
            success:function(data){

            }
        });
    });



});