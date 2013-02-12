/**
 * Created with JetBrains PhpStorm.
 * User: professor
 * Date: 11.11.12
 * Time: 15:14
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){

    tinyMCE.init({
        mode : "textareas",
        theme : "simple"
        });


    var button1 = $('.upload_avatar_2'), interval;

    $.ajax_upload(button1, {
        action:'/bitrix/ui/ajax/upload/club_event_img.php',
        name:'myfile',
        type:"post",
        data:{
            w:400,
            h:400,
            eventID:$("#eventID").val()
        },
        onSubmit:function (file, ext) {
//            $("img#load").attr("src", "load.gif");
            $("#upload_avatar_button_2").text('Идет загрузка...');
            this.disable();

        },
        onComplete:function (file, response) {
//            $("img#load").attr("src", "loadstop.gif");
            $("#upload_avatar_button_2").text('Изменить');

            this.enable();


            $(".img_stock img").attr("src", response);
            console.log(response);

        }
    });

    $(".date_time").datepicker({ dateFormat: "dd.mm.yy" });


    $(".public").click(function(){
        var stockID=$(this).data("id");
        var obj=$(this);
        obj.find("span").addClass("loading loading_block");
        $.ajax({
            url:"/bitrix/ui/ajax/club/public.event.php",
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

});


