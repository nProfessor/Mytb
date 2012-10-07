/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 15.09.12
 * Time: 10:54
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function () {

    $(".stock_info").click(function () {
        var title = $(this).data("title");
        var description = $(this).data("description");
        var img = $(this).data("img");

        $('#myModalLabel').text(title);
        $('#modal-text').text(description);
        $('#link_stock').attr("href", "#");
        $('.img-rounded').attr("src", img);

        $('.modal').modal("show");
        return false;
    });

    $("#filter_club").change(function () {
        $.ajax({
            url:"/personal/subscribe/stock/",
            type:"post",
            data:{
                clubID:$(this).val()
            },
            success:function (data) {
                $("#list").html(data);
            }
        });
    });
});