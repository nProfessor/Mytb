<?
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jquery.fineuploader/jquery.fineuploader-3.3.0.min.js");
$APPLICATION->SetAdditionalCSS("/jslibs/jquery.fineuploader/fineuploader-3.3.0.css");


$clubID = $arResult['clubID'];

?>

<div class="right w8">
    <div class="padding_l_25">

        <h1>Фотографии <?=$clubInfo['NAME']?></h1>
        <div id="fine-uploader"></div>

        <script>
            function createUploader() {
                var uploader = new qq.FineUploader({
                    element: document.getElementById('fine-uploader'),
                    debug:true,
                    success:true,
                    request: {
                        endpoint: '/bitrix/ui/ajax/upload/club_photos.php'
                    },
//                    validation: {
//                        allowedExtensions: ['jpeg', 'jpg', 'png',"gif"]
//                    },
                    text: {
                        uploadButton: '<i class="icon-plus icon-white"></i> Выбрать файлы'
                    },
                    failedUploadTextDisplay: {
                        mode: 'custom',
                        maxChars: 40,
                        responseProperty: 'error',
                        enableTooltip: true
                    }

                });
            }
            window.onload = createUploader;
        </script>

        <ul class="gallery_100x100">
        <?foreach($arResult['photo'] as $var):?>
        <li><img src="<?=imgurl($var["PATH"],array("w"=>100,"h"=>100))?>"/></li>
        <?endforeach;?>
        </ul>
     </div>
</div>

<div class="w2 left">
    <?$APPLICATION->IncludeComponent("club:menu.left", "", array(
        "CLUB_ID" => $clubID,
    ),
    false
);?>
</div>