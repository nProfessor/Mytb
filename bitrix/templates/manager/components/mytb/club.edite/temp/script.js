/**
 * User:        Олег
 * Data:        11.06.12 22:37
 * Site: http://sForge.ru
 **/
function someFunction(img, selection) {

}
$(document).ready(function () {


    $.getJSON("/bitrix/ui/ajax/get_list_table.php", {id:10}, function (data) {

        for (var key in data) {
            div = $("<div data-id='" + data[key]["ID"] + "'/>");
            div.addClass("table_club");
            div.addClass("booking");
            div.css("width", data[key]['PROPERTY_WIDTH_VALUE'] + "px");
            div.css("height", data[key]['PROPERTY_HEIGHT_VALUE'] + "px");
            div.css("left", data[key]['PROPERTY_COORDINATESX_VALUE'] + "px");
            div.css("top", data[key]['PROPERTY_COORDINATESY_VALUE'] + "px");
            div.attr("id", data[key]["ID"]);
            div.attr("title", data[key]["NAME"]);
            div.appendTo("#plan_div");


        }
    });


    $('#plan_club').imgAreaSelect({
        handles:true,
        onSelectEnd:function (img, selection) {
            $('#X').val(selection.x1);
            $('#Y').val(selection.y1);
            $('#W').val(parseInt(selection.x2) - parseInt(selection.x1));
            $('#H').val(parseInt(selection.y2) - parseInt(selection.y1));
        }
    });


    $('#add-table').live("click", function () {
        var dialogdiv = $("#dialog");
        dialogdiv.dialog({
            width:650,
            modal:true,
            draggable:false,
            buttons:{
                "Создать":function () {
                    $.ajax({
                        url:"/bitrix/ui/ajax/table/add_table.php",
                        type:"post",
                        data:{
                            x:$("#X").val(),
                            y:$("#Y").val(),
                            w:$("#W").val(),
                            h:$("#H").val(),
                            number:$("#number-table").val(),
                            count:$("#count-persone").val(),
                            price_group:$("#price_group").val(),
                            success:function (data) {
                                $('.dialog > input').val("");
                                $('.dialog > textarea').val("");
                                alert("Столик создан");

                                div = $("<div data-id='" + parseInt(data) + "'/>");
                                div.addClass("table_club");
                                div.css("width", $("#W").val());
                                div.css("height", $("#H").val() + "px");
                                div.css("left", $("#X").val() + "px");
                                div.css("top", $("#Y").val() + "px");
                                div.appendTo("#plan_div");
                                dialogdiv.dialog("close");
                            }
                        }
                    });
                },
                "Отмена":function () {
                    dialogdiv.dialog("close");
                }
            }
        });

    });


    $('.table_edit').click(function () {
        var tableID = $(this).data("id");

        $('#table_id').val(tableID);

        $.getJSON("/bitrix/ui/ajax/get_table_info.php", {
            tableID:tableID
        }, function (data, textStatus) {

            $('#number-table-edite').val(data['NAME']);
            $('#count-persone-edite').val(data['PROPERTY_COUNT_VALUE']);
            $('#price_group-edite').val(data['PROPERTY_PRICE_GROUP_VALUE']);

            $('#dialog_table_edit').modal();
        });

        return false;
    });


    $('#table_save_info').click(function () {
        var tableID = $('#table_id').val();
        var name = $('#number-table-edite').val();
        var price_group = $('#price_group-edite').val();
        var count = $('#count-persone-edite').val();

        $.ajax({
            url:"/bitrix/ui/ajax/table/save_table.php",
            type:"post",
            dataType:"json",
            data:{
                tableID:tableID,
                name:name,
                price_group:price_group,
                count:count
            },
            success:function (data) {
                var table_name = $("#table_ID_" + tableID).find(".table_name");
                var table_group = $("#table_ID_" + tableID).find(".table_group");
                var table_count = $("#table_ID_" + tableID).find(".table_count");


                table_name.html(data['table']["name"]);
                table_group.html(data['table']["price_group"]+" руб.");
                table_count.html(data['table']["count"]);
                $('#dialog_table_edit').modal("hide");
            }
        });

        return false;
    });


    $('.table_delete').click(function () {
        if (confirm("Вы точно хотите удалить этот столик?")) {

            var tableID = $(this).data("id");

            $.ajax({
                url:"/bitrix/ui/ajax/table/delete_table.php",
                type:"post",
                data:{
                    tableID:tableID
                },
                success:function (data) {
                    $("#table_ID_" + tableID).remove();
                }
            });
        }
        return false;
    });

});