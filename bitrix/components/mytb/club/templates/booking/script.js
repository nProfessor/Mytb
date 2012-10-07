$(document).ready(function () {
    var dataTableList = {};
    var ratio = 0.96;
    $.getJSON("/bitrix/ui/ajax/get_list_table.php", {clubID:$("#club_id").val()}, function (data) {

        for (var key in data) {
            div = $("<div data-id='" + data[key]["ID"] + "'/>");
            div.addClass("table_club");
            div.css("width", data[key]['PROPERTY_WIDTH_VALUE'] * ratio + "px");
            div.css("height", data[key]['PROPERTY_HEIGHT_VALUE'] * ratio + "px");
            div.css("left", (data[key]['PROPERTY_COORDINATESX_VALUE'] * ratio ) + "px");
            div.css("top", data[key]['PROPERTY_COORDINATESY_VALUE'] * ratio + "px");
            div.attr("id", data[key]["ID"]);
            div.attr("data-original-title", "Не курящий<br/>мест: 4");
            div.attr("rel", "tooltip");
            div.appendTo("#plan_div");
            div.tooltip();
            dataTableList[data[key]["ID"]] = data[key];
        }
    });


    $(".table_club:not(.booking)").live("click", function () {
        var tableID = $(this).data("id");
        $("#table_id").val(tableID);
        $("#time").val("");
        $("#date_vizit").text($("#date").val());
        $("#count_vizit").text(dataTableList[tableID]["PROPERTY_COUNT_VALUE"]);
        $("#price_deposit").text(dataTableList[tableID]["PROPERTY_PRICE_GROUP"]);
        $("#img_table").attr("src", dataTableList[tableID]["PREVIEW_PICTURE"] == null ? "/img/160x120.gif" : dataTableList[tableID]["PREVIEW_PICTURE"]);
        $("#dialog").modal();
    });


    $(".table_club.booking").live("click", function () {
        $("#boocing_none").modal();
        return false;
    });

    $("#time").mask("99:99");


    $("#booking_table").live("click", function () {
        var time_vizit = $("#time").val();
        var ar_time_vizit = time_vizit.split(":");
        if ((/[0-9]{2}:[0-9]{2}/.exec(time_vizit)) && (ar_time_vizit[0] >= 0 && ar_time_vizit[0] <= 24) && (ar_time_vizit[1] >= 0 && ar_time_vizit[1] <= 59)) {


            url:"/bitrix/ui/ajax/check_booking.php",
                $.ajax({
                    url:"/bitrix/ui/ajax/check_booking.php",
                    type:"post",
                    data:{
                        table_id:$("#table_id").val(),
                        club_id:$("#club_id").val(),
                        date:$("#date").val(),
                        time:$("#time").val(),
                        comments:$("#comments").val()
                    },
                    success:function (data) {
                        $("#payment .modal-body").html(data);
                        $("#payment").modal();
                    }
                });

            $("#dialog").modal("hide");

        } else {
            $("#time").addClass("errors");
        }

        return false;
    });


    $("#datepicker").datepicker({
        autoSize:true,
        numberOfMonths:3,
        dateFormat:"dd.mm.yy",
        regional:"ru",
        firstDay:1,
        monthNames:["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
        dayNamesMin:["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
        minDate:new Date(),
        onSelect:function (dateText, inst) {

            $("#date").val(dateText);
            $("#plan_div div").removeClass("booking");
            $.getJSON("/bitrix/ui/ajax/get_list_table_booking.php", {dateText:dateText}, function (data) {
                for (var key in data) {
                    $("#" + data[key]['PROPERTY_TABLE_VALUE']).addClass("booking");

                }
            });
        }
    });


});