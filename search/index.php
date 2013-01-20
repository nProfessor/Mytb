<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");?>

<?$APPLICATION->IncludeComponent("bitrix:search.page", "clear", Array(
	"RESTART"	=>	"Y",
	"CHECK_DATES"	=>	"N",
	"AJAX"=>"Y",
    "AJAX_OPTION_SHADOW"=>"Y",
    "AJAX_OPTION_HISTORY"=>"Y",
    "NO_WORD_LOGIC"=>"Y",
    "arrFILTER"=>array("iblock_club"),

	"SHOW_WHERE"	=>	"N",
	"PAGE_RESULT_COUNT"	=>	"10",
    "PAGER_TEMPLATE"=> "modern",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"TAGS_SORT"	=>	"NAME",
	"TAGS_PAGE_ELEMENTS"	=>	"20",
	"TAGS_PERIOD"	=>	"",
	"TAGS_URL_SEARCH"	=>	"",
	"TAGS_INHERIT"	=>	"Y",
	"FONT_MAX"	=>	"50",
	"FONT_MIN"	=>	"10",
	"COLOR_NEW"	=>	"000000",
	"COLOR_OLD"	=>	"C8C8C8",
	"PERIOD_NEW_TAGS"	=>	"",
	"SHOW_CHAIN"	=>	"Y",
	"COLOR_TYPE"	=>	"Y",
	"WIDTH"	=>	"100%"
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>