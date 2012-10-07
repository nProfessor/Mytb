<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 05.09.12
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$count=(int)$_POST['count'];

$club=new Club(intval($_POST['clubID']));

$club->setNewRating($count,"VK");

echo '<i class="icon-star"></i>'.$count." ".declOfNum($count, array("голос", "голоса", "голосов"));