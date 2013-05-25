<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Самые популярные заведения");
?>
<p><?$APPLICATION->IncludeComponent("mytb:club.list.popular", ".default", array(
),
    false
);?></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>