/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 30.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $("#login").click(function () {

        var email = $(".modal_auth_subscribe input[name='AUTH[EMAIL]']").val();
        var password = $(".modal_auth_subscribe input[name='AUTH[PASSWORD]']").val();
        var reg = $(".modal_auth_subscribe input[name='AUTH[REG]']:checked").val();

        $("#login").text("Выполняется вход");
        $("#errors_auth").addClass("hide");
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
                    $("#error_text").html(data.message);
                    $("#errors_auth").removeClass("hide");
                    $("#login").text("Войти");
                } else {
                    location = $("#redirect").val();
                }

            }
        });

        return false;
    });
});