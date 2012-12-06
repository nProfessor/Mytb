/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 30.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {

$(".auth_stocks").click(function(){

    $('#club_name_show').html($('#club_name').val());
    $('#img_club_logo').attr("src",$('#club_img').val());
    $('#auth_stocks').modal();
    return false;
});

    $(".login").click(function () {

        var email = $("#auth_stocks input[name='AUTH[EMAIL]']").val();
        var password = $("#auth_stocks input[name='AUTH[PASSWORD]']").val();
        var reg = $("#auth_stocks input[name='AUTH[REG]']:checked").val();

        $(".login").text("Вхожу...");
        $(".errors_auth").addClass("hide");
        $.ajax({
            url:"/bitrix/ui/ajax/auth/auth.php",
            type:"post",
            data:{
                reg:reg,
                email:email,
                password:password
            },
            dataType:"json",
            success:function (data) {
                if (data.status == "errors") {

                    //нужно помечать красным, если неверный логин и пароль
                    if(data.input!=undefined){

                    }
                    $(".error_text").html(data.message);
                    $(".errors_auth").removeClass("hide");
                    $(".login").text("Войти");
                } else {
                    location = "";
                }

            }
        });

        return false;
    });


});