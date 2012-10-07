<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div id="social_like">
<? if($arParams["GOOGLE"]=="Y"):?>
<div class="like_item" style="width:80px">
<? $APPLICATION->AddHeadString("<script type='text/javascript' src='https://apis.google.com/js/plusone.js'>
  {lang: '".$arParams['GOOGLE_LANG']."'}</script>",true);?>


<g:plusone <?=($arParams['GOOGLE_ADD'] == 'Y')?'':'count="false"'?>></g:plusone>



</div>
<? endif?>


<? if($arParams['VKONTAKTE'] == "Y"):?>
<div class="like_item" style="width:150px">
<? $APPLICATION->AddHeadScript("http://userapi.com/js/api/openapi.js?34");?>
<script type="text/javascript">
  VK.init({apiId: <?=$arParams['VKONTAKTE_APIID']?>, onlyWidgets: true});
</script>
<div id="vk_like"></div>
<script type="text/javascript">

VK.Widgets.Like("vk_like", {type: "<?=$arParams['VKONTAKTE_TYPE']?>"});

VK.Observer.subscribe('widgets.like.liked',function(data){
    var element=document.getElementById('clubID');
    if(element){
        var clubID=$("#clubID").val();
        $.post("/bitrix/ui/ajax/club/rating.php",{clubID:clubID,count:data},function(data){
            $("#rating-a").html(data);
        });

     }
});
VK.Observer.subscribe('widgets.like.unliked',function(data){
    var element=document.getElementById('clubID');
    if(element){
        var clubID=$("#clubID").val();
        $.post("/bitrix/ui/ajax/club/rating.php",{clubID:clubID,count:data},function(data){
            $("#rating-a").html(data);
        });
    }
});
</script>
</div>
<? endif?>



<? if($arParams['MM_OK'] == "Y"):?>
<div class="like_item">
<a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share?share_url=http://<?=$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPage()?>" data-mrc-config="{'type' : '<?=$arParams['MM_OK_TYPE']?>', 'caption-mm' : '<?=$arParams['MM_NAME']?>', 'caption-ok' : '<?=$arParams['OK_NAME']?>', <?=($arParams['MM_OK_SH'] == "Y")?"'counter' : 'true'":"'nc' : '1'"?>, <?=($arParams['MM_OK_SH'] == "Y")?"'text' : 'true'":"'nt' : '1'"?>, 'width' : '<?=$arParams['MM_OK_WIDTH']?>'}">��������</a>
<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="<?=SITE_CHARSET?>"></script>
</div>
<? endif?>

<? if($arParams['TWEETTER'] == "Y"):?>
<div class="like_item">
<? $APPLICATION->AddHeadScript("//platform.twitter.com/widgets.js");?>
    <div>
      
<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://<?=$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPage()?>" data-count="<?=$arParams['TWEETTER_TYPE']?>" data-via="<?=$arParams['TWEETTER_NAME']?>" data-lang="ru">��������</a>
    </div>
</div>
<? endif?>

<? if($arParams['FACEBOOK'] == "Y"):?>
<div class="like_item">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#appId=215521648507704&xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-like" data-href="<?=$_SERVER['HTTP_HOST'].$APPLICATION->GetCurPage()?>" data-send="<?=($arParams['FACEBOOK_SEND_BUTTON']=="Y")?'true':'false'?>" data-layout="<?=$arParams['FACEBOOK_TYPE']?>" data-width="<?=$arParams["FACEBOOK_WIDTH"]?>" data-show-faces="<?=($arParams['FACEBOOK_SHOW_FACE']=="Y")?'true':'false'?>"></div>
</div>
<? endif?>


</div>
