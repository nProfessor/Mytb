<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$name = "";
$text = "";
$img  = "";

$arLinks = array();
$step    = isset($_GET["step"])
    ? intval($_GET["step"])
    : 10;


//$r=file_get_contents("http://www.nightspirit.ru/club/show/id/44");
$r = file_get_contents("http://menu.ru/type/182-195-200-385409/page/$step");

if (!preg_match("#поиск не дал результатов#is", $r)) {
    preg_match_all('#/places/show/place/([0-9]+)#is', $r, $ar);

    foreach ($ar[1] as $var) {
        $arLinksID[$var] = $var;
    }

    foreach ($arLinksID as $ID) {
         $data=array();
        $club = file_get_contents("http://menu.ru/places/show/place/$ID");

        //Название клуба
        preg_match('#<h1>.*?</h1>#is', $club, $ar);
        $clubName = str_replace('style="font-size:50%"', 'class="title-eng"', $ar[0]);
        $clubName = str_replace(array("<h1>", "</h1>"), array(""), $clubName);


        if(preg_match('#>(.*?)<#is', $clubName, $arClubName2)){
            $clubName2=$arClubName2[1];
        }else{
             $clubName2="";
        }

        $clubName = trim(preg_replace("#<.*>#","", $clubName));


        //Телефоны
        preg_match('#<div class="phones_icons">.*?</div>#is', $club, $ar);
        preg_match_all('#7 \([0-9]{3}\) [0-9-]+#is', $ar[0], $phone);
        foreach ($phone as $var) {
            $phoneList[$var] = $var;
        }

        preg_match('#http://.*?\.(ru|com|net|org)#i', $ar[0], $url);
        $urlClub=$url[0];

        //адрес
        preg_match('#<a.*?class="address_link".*?>(.*?)</a>#is', $club, $ar);
        $address = $ar[1];


        //метро
        preg_match('#<a href="/metro/[0-9]+">(.*?)</a>#is', $club, $ar);
        $mentro = $ar[1];


        //характеристики
        preg_match('#<div class="desc_items">(.*?)</div>#is', $club, $table);
        preg_match_all('#<tr>(.*?)</tr>#is', $table[1], $h);

        foreach ($h[1] as $var) {
            preg_match_all('#>([^<>]*?)</#is', $var, $td);

            $arH          = $td[1];
            $har[$arH[0]] = $arH[1];
            /*
             [Кухня] => Тайская
             [Счет] => 2000 - 3000 р.
             [Время работы] => Пн-Пт: 10:00 - 06:00, Сб-Вс: 12:00 - 06:00
             [Бизнес-ланч] => с 14:00 до 16:00
            */
        }


        //информация
        preg_match('#<div class="rest_desc">(.*?)<div#is', $club, $info);
        $clubInfo = $info[1];


        //Музыка Жанр
        $musik = array(
            "Техно"          => 15,
            "House"          => 16,
            "Trans"          => 17,
            "Поп"            => 18,
            "Launge"         => 19,
            "R&amp;B"        => 20,
            "Поп-Рок"        => 21,
            "DJ's"           => 41,
            "Живая"          => 42,
            "Джаз/Блюз"      => 43,
            "Рок"            => 44,
            "Авторская песня"=> 45,
        );
        preg_match_all('#<a href="/music/[0-9]+">(.*?)</a>#is', $club, $mus);

        foreach ($mus[1] as $var) {

            if ($var == "Поп/Рок") {
                $musList[] = 18;
                $musList[] = 44;
            }

            if (isset($musik[$var])) {
                $musList[] = trim($musik[$var]);
            }

        }


        $data["NAME"]=$clubName." ".$clubName2;
        $data["DETAIL_TEXT"]=$clubInfo;
        $data["ACTIVE"]="N";
        $data["PROPERTY_VALUES"]["ADDRESS"]=$address;
        $data["PROPERTY_VALUES"]["METRO"]=$mentro;
        $data["PROPERTY_VALUES"]["PHONE"]=$phoneList;
        $data["PROPERTY_VALUES"]["TIME_WORKING"]=$har["Время работы"];
        $data["PROPERTY_VALUES"]["SITE"]=$urlClub;
        $data["PROPERTY_VALUES"]["ID_CLUB"]=$ID;
        $data["PROPERTY_VALUES"]["LINK_DATA"]="http://menu.ru/places/show/place/$ID";
        $data["CODE"]="menu_ru";

        $data["IBLOCK_ID"]=6;
        $data["PROPERTY_VALUES"]["MUSIC"]=$musList;




        $arFilter = array(
            "IBLOCK_ID" => 6,
            array(
                "LOGIC" => "OR",
                array("NAME" => "%".$clubName."%"),
                array("NAME" => "%".str_replace(array(")","("),"",$clubName2."%")),
                array("PROPERTY_SITE" => $urlClub),
            ),
        );


        $res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter, Array("ID"));
        if($res->SelectedRowsCount()==0){
                    $el = new CIBlockElement;
                    $el->Add($data);
            echo "ok";
        }
        else{
            echo  $data["NAME"]."\n";
        }



//


    }



    /*
*
*

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
        } elseif ($var == 'Кредитные карты') {
            $data["PROPERTY_VALUES"][$arHHH[$val]] = $var == "принимают"
                ? 7
                : 8;
        } else {
            $data["PROPERTY_VALUES"][$arHHH[$val]] = trim($var);
        }
    }

    $data["PROPERTY_VALUES"]["ID_CLUB"] = $i;





    $imgDIR = preg_replace("#[^/]+\.[a-z]{1,3}#is", "", str_replace("http://www.nightspirit.ru", "", $img));

    if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/tmp" . $imgDIR)) {

        mkdir($_SERVER["DOCUMENT_ROOT"] . "/tmp" . $imgDIR);
    }

    $imgContent = file_get_contents($img);
    echo str_replace("http://www.nightspirit.ru", "", $img);
    file_put_contents(
        $_SERVER["DOCUMENT_ROOT"] . "/tmp" . str_replace("http://www.nightspirit.ru", "", $img), $imgContent);

    $data["PREVIEW_PICTURE"] = CFile::MakeFileArray(
        $_SERVER["DOCUMENT_ROOT"] . "/tmp" . str_replace("http://www.nightspirit.ru", "", $img));

    $data["IBLOCK_ID"] = 6;

    $musik["Техно"]   = 15;
    $musik["House"]   = 16;
    $musik["Trans"]   = 17;
    $musik["Поп"]     = 18;
    $musik["Launge"]  = 19;
    $musik["R&amp;B"] = 20;
    $musik["Поп-Рок"] = 21;

    $arcontrolf['Охрана']     = 24;
    $arcontrolf['Хостес']     = 25;
    $arcontrolf['Лояльно']    = 26;
    $arcontrolf['Строгий']    = 27;
    $arcontrolf['По спискам'] = 28;


    preg_match('#<a class="drink navigation_a".*?<span>(.*?)</span>.*?</a>#is', $r, $drink);
    $data["PROPERTY_VALUES"]["PRICE_COCKTAIL"] = $drink[1];

    preg_match('#<a class="controlf navigation_a".*?<span>(.*?)</span>.*?</a>#is', $r, $controlf);

    $data["PROPERTY_VALUES"]["FACE_CONTROL"] = $arcontrolf[trim($controlf[1])];


    preg_match('#<a class="genre navigation_a".*?<span>(.*?)</span>.*?</a>#is', $r, $genre);

    foreach (explode(",", $genre[1]) as $var) {
        if (trim($var) != "")
            $data["PROPERTY_VALUES"]["MUSIC"][] = $musik[trim($var)];
    }

    //print_r(implode(",",$genre));


    $el = new CIBlockElement;
    $el->Add($data);*/
}





?>

