<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 11.08.12
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */


function dateShowText($date)
{
    $now              = date("d.m.Y");
    $tomorrow         = date("d.m.Y", strtotime("+1 day"));
    $dayAfterTomorrow = date("d.m.Y", strtotime("+2 day"));

    switch ($date) {
        case $now:
            return "Сегодня";
            break;
        case $tomorrow:
            return "Завтра";
            break;
        case $dayAfterTomorrow:
            return "После завтра";
            break;
    }
    return FALSE;
}


function GetShortUrl($url,$server=false){
    $show = false;
    $rsData = CBXShortUri::GetList(Array(),Array());
    while($arRes = $rsData->Fetch()) {
        if ($arRes["URI"] == $url){
            $str_SHORT_URI = $arRes["SHORT_URI"];
            $show = true;
        }
    }
    if ($show){
        return $server?'http://'.$_SERVER['SERVER_NAME'].'/'.$str_SHORT_URI:$str_SHORT_URI;
    }else{
        $str_SHORT_URI = CBXShortUri::GenerateShortUri();
        $arFields = Array(
            "URI" => $url,
            "SHORT_URI" => $str_SHORT_URI,
            "STATUS" => "301",
        );
        $ID = CBXShortUri::Add($arFields);
        return $server?'http://'.$_SERVER['SERVER_NAME'].'/'.$str_SHORT_URI:$str_SHORT_URI;
    }
}


/**
 * выводит стоимость  в нужном формате
 * @param $price
 */
function formatPrice($price, $curr = NULL)
{
    if ($curr != NULL) {
        return number_format($price, 2, ',', ' ') . " {$curr}";
    }
    return number_format($price, 2, ',', ' ');
}


/**
 * Функция склонения числительных в русском языке
 *
 * @param int    $number Число которое нужно просклонять
 * @param array  $titles Массив слов для склонения
 *
 * @return string
 **/
function declOfNum($number, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($number % 100 > 4 && $number % 100 < 20)
        ? 2
        : $cases[min($number % 10, 5)]];
}


/**
 * транслит для файлов
 */
function translate($text)
{
    $rus  = array("а", "б", "в",
                  "г", "ґ", "д", "е", "ё", "ж",
                  "з", "и", "й", "к", "л", "м",
                  "н", "о", "п", "р", "с", "т",
                  "у", "ф", "х", "ц", "ч", "ш",
                  "щ", "ы", "э", "ю", "я", "ь",
                  "ъ", "і", "ї", "є", "А", "Б",
                  "В", "Г", "ґ", "Д", "Е", "Ё",
                  "Ж", "З", "И", "Й", "К", "Л",
                  "М", "Н", "О", "П", "Р", "С",
                  "Т", "У", "Ф", "Х", "Ц", "Ч",
                  "Ш", "Щ", "Ы", "Э", "Ю", "Я",
                  "Ь", "Ъ", "І", "Ї", "Є", " ", "'", '"');
    $lat  = array("a", "b", "v",
                  "g", "g", "d", "e", "e", "zh", "z", "i",
                  "j", "k", "l", "m", "n", "o", "p", "r",
                  "s", "t", "u", "f", "h", "c", "ch", "sh",
                  "sh'", "y", "e", "yu", "ya", "_", "_", "i",
                  "i", "e", "A", "B", "V", "G", "G", "D",
                  "E", "E", "ZH", "Z", "I", "J", "K", "L",
                  "M", "N", "O", "P", "R", "S", "T", "U",
                  "F", "H", "C", "CH", "SH", "SH'", "Y", "E",
                  "YU", "YA", "_", "_", "I", "I", "E", "_", "", "");
    $text = str_replace($rus, $lat, $text);
    return (preg_replace("#[^a-z0-9._-]#i", "", $text));
}

/**
 * сли картинки нет то создаем ее с таким размером нет
 * @param $path
 * @param $size
 *
 * @return string
 */
function imgurl($path, $size,$resize=false)
{

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $path)) {
        return "";
    }
    preg_match("#^(.*)([^/]*)\.([a-z]{2,4})$#i", $path, $NAME_1);

    $size["w"]=intval($size["w"]);
    $size["h"]=intval($size["h"]);

    list( $width , $height ) = getimagesize( $_SERVER["DOCUMENT_ROOT"] . $path );


    if($size["w"]==0){
        $xscale=$height/$size["h"];
        $size["w"]=intval($width/$xscale);
    }

    if($size["h"]==0){
        $xscale=$width/$size["w"];
        $size["h"]=intval($height/$xscale);
    }

    $imgFile = "{$NAME_1[1]}{$NAME_1[2]}-resize-{$size["w"]}x{$size["h"]}.{$NAME_1[3]}";

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $imgFile)) {
        try {
            $image = new Imagick($_SERVER["DOCUMENT_ROOT"] . $path);
            $image->ResizeImage($size["w"], $size["h"],imagick::FILTER_LANCZOS, 0.9,true);
            $image->writeImages($_SERVER["DOCUMENT_ROOT"] . $imgFile, TRUE);
        } catch (ImagickException $e) {
            echo $e->getMessage();
        }
    }


    return $imgFile;
}


/**
 * возвращает ссылку на ту же страницу, добавляя ему гет параметры
 * @param array $params
 *
 * @return mixed
 */
function setGetParamsURL($params = array())
{
    $url = $_SERVER['REQUEST_URI'];

    $paramsURL = array_merge($_GET, $params);
    $i         = 0;
    foreach ($paramsURL as $val=> $var) {
        $url .= $i == 0
            ? "?{$val}={$var}"
            : "&{$val}={$var}";
        $i = 1;
    }
    return $url;
}

/**
 * генерация пароля
 *
 * @param $number
 *
 * @return string
 */
function generate_password($number, $complexity = "easy")
{


    $arr["easy"] = array('a', 'b', 'c', 'd', 'e', 'f',
                         'g', 'h', 'i', 'j', 'k', 'l',
                         'm', 'n', 'o', 'p', 'r', 's',
                         't', 'u', 'v', 'x', 'y', 'z');

    $arr["average"] = array_merge($arr["easy"], array('A', 'B', 'C', 'D', 'E', 'F',
                                                      'G', 'H', 'I', 'J', 'K', 'L',
                                                      'M', 'N', 'O', 'P', 'R', 'S',
                                                      'T', 'U', 'V', 'X', 'Y', 'Z',
                                                      '1', '2', '3', '4', '5', '6',
                                                      '7', '8', '9', '0'));

    $arr["complex"] = array_merge($arr["average"], array('.', ',', '(', ')', '[', ']', '!', '?',
                                                         '&', '^', '%', '@', '*', '$', '<', '>',
                                                         '/', '|', '+', '-', '{', '}', '`', '~'));

    if (!isset($arr[$complexity])) {
        $complexity = "easy";
    }


    // Генерируем пароль
    $pass = "";
    for ($i = 0; $i < $number; $i++) {
        // Вычисляем случайный индекс массива
        $index = rand(0, count($arr[$complexity]) - 1);

        $pass .= $arr[$complexity][$index];
    }


    return $pass;
}

/**
 * Дебагер
 * @param $data
 */
function printAr($data){
    echo "<pre>";
    echo "[".__LINE__."] ";
    print_r($data);
    echo "</pre>";
};