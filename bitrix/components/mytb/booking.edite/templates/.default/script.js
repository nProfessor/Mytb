$(document).ready(function () {
    var dataTableList = {};
    $.getJSON("/bitrix/ui/ajax/get_list_table.php", {clubID:$("#clubID").val()}, function (data) {
        dataTableList = data;
        for (var key in data) {
            div = $("<div data-id='" + data[key]["ID"] + "'/>");
            div.addClass("table_club");
            div.css("width", data[key]['PROPERTY_WIDTH_VALUE']+ "px");
            div.css("height", data[key]['PROPERTY_HEIGHT_VALUE']  + "px");
            div.css("left", (data[key]['PROPERTY_COORDINATESX_VALUE'] ) + "px");
            div.css("top", data[key]['PROPERTY_COORDINATESY_VALUE']  + "px");
            div.attr("id", data[key]["ID"]);
            div.attr("title", data[key]["NAME"]);
            div.appendTo("#plan_div");
        }

        var d = new Date();

        $("#plan_div div").removeClass("booking");
        $.getJSON("/bitrix/ui/ajax/get_list_table_booking.php", {dateText:d}, function (data) {
            for (var key in data) {
                $("#" + data[key]['PROPERTY_TABLE_VALUE']).addClass("booking");

            }
        });


    });

    $("#TIME").mask("99:99");


    $.datepicker.setDefaults($.datepicker.regional[ "ru" ])
    $("#datepicker").datepicker(
        {
            dateFormat:"dd.mm.yy",
            numberOfMonths:3,
            regional:"ru",
            firstDay: 1,
            monthNames:["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            dayNamesMin:["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],


            onSelect:function (dateText, inst) {
                $("#plan_div div").removeClass("booking");
                $.getJSON("/bitrix/ui/ajax/get_list_table_booking.php", {dateText:dateText}, function (data) {
                    for (var key in data) {
                        $("#" + data[key]['PROPERTY_TABLE_VALUE']).addClass("booking");

                    }
                });

                $("#date").val(dateText);
            }
        }
    );

    $(".table_club").live("click", function () {
        $("#table_id").val($(this).data("id"));
        $("#namber_table_booking").text($(this).attr("title"));

        if($(this).hasClass("booking")){
            $("#dialog_busy").modal();
        }else{
            $("#dialog").modal();
        }

    });

    $("#booking_table").live("click", function () {

        var name = $("#NAME").val();
        var phone = $("#PHONE").val();

        if (phone == "") {
            $("#booking-data-errors").removeClass("none");
        } else {
            $("#booking-data-errors").addClass("none");

            //проверяем есть ли пользователь с таким телефоном

            $.ajax({
                url:"/bitrix/ui/ajax/check_booking.php",
                type:"post",
                data:{
                    table_id:$("#table_id").val(),
                    club_id:$("#club_id").val(),
                    date:$("#date").val(),
                    type:"club",
                    time:$("#TIME").val(),
                    fio:name,
                    phone:phone
                },
                success:function (data) {
                    $("#" + $("#table_id").val()).addClass("booking");
                    $(".alert").show();

                }
            });
            $("#dialog").modal("hide");
        }
        return false;
    });




});