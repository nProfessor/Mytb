<?php

namespace Bitrix\Iblock;

use Bitrix\Main\Entity;

class SectionEntity extends Entity\Base
{
	protected function __construct() {}

	public function Initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_iblock_section';

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'NAME' => array(
				'data_type' => 'string'
			)
		);
	}
}
?>
