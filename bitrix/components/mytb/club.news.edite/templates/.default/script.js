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


    var button = $('.upload_avatar'), interval;

    $.ajax_upload(button, {
        action:'/bitrix/ui/ajax/upload/club_news_img.php',
        name:'myfile',
        type:"post",
        data:{
            w:200,
            h:200,
            newsID:$("#newsID").val()
        },
        onSubmit:function (file, ext) {
//            $("img#load").attr("src", "load.gif");
            $("#upload_avatar_button").text('Идет загрузка');
            this.disable();

        },
        onComplete:function (file, response) {
//            $("img#load").attr("src", "loadstop.gif");
            $("#upload_avatar_button").text('Изменить');

            this.enable();


            $(".thumbnail img").attr("src", response);

        }
    });
    $(".date_time").datepicker({ dateFormat: "dd.mm.yy" });

});
