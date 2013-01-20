/**
 * Created with JetBrains PhpStorm.
 * User: professor
 * Date: 28.10.12
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $(".m_tooltip").tooltip();

    VK.init({apiId: 3009096, onlyWidgets: true});
    try {
        if (VK && VK.Observer && VK.Observer.subscribe) {
            VK.Observer.subscribe('widgets.like.liked', function (data) {
                var element = document.getElementById('clubID');
                if (element) {
                    var clubID = $("#clubID").val();
                    $.post("/bitrix/ui/ajax/club/rating.php", {clubID:clubID, count:data}, function (data) {
                        $("#rating-a").html(data);
                    });

                }
            });
            VK.Observer.subscribe('widgets.like.unliked', function (data) {
                var element = document.getElementById('clubID');
                if (element) {
                    var clubID = $("#clubID").val();
                    $.post("/bitrix/ui/ajax/club/rating.php", {clubID:clubID, count:data}, function (data) {
                        $("#rating-a").html(data);
                    });
                }
            });
        }
    }
    catch
        (error) {
    }

    try {
        if (FB && FB.Event && FB.Event.subscribe) {
            FB.Event.subscribe('edge.create', function () {
                var element = document.getElementById('clubID');
                if (element) {
                    var clubID = $("#clubID").val();
                    $.post("/bitrix/ui/ajax/club/rating.php", {clubID:clubID, count:data}, function (data) {
                        $("#rating-a").html(data);
                    });
                }
            });
            FB.Event.subscribe('edge.remove', function () {
                $.post("/bitrix/ui/ajax/club/rating.php", {clubID:clubID, count:data}, function (data) {
                    $("#rating-a").html(data);
                });
            });
        }
    }
    catch
        (error) {
    }


});

