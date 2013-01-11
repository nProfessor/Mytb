<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 27.10.12
 * Time: 13:44
 * To change this template use File | Settings | File Templates.
 */
class Vigoda extends Kupon
{
    public $xml="http://vigoda.ru/api/xml/mytb";
    public $tags="vigoda";
    public $parsePage="http://vigoda.ru/cafe/";
    public $parseReg="#http://vigoda.ru/cafe/offer/([0-9]+)#is";
}