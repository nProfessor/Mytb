/**
 *
 * User: Олег
 * Date: 19.01.13 21:20
 *
 */

$(document).ready(function () {

    var button = $('.upload_avatar'), interval;

    $.ajax_upload(button, {
        action:'/bitrix/ui/ajax/upload/avatar.php',
        name:'myfile',
        type:"post",
        data:{
            w:200,
            h:0
        },
        onSubmit:function (file, ext) {
//            $("img#load").attr("src", "load.gif");
            $("#upload_avatar_button").text('Идет загрузка');
            $("#upload_avatar_button").addClass('menu_load_right');
            this.disable();

        },
        onComplete:function (file, response) {
//            $("img#load").attr("src", "loadstop.gif");
            $("#upload_avatar_button").text('Изенить фотографию');
            $("#upload_avatar_button").removeClass('menu_load_right');

            this.enable();

            $("#ava img").attr("src", response);

        }
    });
});