<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
die();
CModule::IncludeModule("iblock");

$name = "";
$text = "";
$img  = "";
if ($step >= 1000)
    die("все");
$step = $_GET['step'];
for ($i = $step; $i < ($step + 100); $i++) {

    //$r=file_get_contents("http://www.nightspirit.ru/club/show/id/44");
    $r = file_get_contents("http://www.nightspirit.ru/club/show/id/$i");

    if (!preg_match("#/images/front/tech_work\.jpg#is", $r)) {
        preg_match_all('#<div.*?>.*?</div>#is', $r, $ar);
        preg_match('#<h3>(.*?)</h3>#is', $r, $arName);

        $data["NAME"] = trim($arName[1]);

        preg_match_all('#<span style="font-size: small;">.*?</span>#is', $r, $arText);

        foreach ($arText[0] as $var) {
            if (mb_strlen($var) > 300) {
                $text = trim($var);
                break;
            }
        }

        $data["DETAIL_TEXT"] = $text;

        preg_match_all('#<img src="(http://www.nightspirit.ru/.*?)" />#is', $r, $arImg);
        $img = $arImg[1][0];

        preg_match('#<table class="list">.*?</table>#is', $r, $arContent);
        preg_match_all('#<tr>(.+?)</tr>#is', $arContent[0], $arTR);
        $ar_h = array();
        foreach ($arTR[1] as $var) {
            preg_match('#<th.*?>(.*?)</th>#is', $var, $arTH);
            preg_match('#<td>(.*?)</td>#is', $var, $arTD);
            $ar_h[$arTH[1]] = $arTD[1];
        }
        $arHHH["Адрес"]           = "ADDRESS";
        $arHHH["Метро"]           = "METRO";
        $arHHH["Телефон"]         = 'PHONE';
        $arHHH["Часы работы"]     = "TIME_WORKING";
        $arHHH["Ссылка на сайт"]  = "SITE";
        $arHHH["Кредитные карты"] = "CARDS";


        foreach ($ar_h as $val=> $var) {
            if ($var == 'Телефон') {
                $data["PROPERTY_VALUES"][$arHHH[$val]] = array(trim($var));
            }
            elseif ($var == 'Кредитные карты') {
                $data["PROPERTY_VALUES"][$arHHH[$val]] = $var == "принимают" ? 7 : 8;
            }
            else {
                $data["PROPERTY_VALUES"][$arHHH[$val]] = trim($var);
            }
        }

        $data["PROPERTY_VALUES"]["ID_CLUB"] = $i;


        /**
         * (
        [] => г. Москва, Покровка, дом 17, стр. 1
        [] => Чистые пруды
        [] => 8 (495) 624-07-18
        [] => пн-вс круглосуточно
        [Ссылка на сайт] => <a target="_blank" href="http://cubalibrebar.ru">cubalibrebar.ru</a>
         */


        $imgDIR = preg_replace("#[^/]+\.[a-z]{1,3}#is", "", str_replace("http://www.nightspirit.ru", "", $img));

        if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/tmp" . $imgDIR)) {

            mkdir($_SERVER["DOCUMENT_ROOT"] . "/tmp" . $imgDIR);
        }

        $imgContent = file_get_contents($img);
        echo str_replace("http://www.nightspirit.ru", "", $img);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/tmp" . str_replace("http://www.nightspirit.ru", "", $img), $imgContent);

        $data["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/tmp" . str_replace("http://www.nightspirit.ru", "", $img));


        $arFilter = Array(
            "IBLOCK_ID"    => IB_CLUB_ID,
            "PROPERTY_SITE"=> "%" . $data["PROPERTY_VALUES"]['SITE'] . "%");
        $arSelect = Array(
            "ID",
            "NAME"
        );
        if ($res = CIBlockElement::GetList(Array("SORT"=> "DESC"), $arFilter, FALSE, FALSE, $arSelect)->Fetch()) {
            $el                 = new CIBlockElement;
            $arLoadProductArray = Array(
                "DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/image.gif")
            );

            $res = $el->Update($res['ID'], $arLoadProductArray);
        }

        /*
$data["IBLOCK_ID"]=6;

$musik["Техно"]=15;
$musik["House"]=16;
$musik["Trans"]=17;
$musik["Поп"]=18;
$musik["Launge"]=19;
$musik["R&amp;B"]=20;
$musik["Поп-Рок"]=21;

$arcontrolf['Охрана']=24;
$arcontrolf['Хостес']=25;
$arcontrolf['Лояльно']=26;
$arcontrolf['Строгий']=27;
$arcontrolf['По спискам']=28;


preg_match('#<a class="drink navigation_a".*?<span>(.*?)</span>.*?</a>#is',$r,$drink);
$data["PROPERTY_VALUES"]["PRICE_COCKTAIL"]=$drink[1];

preg_match('#<a class="controlf navigation_a".*?<span>(.*?)</span>.*?</a>#is',$r,$controlf);

$data["PROPERTY_VALUES"]["FACE_CONTROL"]=$arcontrolf[trim($controlf[1])];


preg_match('#<a class="genre navigation_a".*?<span>(.*?)</span>.*?</a>#is',$r,$genre);

foreach(explode(",",$genre[1]) as $var){
    if(trim($var)!="")
$data["PROPERTY_VALUES"]["MUSIC"][]=$musik[trim($var)];
}

//print_r(implode(",",$genre));

*/

    }

}
header("Location: /bitrix/ui/ajax/curl.php?step=" . ($step + 101));

?>

