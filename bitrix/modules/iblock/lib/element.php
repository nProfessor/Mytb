<?php

namespace Bitrix\Iblock;

use Bitrix\Main\Entity;

class ElementEntity extends Entity\Base
{
	protected function __construct() {}

	public function Initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_iblock_element';

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'NAME' => array(
				'data_type' => 'string'
			),
			'IBLOCK_ID' => array(
				'data_type' => 'integer'
			),
			'IBLOCK' => array(
				'data_type' => 'Iblock',
				'reference' => array('=this.IBLOCK_ID' => 'ref.ID')
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y')
			)
		);
	}
}
?>
