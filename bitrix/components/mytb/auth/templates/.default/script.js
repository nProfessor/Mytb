/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 30.09.12
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $("#login_all").click(function () {

        var email = $("#modal_auth input[name='AUTH[EMAIL]']").val();
        var password = $("#modal_authinput[name='AUTH[PASSWORD]']").val();
        var reg = $("#modal_auth input[name='AUTH[REG]']:checked").val();

        $("#login_all").text("Выполняется вход");
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



//        window.vkAsyncInit = function() {
//        VK.init({
//            apiId: $("#apiVK").val()
//        });
//    };
//
//    setTimeout(function() {
//        var el = document.createElement("script");
//        el.type = "text/javascript";
//        el.src = "http://vkontakte.ru/js/api/openapi.js";
//        el.async = true;
//        document.getElementById("vk_api_transport").appendChild(el);
//    }, 0);
//
//    $("#auth_vk").click(function(){
//        VK.Auth.login(function(response) {
//            if (response.session) {
//
//                console.log(response);
//            } else {
//                alert('not auth');
//            }
//        },8199);
//    });



});