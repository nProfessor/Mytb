/**
 *
 * User: Олег
 * Date: 07.02.13 23:11
 *
 */

$(document).ready(function () {


    $(".public").click(function(){
        var stockID=$(this).data("id");
        var obj=$(this);
        obj.find("span").addClass("loading loading_block");
        $.ajax({
            url:"/bitrix/ui/ajax/club/public.stock.php",
            type:"post",
            data:{
                ID:stockID
            },
            dataType:"json",
            success:function(data){
                if(data.status=="ok"){
                    obj.removeClass("loading").addClass(data.class);
                    obj.remove();
                }
            }
        });
    });

    $(".active_button").click(function(){
        var stockID=$(this).data("id");
        var obj=$(this);
        obj.removeClass("Y N").addClass("loading");
        $.ajax({
            url:"/bitrix/ui/ajax/club/active.php",
            type:"post",
            data:{
                ID:stockID
            },
            dataType:"json",
            success:function(data){
                if(data.status=="ok"){
                    obj.removeClass("loading").addClass(data.class);
                }

            }
        });
    });
});