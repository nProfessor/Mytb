/**
 *
 * User: Олег
 * Date: 28.12.12 17:04
 *
 */

$(document).ready(function () {
 $(".item-filter select").change(function(){
     if($(this).val()==""){
         console.log($(this));
         $(this).css("color","#ccc");
     }else{
         $(this).css("color","#000");
     }
 });

});