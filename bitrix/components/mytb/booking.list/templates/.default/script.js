/**
 * User:        Олег
 * Data:        08.07.12 14:28
 * Site: http://sForge.ru
 **/

$(document).ready(function () {
var bookingDay;
    $("#example").dataTable({
        bPaginate:false,
        bLengthChange:false
    });

    $('#date_select').tooltip();


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
                $.ajax({
                    url:"/bitrix/ui/ajax/get_list_booking_manager.php",
                    data:{
                        date:dateText
                    },
                    success:function (data) {

                        $("#table_list_booling").html(data);
                        $("#example").dataTable({
                            bPaginate:false,
                            bLengthChange:false
                        });

                        for (d in bookingDay){
                            $("#d_"+d).addClass("red");

                        }
                    }
                });


            },
            onChangeMonthYear: function(year, month, inst) {
                $.getJSON("/bitrix/ui/ajax/get_list_booking_day.php",{
                    dateFrom:month +"."+year
                },function(data){
                    for (d in data){
                        $("#d_"+d).addClass("red");
                    }
                });
            }
        }
    );

    $.getJSON("/bitrix/ui/ajax/get_list_booking_day.php",function(data){
        bookingDay=data;
        for (d in data){
            $("#d_"+d).addClass("red");
        }
    });

    $('#date_select').click(function () {
        $("#datepicker_div").toggle()
    });


});