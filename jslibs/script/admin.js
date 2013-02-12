/**
 *
 * User: Олег
 * Date: 11.02.13 22:38
 *
 */

$(document).ready(function () {

    $(".admin_club").click(function(){
        var clubID=$(this).data("id");
        $.ajax({
            url:"/bitrix/ui/ajax/admin/auth.php",
            type:"post",
            dataType:"json",
            data:{
                clubID:clubID
            },
            success:function(data){
                window.location="/personal/";
            }
        });
        return false;
    });

});