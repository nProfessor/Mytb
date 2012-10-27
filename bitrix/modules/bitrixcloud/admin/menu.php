<?
IncludeModuleLangFile(__FILE__);
if ($USER->IsAdmin())
{
	return array(
		"parent_menu" => "global_menu_settings",
		"section" => "bitrixcloud",
		"sort" => 1645,
		"text" => GetMessage("BCL_MENU_ITEM"),
		"url" => "bitrixcloud_index.php?lang=".LANGUAGE_ID,
		"icon" => "bitrixcloud_menu_icon",
		"page_icon" => "bitrixcloud_page_icon",
		"items_id" => "menu_bitrixcloud",
		"items" => array(
			array(
				"text" => GetMessage("BCL_MENU_CONTROL_ITEM"),
				"url" => "bitrixcloud_cdn.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"bitrixcloud_cdn.php",
				),
			),
		),
	);
}
else
{
	return false;
}
?>
