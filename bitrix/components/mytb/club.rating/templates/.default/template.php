<div class="popover bottom" id="rating-like">
    <div class="arrow"></div>
    <h3 class="popover-title">Рейтинг</h3>

    <div class="popover-content">
        <table>
            <tr>
                <td style="width: 100px;">Голосовать:</td>
                <td>

<?$APPLICATION->AddHeadScript("//vk.com/js/api/openapi.js");?>
                    <script type="text/javascript">
                        VK.init({apiId: <?=SOC_API_ID_VK?>, onlyWidgets: true});
                    </script>

                    <!-- Put this div tag to the place, where the Like block will be -->
                    <div id="vk_like"></div>
                    <script type="text/javascript">
                        VK.Widgets.Like("vk_like", {type: "mini"});
                    </script>
                </td>
            </tr>
            <?if(0)://TODO facebook пока отключили?>
            <tr>
                <td></td>
                <td>
                    <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=363964117019670";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>



                    <div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false" data-action="recommend" data-font="arial"></div>
                </td>
            </tr>
                <?endif;?>
        </table>
    </div>
</div>
<div id="rating" data-placement="left">
    <a id="rating-a" class="button gray m_tooltip" data-animation="Ты можешь проголосовать за это заведение" title="Ты можешь проголосовать за это заведение" href="#"><i class="icon-star"></i>
        <?=$arResult["RATING"]?> <?=declOfNum($arResult["RATING"], array("голос", "голосов", "голоса"))?>
    </a>
</div>