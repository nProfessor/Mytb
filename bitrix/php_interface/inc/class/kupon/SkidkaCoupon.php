<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 01.11.12
 * Time: 21:10
 * To change this template use File | Settings | File Templates.
 */
class SkidkaCoupon extends Kupon
{
    public $xml="http://skidka-coupon.ru/xml4";
    public $parsePage="http://skidka-coupon.ru/restaurant";
    public $parseReg="#/component/coupon/event/([0-9]+)#is";
    public $tags="skidkacoupon";
}