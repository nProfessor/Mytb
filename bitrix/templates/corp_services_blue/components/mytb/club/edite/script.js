/**
 * User:        Олег
 * Data:        11.06.12 22:37
 * Site: http://sForge.ru
 **/
function someFunction(img, selection) {

}
$(document).ready(function () {


    $.getJSON("/bitrix/ui/ajax/get_list_table.php",{id:10},function(data){

        for (var key in data) {

            console.log(data[key]);
            div=$("<div/>");
            div.addClass("table");
            div.css("width",data[key]['PROPERTY_WIDTH_VALUE']+"px");
            div.css("height",data[key]['PROPERTY_HEIGHT_VALUE']+"px");
            div.css("left",data[key]['PROPERTY_COORDINATESX_VALUE']+"px");
            div.css("top",data[key]['PROPERTY_COORDINATESY_VALUE']+"px")
            div.appendTo("#plan_div");
        }
    });


    $('#plan_club').imgAreaSelect({
        handles:true,
        onSelectEnd: function (img, selection) {
            $('#X').val(selection.x1);
            $('#Y').val(selection.y1);
            $('#W').val(parseInt(selection.x2)-parseInt(selection.x1));
            $('#H').val(parseInt(selection.y2)-parseInt(selection.y1));
        }
    });

    $('#add-table').live("click", function () {
        var dialogdiv = $("#dialog");
        dialogdiv.dialog({
            buttons:{
                "Создать":function () {
                    $.ajax({
                        url:"/bitrix/ui/ajax/add_table.php",
                        type:"post",
                        data:{
                            x:$("#X").val(),
                            y:$("#Y").val(),
                            w:$("#W").val(),
                            h:$("#H").val(),
                            text:$("#text").val(),
                            name:$("#name-table").val(),
                            success:function(data){
                               $('.dialog > input').val("");
                               $('.dialog > textarea').val("");
                                alert("Столик создан");

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


});