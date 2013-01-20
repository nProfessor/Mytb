/**
 *
 * User: Олег
 * Date: 19.01.13 18:00
 *
 */

$(document).ready(function () {
    $(".list_clubs .item").click(function(){
        $(".item_content").removeClass("active");
        $(this).parent(".item_content").addClass("active");
        $(".name_club").html($(this).attr("title"));

        $("#list").html("<img src='/images/loading5.gif'/>");
        
        $.ajax({
            url:"/bitrix/ui/ajax/club/get_list_stock.php",
            type:"post",
            dataType:"json",
            data:{
               CLUB_ID:$(this).data("id")
            },
            success:function(data){
                if(data.status=="ok"){
                    $("#list").html("");
                    for (var key in data.data) {
                        var stock = data.data[key];
                        var block=$(".template").clone();
                        
                        block.removeClass("template hide").addClass("block");
                        block.removeClass("template hide").addClass("block");
                        
                        block.find(".img_stock").addClass(stock.CLASS);
                        block.find(".time_stock").html("до "+stock.DATE_ACTIVE_TO);
                        block.find(".link_stock").html(stock.NAME);
                        block.find(".link_stock").attr("href","/club/"+stock.CLASS+"/"+stock.ID+"/");

                        block.appendTo($("#list"));
     
                    }

                    
                    
                    
                }else{
                    $("#list").html(data.message);
                    
                }
            }
        });
    });

});