/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 14.07.12
 * Time: 14:34
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    $('.carousel').carousel({
        interval: 40000
    });


    $(".timer").each(function () {
        new eTextTimer2({
            targetDate:$(this).data("active"),
            nowDate:$("#time_now").val(),
            leadingZero:true,
            finishMessage:"Акция завершена!",
            dstprefix:"time"+$(this).data("id")
        });
    });
});
