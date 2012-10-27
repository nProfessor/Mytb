<?
IncludeModuleLangFile(__FILE__);
class CBitrixCloudCDNWebService
{
	private $domain = "";
	/**
	 *
	 * @param string $domain
	 * @return void
	 *
	 */
	public function __construct($domain)
	{
		$this->domain = $domain;
	}
	/**
	 * Returns URL to update policy
	 *
	 * @param array[string]string $arParams
	 * @return string
	 *
	 */
	private function getActionURL($arParams = /*.(array[string]string).*/ array())
	{
		$arErrors = /*.(array[int]string).*/ array();
		$domainTmp = CBXPunycode::ToASCII($this->domain, $arErrors);
		if (strlen($domainTmp) > 0)
			$domain = $domainTmp;
		else
			$domain = $this->domain;

		$arParams["license"] = md5(LICENSE_KEY);
		$arParams["domain"] = $domain;
		$url = COption::GetOptionString("bitrixcloud", "cdn_policy_url");
		$url = CHTTP::urlAddParams($url, $arParams, array(
			"encode" => true,
		));
		return $url;
	}
	/**
	 * Returns action response XML
	 *
	 * @param string $action
	 * @return CDataXML
	 *
	 */
	private function action($action) /*. throws CBitrixCloudException .*/
	{
		global $APPLICATION;
		$url = $this->getActionURL(array(
			"action" => $action,
		));
		$server = new CHTTP;
		$strXML = $server->Get($url);
		if ($strXML === false)
		{
			$e = $APPLICATION->GetException();
			if (is_object($e))
				throw new CBitrixCloudException($e->GetString());
			else
				throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
					"#STATUS#" => "-1",
				)));
		}
		if ($server->status != 200)
		{
			throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
				"#STATUS#" => (string)$server->status,
			)));
		}
		$obXML = new CDataXML;
		if (!$obXML->LoadString($strXML))
		{
			throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_XML_PARSE", array(
				"#CODE#" => "1",
			)));
		}
		$node = $obXML->SelectNodes("/error/code");
		if (is_object($node))
		{
			$error_code = $node->textContent();
			if (HasMessage("BCL_CDN_WS_ERROR_".$error_code))
				throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_ERROR_".$error_code), $error_code);
			else
				throw new CBitrixCloudException(GetMessage("BCL_CDN_WS_SERVER", array(
					"#STATUS#" => $error_code,
				)), $error_code);
		}
		return $obXML;
	}
	/**
	 *
	 * @return CDataXML
	 *
	 */
	public function actionQuota() /*. throws CBitrixCloudException .*/
	{
		return $this->action("get_quota_info");
	}
	/**
	 *
	 * @return CDataXML
	 *
	 */
	public function actionStop() /*. throws CBitrixCloudException .*/
	{
		return $this->action("stop");
	}
	/**
	 *
	 * @return CDataXML
	 *
	 */
	public function actionGetConfig() /*. throws CBitrixCloudException .*/
	{
		return $this->action("");
	}
}
?>