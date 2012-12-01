<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Main\Localization;

use Bitrix\Main\Entity;

class CultureEntity extends Entity\Base
{
	public function initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_culture';

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'CODE' => array(
				'data_type' => 'string',
			),
			'NAME' => array(
				'data_type' => 'string',
			),
			'FORMAT_DATE' => array(
				'data_type' => 'string',
			),
			'FORMAT_DATETIME' => array(
				'data_type' => 'string',
			),
			'FORMAT_NAME' => array(
				'data_type' => 'string',
			),
			'WEEK_START' => array(
				'data_type' => 'integer',
			),
			'CHARSET' => array(
				'data_type' => 'string',
			),
			'DIRECTION' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
			),
		);
	}
}

