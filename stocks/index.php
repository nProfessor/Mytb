<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 08.10.12
 * Time: 21:35
 * To change this template use File | Settings | File Templates.
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Акции клубов, баров, ресторанов");


$APPLICATION->IncludeComponent("mytb:stocks", "", array(), false );


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
