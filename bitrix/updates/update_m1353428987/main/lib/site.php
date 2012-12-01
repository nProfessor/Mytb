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
			'NAME' => array(
				'data_type' => 'string'
			),
			'SITE_NAME' => array(
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

