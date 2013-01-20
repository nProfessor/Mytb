$(document).ready(function () {
    $("input[name='PERSONAL_BIRTHDAY']").mask("99.99.9999");




    $("#phone_confirme").click(function () {
        var phone = $("input[name='PERSONAL_PHONE']").val();
        $("#phone_confirme_text").hide();
        $("#phone_confirme_code").show();
        $("#phone_confirme_code #namber").text(phone);

        $.post("/bitrix/ui/ajax/sms/send_phone_confirme.php",{phone:phone});

        return false;
    });


    $("#repeatedly_phone_confirme_code").click(function () {
        var phone = $("input[name='PERSONAL_PHONE']").val();
        $("#phone_confirme_code #namber").text(phone);
        $.post("/bitrix/ui/ajax/sms/send_phone_confirme.php",{phone:phone});

        return false;
    });

    $("#phone_confirme_ajax").click(function () {
        var code = $("#phone_confirme_code_val").val();

        $.ajax({
            url:"/bitrix/ui/ajax/sms/phone_confirme.php",
            type:"post",
            data:{
                code:code
            },
            dataType:"json",
            success:function(data){

                if(data.status=="ok"){
                    $("#phone_confirme_code").hide();
                    $('<span class="help-inline">Телефон подтвержден.</span>').appendTo($("#message_text_phone"));
                    $("#phone_number").removeClass("warning").addClass("success");
                }else{
                    $("#phone_confirme_code").removeClass("input-append").addClass("alert-error");
                    $("#code_input");
                }
            }
        });

        return false;
    });



    $("#save_profile").click(function () {
        $(".error span.help-inline").hide("slow").remove();
        $(".error").removeClass("error");

        var name = $("input[name='NAME']").val();
        var last_name = $("input[name='LAST_NAME']").val();
        var email = $("input[name='EMAIL']").val();
        var second_name = $("input[name='SECOND_NAME']").val();
        var personal_phone = $("input[name='PERSONAL_PHONE']").val();
        var personal_birthday = $("input[name='PERSONAL_BIRTHDAY']").val();
        var personal_gender = $("select[name='PERSONAL_GENDER']").val();
        var personal_notes = $("textarea[name='PERSONAL_NOTES']").val();


        $.ajax({
            url:"/bitrix/ui/ajax/profile/profile.save.php",
            type:"post",
            dataType:"json",
            data:{
                "NAME":name,
                "LAST_NAME":last_name,
                "EMAIL":email,
                "PERSONAL_PHONE":personal_phone,
                "PERSONAL_BIRTHDAY":personal_birthday,
                "PERSONAL_GENDER":personal_gender,
                "PERSONAL_NOTES":personal_notes,
                "SECOND_NAME":second_name
            },
            success:function (data) {
                if (data["errors"]) {
                    var errors = data["errors"];
                    for (var key in errors) {
                        var error = errors[key];
                        var obj = $("input[name='" + key + "']");
                        obj.parents(".control-group").addClass("error");
                        $("<span class='help-inline'>" + error + "</span>").insertAfter(obj);
                    }
                } else {
                    $("#save_ok").show().animate({opacity:0}, 2000, function () {
                        $(this).hide().css("opacity", "1")
                    });
                    $("#save_ok .content_text").html(data["message"]);
                }
            }
        });

    });

})
;