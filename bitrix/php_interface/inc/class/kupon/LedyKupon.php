<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 27.10.12
 * Time: 13:43
 * To change this template use File | Settings | File Templates.
 */
class LedyKupon extends Kupon
{
    public $xml="http://www.ladykupon.ru/kuponator.xml";
    public $tags="ladykupon";
    public $parsePage="http://www.ladykupon.ru/category/rest";
    public $parseReg="#/shares/single/([0-9]+)#is";


}