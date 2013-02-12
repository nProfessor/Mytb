/**
 *
 * User: Олег
 * Date: 21.01.13 13:42
 *
 */

$(document).ready(function () {

    $(".linc_coupon").click(function(){
        window.open($(this).data("link"), "");
        return false;
    });
});