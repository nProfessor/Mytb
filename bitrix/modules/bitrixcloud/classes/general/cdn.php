<?
IncludeModuleLangFile(__FILE__);
class CBitrixCloudCDN
{
	private static $config = /*.(CBitrixCloudCDNConfig).*/ null;
	private static $proto = "";
	private static $domain_changed = false;
	/**
	 *
	 * @param string &$content
	 * @return void
	 *
	 */
	public function OnEndBufferContent(&$content)
	{
		if (isset($_GET["nocdn"]))
			return;

		CBitrixCloudCDN::$proto = CMain::IsHTTPS() ? "https" : "http";
		CBitrixCloudCDN::$config = CBitrixCloudCDNConfig::getInstance()->loadFromOptions();

		if (CBitrixCloudCDN::$config->isExpired())
		{
			if (CBitrixCloudCDN::$config->lock())
			{
				try
				{
					try
					{
						$delayExpiration = true;
						CBitrixCloudCDN::$config = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
						CBitrixCloudCDN::$config->saveToOptions();
						CBitrixCloudCDN::$config->unlock();
					}
					catch(CBitrixCloudException $e)
					{
						//In case of documented XML error we'll disable CDN
						if($e->getErrorCode() !== "")
						{
							CBitrixCloudCDN::SetActive(false);
							$delayExpiration = false;
						}
						throw $e;
					}
				}
				catch(exception $e)
				{
					if($delayExpiration)
						self::setExpired(time() + 1800);
					CAdminNotify::Add(array(
						"MESSAGE" => GetMessage("BCL_CDN_NOTIFY", array(
							"#HREF#" => "/bitrix/admin/bitrixcloud_cdn.php?lang=".LANGUAGE_ID,
						)),
						"TAG" => "bitrixcloud_off",
						"MODULE_ID" => "bitrixcloud",
						"ENABLE_CLOSE" => "Y",
					));
					CBitrixCloudCDN::$config->unlock();
					return;
				}
			}
		}
		$sites = CBitrixCloudCDN::$config->getSites();
		if (defined("ADMIN_SECTION"))
		{
			if (!isset($sites["admin"]))
				return;
		}
		elseif (defined("SITE_ID"))
		{
			if (!isset($sites[SITE_ID]))
				return;
		}
		else
		{
			return;
		}
		$arPrefixes = array_map(array(
			"CBitrixCloudCDN",
			"_preg_quote",
		), CBitrixCloudCDN::$config->getLocationsPrefixes());
		$arExtensions = array_map(array(
			"CBitrixCloudCDN",
			"_preg_quote",
		), CBitrixCloudCDN::$config->getLocationsExtensions());
		if (!empty($arPrefixes) && !empty($arExtensions))
		{
			$prefix_regex = "(?:".implode("|", $arPrefixes).")";
			$extension_regex = "(?:".implode("|", $arExtensions).")";
			$regex = "/((?:href|src)=\")(".$prefix_regex.")([a-zA-Z0-9_.\\/-]+\\.)(".$extension_regex.")(\"|\\?\\d+\")/";
			$content = preg_replace_callback($regex, array(
				"CBitrixCloudCDN",
				"_filter",
			), $content);
		}
	}
	/**
	 *
	 * @return void
	 *
	 */
	public function domainChanged()
	{
		CBitrixCloudCDN::$domain_changed = true;
	}
	/**
	 *
	 * @param string $str
	 * @return string
	 *
	 */
	private function _preg_quote($str)
	{
		return preg_quote($str, "/");
	}
	/**
	 *
	 * @param array[int]string $match
	 * @return string
	 *
	 */
	private function _filter($match)
	{
		$prefix = $match[2];
		$link = $match[3];
		$extension = $match[4];
		$params = $match[5];
		$location = /*.(CBitrixCloudCDNLocation).*/ null;
		foreach (CBitrixCloudCDN::$config->getLocations() as $location)
		{
			if ($location->getProto() === CBitrixCloudCDN::$proto)
			{
				$server = $location->getServerNameByPrefixAndExtension($prefix, $extension, $link);
				if ($server !== "")
				{
					if ($params === '"')
					{
						if (file_exists($_SERVER["DOCUMENT_ROOT"].$prefix.$link.$extension))
							$params = "?".filemtime($_SERVER["DOCUMENT_ROOT"].$prefix.$link.$extension).$params;
					}
					return $match[1]."//".$server.$prefix.$link.$extension.$params;
				}
			}
		}
		return $match[0];
	}
	/**
	 *
	 * @return void
	 *
	 */
	private static function stop() /*. throws Exception .*/
	{
		$o = CBitrixCloudCDNConfig::getInstance()->loadFromOptions();
		$a = new CBitrixCloudCDNWebService($o->getDomain());
		$a->actionStop();
	}
	/**
	 *
	 * @return bool
	 *
	 */
	public function IsActive()
	{
		$bActive = false;
		foreach (GetModuleEvents("main", "OnEndBufferContent", true) as $arEvent)
		{
			if ($arEvent["TO_MODULE_ID"] === "bitrixcloud" && $arEvent["TO_CLASS"] === "CBitrixCloudCDN")
			{
				$bActive = true;
				break;
			}
		}
		return $bActive;
	}
	/**
	 *
	 * @param bool $bActive
	 * @return bool
	 *
	 */
	public function SetActive($bActive)
	{
		global $APPLICATION;
		if ($bActive)
		{
			if (!CBitrixCloudCDN::IsActive())
			{
				try
				{
					$o = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
					$o->saveToOptions();
					RegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
					CBitrixCloudCDN::$domain_changed = false;
					CAdminNotify::DeleteByTag("bitrixcloud_off");
				}
				catch(exception $e)
				{
					$ex = new CApplicationException($e->getMessage());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
			elseif (CBitrixCloudCDN::$domain_changed)
			{
				try
				{
					$o = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
					$o->saveToOptions();
					CBitrixCloudCDN::$domain_changed = false;
				}
				catch(exception $e)
				{
					$ex = new CApplicationException($e->getMessage());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
		}
		else
		{
			if (CBitrixCloudCDN::IsActive())
			{
				try
				{
					CBitrixCloudCDN::stop();
					UnRegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
				}
				catch(exception $e)
				{
					UnRegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
					$ex = new CApplicationException($e->getMessage());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
		}
		return true;
	}
}
?>