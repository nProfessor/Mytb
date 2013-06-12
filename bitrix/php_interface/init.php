<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 11.08.12
 * Time: 17:45
 * To change this template use File | Settings | File Templates.
 */

//if(preg_match("#38\.99\.82\.[0-9]+#i",$_SERVER['REMOTE_ADDR']))
//{
//    die();
//}
CModule::IncludeModule("iblock");

CModule::AddAutoloadClasses(
    '',
    array(
        'Club' => '/bitrix/php_interface/inc/class/club/Club.php',
        'Stocks' => '/bitrix/php_interface/inc/class/club/Stocks.php',
        'Table' => '/bitrix/php_interface/inc/class/club/Table.php',
        'User' => '/bitrix/php_interface/inc/class/user/User.php',
        'Errors' => '/bitrix/php_interface/inc/class/errors/Errors.php',
        'Smsc' => '/bitrix/php_interface/inc/class/SMS/Smsc.php',
        'News' => '/bitrix/php_interface/inc/class/club/News.php',
        'Event' => '/bitrix/php_interface/inc/class/club/Event.php',
        'Article' => '/bitrix/php_interface/inc/class/article/Article.php',


        // купонные сервисы
        'Kupon' => '/bitrix/php_interface/inc/class/kupon/Kupon.php',
        'CityCoupon' => '/bitrix/php_interface/inc/class/kupon/CityCoupon.php',
        'KingCoupon' => '/bitrix/php_interface/inc/class/kupon/KingCoupon.php',
        'KuponAuktsion' => '/bitrix/php_interface/inc/class/kupon/KuponAuktsion.php',
        'LedyKupon' => '/bitrix/php_interface/inc/class/kupon/LedyKupon.php',
        'MyFant' => '/bitrix/php_interface/inc/class/kupon/MyFant.php',
        'Vigoda' => '/bitrix/php_interface/inc/class/kupon/Vigoda.php',
        'MegaKupon' => '/bitrix/php_interface/inc/class/kupon/MegaKupon.php',
        'BigCoupon' => '/bitrix/php_interface/inc/class/kupon/BigCoupon.php',
        'SKuponom' => '/bitrix/php_interface/inc/class/kupon/SKuponom.php',
        'SkidkaCoupon' => '/bitrix/php_interface/inc/class/kupon/SkidkaCoupon.php',
        'DariKupon' => '/bitrix/php_interface/inc/class/kupon/DariKupon.php',
        'SkidkaBum' => '/bitrix/php_interface/inc/class/kupon/SkidkaBum.php',
        'ZoomBonus' => '/bitrix/php_interface/inc/class/kupon/ZoomBonus.php',
        'Test' => '/bitrix/php_interface/inc/class/kupon/Test.php',

    )
);


$file = scandir(dirname(__FILE__) . "/inc/events/");

foreach ($file as $var) {
    if ($var != "." && $var != "..") {
        require_once(dirname(__FILE__) . "/inc/events/" . $var);
    }
}

$fileAgent = scandir($_SERVER["DOCUMENT_ROOT"]. "/bitrix/ui/agent/");

foreach ($fileAgent as $var) {
    if ($var != "." && $var != "..") {
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/ui/agent/" . $var);
    }
}


include_once(dirname(__FILE__) . "/inc/functions.php");