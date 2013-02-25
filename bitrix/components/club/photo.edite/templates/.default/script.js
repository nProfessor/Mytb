/**
 *
 * User: professor
 * Date: 20.12.12 18:47
 *
 */

$(document).ready(function () {

    var button = $('#jquery-uploader'), interval;

    $.ajax_upload(button, {
        action:'/bitrix/ui/ajax/upload/photo_club.php',
        name:'myfile',
        type:"post",
        data:{
            w:200,
            h:200
        },
        onSubmit:function (file, ext) {
//
            $("#jquery-uploader").text('Идет загрузка');
            $("#upload_avatar_button").addClass('menu_load_right');
            this.disable();

        },
        onComplete:function (file, response) {
//            $("img#load").attr("src", "loadstop.gif");
            $("#jquery-uploader").text('Загрузить фотографии');
            $("#upload_avatar_button").removeClass('menu_load_right');

            this.enable();

            $("#ava img").attr("src", response);

        }
    });

});