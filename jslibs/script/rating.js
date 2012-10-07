/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 06.09.12
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    $('#rating-a').click(function(){

        var top=$('#rating-a').offset().top+35;
        var left=$('#rating-a').offset().left-263;
        $('#rating-like').toggle();
        $('#rating-like').offset({left:left,top:top});

        return false;
    });
});