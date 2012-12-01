<?php

namespace Bitrix\Iblock;

use Bitrix\Main\Entity;

class IblockEntity extends Entity\Base
{
	protected function __construct() {}

	public function Initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_iblock';

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'IBLOCK_TYPE_ID' => array(
				'data_type' => 'string'
			)
		);
	}
}
