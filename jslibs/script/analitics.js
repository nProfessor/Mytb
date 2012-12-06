/**
 * Created with JetBrains PhpStorm.
 * User: professor
 * Date: 17.11.12
 * Time: 17:25
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function(){

    //Нажал подписаться на акции
    $("#subs_ok").click(function(){
        _gaq.push(['_trackEvent', 'stock', 'subscrib', $(".workarea-inner h1").html()]);
    });

    //Нажал список акций
    $("#subs_stock").click(function(){
        _gaq.push(['_trackEvent', 'stock', 'list', $(".workarea-inner h1").html()]);
    });

//
//    //Нажал кнопку войти
//    $("#login").click(function(){
//        _gaq.push(['_trackEvent', 'login', 'list', $(".workarea-inner h1").html()]);
//    });


    //Нажал на кнопки информации
    $(".partnership, .how_work, .question").click(function(){
        _gaq.push(['_trackEvent', 'link_info', 'click', $(this).text()]);
    });


    //Нажал на акцию
    $(".auth_stocks").click(function(){
        _gaq.push(['_trackEvent', 'stock', 'go', $("#club_name").val()]);
    });


});