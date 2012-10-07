/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 04.10.12
 * Time: 21:01
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {


    $(".timer").each(function () {
        new eTextTimer2({
            targetDate:$(this).data("active"),
            nowDate:$("#time_now").val(),
            leadingZero:true,
            finishMessage:"Акция завершена!",
            dstprefix:"time"
        });
    });


});