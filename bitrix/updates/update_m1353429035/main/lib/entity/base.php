<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */

namespace Bitrix\Main\Entity;

IncludeModuleLangFile(__FILE__);

/**
 * Base entity
 * @package bitrix
 * @subpackage main
 */
abstract class Base
{
	protected
		$className,
		$name,
		$dbTableName,
		$primary;

	protected
		$uf_id;

	/**
	 * @var Field
	 */
	protected $fields;

	protected
		$fieldsMap,
		$u_fields;

	protected
		$references;

	protected
		$filePath;

	protected static
		$instances;


	/**
	 * @static
	 *
	 * @param string $entityName
	 *
	 * @return Base
	 */
	public static function getInstance($entityName)
	{
		return self::getInstanceDirect($entityName . 'Entity');
	}


	protected static function getInstanceDirect($className)
	{
		if (empty(self::$instances[$className]))
		{
			self::$instances[$className] = new $className;
			self::$instances[$className]->initialize();
			self::$instances[$className]->postInitialize();
		}

		return self::$instances[$className];
	}

	/**
	 * Fields factory
	 * @param string $fieldName
	 * @param array  $fieldInfo
	 *
	 * @return BooleanField|ScalarField|ExpressionField|ReferenceField|UField
	 * @throws \Exception
	 */
	public function initializeField($fieldName, $fieldInfo)
	{
		if (!empty($fieldInfo['reference']))
		{
			if (strpos($fieldInfo['data_type'], '\\') === false)
			{
				// if reference has no namespace, then it'is in the same namespace
				$fieldInfo['data_type'] = $this->getNamespace().'\\'.$fieldInfo['data_type'];
			}

			$refEntity = Base::getInstance($fieldInfo['data_type']);
			$field = new ReferenceField($fieldName, $this, $refEntity, $fieldInfo['reference'], $fieldInfo);
		}
		elseif (!empty($fieldInfo['expression']))
		{
			$field = new ExpressionField($fieldName, $fieldInfo['data_type'], $this, $fieldInfo['expression'], $fieldInfo);
		}
		elseif (!empty($fieldInfo['USER_TYPE_ID']))
		{
			$field = new UField($fieldInfo, $this);
		}
		else
		{
			$fieldClass = Base::snake2camel($fieldInfo['data_type']) . 'Field';
			$fieldClass = __NAMESPACE__.'\\'.$fieldClass;

			if (strlen($fieldInfo['data_type']) && class_exists($fieldClass))
			{
				$field = new $fieldClass($fieldName, $fieldInfo['data_type'], $this, $fieldInfo);
			}
			else
			{
				throw new \Exception(sprintf(
					'Unknown data type "%s" found for `%s` field in %s Entity.',
					$fieldInfo['data_type'], $fieldName, $this->getName()
				));
			}
		}

		return $field;
	}


	abstract public function initialize();


	public function postInitialize()
	{
		// базовые свойства
		$classPath = explode('\\', $this->className);
		$this->name = substr(end($classPath), 0, -6);

		// default db table name
		if (is_null($this->dbTableName))
		{
			$_classPath = array_slice($classPath, 0, -1);

			$this->dbTableName = 'b_';

			foreach ($_classPath as $i => $_pathElem)
			{
				if ($i == 0 && $_pathElem == 'Bitrix')
				{
					// skip bitrix namespace
					continue;
				}

				if ($i == 1 && $_pathElem == 'Main')
				{
					// also skip Main module
					continue;
				}

				$this->dbTableName .= strtolower($_pathElem).'_';
			}

			// add class
			if ($this->name !== end($_classPath))
			{
				$this->dbTableName .= Base::camel2snake($this->name);
			}
			else
			{
				$this->dbTableName = substr($this->dbTableName, 0, -1);
			}
		}

		$this->primary = array();
		$this->references = array();

		if (empty($this->filePath))
		{
			throw new \Exception(sprintf(
				'Parameter `filePath` required for `%s` Entity', $this->name
			));
		}

		// инициализация атрибутов
		foreach ($this->fieldsMap as $fieldName => &$fieldInfo)
		{
			$field = $this->initializeField($fieldName, $fieldInfo);

			if ($field instanceof ReferenceField)
			{
				// references cache
				$this->references[strtolower($fieldInfo['data_type'])][] = $field;
			}

			$this->fields[$fieldName] = $field;

			if ($field instanceof ScalarField && $field->isPrimary())
			{
				$this->primary[] = $fieldName;
			}

			// add reference field for UField iblock_section
			if ($field instanceof UField && $field->getTypeId() == 'iblock_section')
			{
				$refFieldName = $field->GetName().'_BY';

				if ($field->isMultiple())
				{
					$localFieldName = $field->getValueFieldName();
				}
				else
				{
					$localFieldName = $field->GetName();
				}

				$newFieldInfo = array(
					'data_type' => 'Bitrix\Iblock\Section',
					'reference' => array($localFieldName, 'ID')
				);

				$refEntity = Base::getInstance($newFieldInfo['data_type']);
				$newRefField = new ReferenceField($refFieldName, $this, $refEntity, $newFieldInfo['reference'][0], $newFieldInfo['reference'][1]);

				$this->fields[$refFieldName] = $newRefField;
			}
		}

		if (empty($this->primary))
		{
			throw new \Exception(sprintf('Primary not found for %s Entity', $this->name));
		}
	}

	public function getReferencesCountTo($refEntityName)
	{
		if (array_key_exists($key = strtolower($refEntityName), $this->references))
		{
			return count($this->references[$key]);
		}

		return 0;
	}


	public function getReferencesTo($refEntityName)
	{
		if (array_key_exists($key = strtolower($refEntityName), $this->references))
		{
			return $this->references[$key];
		}

		return array();
	}


	// getters
	public function getFields()
	{
		return $this->fields;
	}


	/**
	 * @param $name
	 *
	 * @return Field
	 * @throws \Exception
	 */
	public function getField($name)
	{
		if ($this->hasField($name))
		{
			return $this->fields[$name];
		}

		throw new \Exception(sprintf(
			'%s Entity has no `%s` field.', $this->getName(), $name
		));
	}


	public function hasField($name)
	{
		return isset($this->fields[$name]);
	}


	public function getUField($name)
	{
		if ($this->hasUField($name))
		{
			return $this->u_fields[$name];
		}

		throw new \Exception(sprintf(
			'%s Entity has no `%s` userfield.', $this->getName(), $name
		));
	}


	public function hasUField($name)
	{
		if (is_null($this->u_fields))
		{
			$this->u_fields = array();

			if (strlen($this->uf_id))
			{
				/**
				 * @var $USER_FIELD_MANAGER CAllUserTypeManager
				 */
				global $USER_FIELD_MANAGER;

				foreach ($USER_FIELD_MANAGER->GetUserFields($this->uf_id) as $info)
				{
					$this->u_fields[$info['FIELD_NAME']] = new UField($info, $this);

					// add references for ufield (UF_DEPARTMENT_BY)
					if ($info['USER_TYPE_ID'] == 'iblock_section')
					{
						$info['FIELD_NAME'] .= '_BY';
						$this->u_fields[$info['FIELD_NAME']] = new UField($info, $this);
					}
				}
			}
		}

		return isset($this->u_fields[$name]);
	}


	public function getName()
	{
		return $this->name;
	}


	public function getNamespace()
	{
		return substr($this->className, 0, strrpos($this->className, '\\'));
	}

	public function getDataClass()
	{
		return substr($this->className, 0, -6);
	}

	public function getFilePath()
	{
		return $this->filePath;
	}


	public function getDBTableName()
	{
		return $this->dbTableName;
	}


	public function getPrimary()
	{
		return count($this->primary) == 1 ? $this->primary[0] : $this->primary;
	}


	public function getPrimaryArray()
	{
		return $this->primary;
	}

	public function isUts()
	{
		return false;
	}


	public function isUtm()
	{
		return false;
	}


	public function getUfId()
	{
		return $this->uf_id;
	}


	public static function isExists($name)
	{
		return class_exists($name . 'Entity');
	}

	public function getCode()
	{
		$code = '';

		// get absolute path to class
		$class_path = explode('\\', strtoupper($this->className));

		// cut class name to leave namespace only
		$class_path = array_slice($class_path, 0, -1);

		// cut Bitrix namespace
		if ($class_path[0] === 'BITRIX')
		{
			$class_path = array_slice($class_path, 1);
		}

		// glue module name
		if (count($class_path))
		{
			$code = join('_', $class_path).'_';
		}

		// glue entity name
		$code .= strtoupper(Base::camel2snake($this->getName()));

		return $code;
	}


	public function getLangCode()
	{
		return $this->getCode().'_ENTITY';
	}


	public static function camel2snake($str)
	{
		return strtolower(preg_replace('/(.)([A-Z])(.*?)/', '$1_$2$3', $str));
	}


	public static function snake2camel($str)
	{
		$str = str_replace('_', ' ', strtolower($str));
		return str_replace(' ', '', ucwords($str));
	}
	
	public static function getInstanceByQuery(Query $query, &$entity_name = null)
	{
		if (empty($entity_name))
		{
			$entity_name = 'Tmp'.randString();
		}

		$query_string = '('.$query->getQuery().')';
		$query_chains = $query->getChains();

		$replaced_aliases = array_flip($query->getReplacedAliases());

		// generate fieldsMap
		$fieldsMap = array('TMP_ID' => array('data_type' => 'integer', 'primary' => true));

		foreach ($query->getSelect() as $k => $v)
		{
			if (is_array($v))
			{
				$fieldsMap[$k] = array('data_type' => $v['data_type']);
			}
			else
			{
				$fieldsMap[$k] = array('data_type' => $query_chains[$k]->getLastElement()->getValue()->getDataType());
			}

			if (isset($replaced_aliases[$k]))
			{
				$fieldsMap[$k]['column_name'] = $replaced_aliases[$k];
			}
		}

		// generate class content
		$eval = 'class '.$entity_name.'Entity extends '.__CLASS__.' {'.PHP_EOL;
		$eval .= 'protected function __construct(){}'.PHP_EOL;
		$eval .= 'public function initialize() { $this->className = __CLASS__; $this->filePath = __FILE__;'.PHP_EOL;
		$eval .= '$this->dbTableName = '.var_export($query_string, true).';'.PHP_EOL;
		$eval .= '$this->fieldsMap = '.var_export($fieldsMap, true).';'.PHP_EOL;
		$eval .= '}}';

		eval($eval);

		return self::getInstance($entity_name);
	}
}
