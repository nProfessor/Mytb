/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 30.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {

$(".login_show").click(function(){
    $('#auth_block_top').modal();
    return false;
});

    $(".login").click(function () {

        var email = $("#auth_block_top input[name='AUTH[EMAIL]']").val();
        var password = $("#auth_block_top input[name='AUTH[PASSWORD]']").val();
        var reg = $("#auth_block_top input[name='AUTH[REG]']:checked").val();

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
                    location = $("#login_top_redirect").val();
                }

            }
        });

        return false;
    });


});