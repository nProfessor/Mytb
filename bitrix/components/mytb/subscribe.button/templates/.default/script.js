/**
 *
 * User: Олег
 * Date: 13.01.13 15:16
 *
 */

$(document).ready(function () {
    $(".button_subsribe").on("click", {}, function () {
        var obj = $(this);
        $.ajax({
            url:"/bitrix/ui/ajax/club/subscribe.php",
            type:"post",
            data:{
                CLUB_ID:$(this).data("id")
            },
            dataType:"json",
            success:function (data) {
                if(data.status=="ok"){//Подписался
                    obj.removeClass("button_subsribe").addClass("button_subsribe_ok");
                    obj.find("span").html("Ты подписался на");
                        _gaq.push(['_trackEvent', 'subscribe', 'ok', $(".club_info h1").html()]);
                }else{//Не подписался
                    $("#modal_auth_subscribe").modal();
                }
            }
        });
    });
});