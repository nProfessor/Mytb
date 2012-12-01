<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Main;

use Bitrix\Main\Entity;

class SiteEntity extends Entity\Base
{
	protected function __construct() {}

	public function initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_lang';

		$this->fieldsMap = array(
			'LID' => array(
				'data_type' => 'string',
				'primary' => true
			),
			'SORT' => array(
				'data_type' => 'integer',
			),
			'DEF' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
			),
			'NAME' => array(
				'data_type' => 'string'
			),
			'DIR' => array(
				'data_type' => 'string'
			),
			'LANGUAGE_ID' => array(
				'data_type' => 'string',
			),
			'DOC_ROOT' => array(
				'data_type' => 'string',
			),
			'DOMAIN_LIMITED' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
			),
			'SERVER_NAME' => array(
				'data_type' => 'string'
			),
			'SITE_NAME' => array(
				'data_type' => 'string'
			),
			'EMAIL' => array(
				'data_type' => 'string'
			),
			'CULTURE_ID' => array(
				'data_type' => 'integer',
			),
			'CULTURE' => array(
				'data_type' => 'Bitrix\Main\Localization\Culture',
				'reference' => array('=this.CULTURE_ID' => 'ref.ID'),
			),
		);
	}
}

class Site extends Entity\DataManager
{
}
