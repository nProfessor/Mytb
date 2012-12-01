<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2012 Bitrix
 */

namespace Bitrix\Main\Entity;

/**
 * Base entity data manager
 * @package bitrix
 * @subpackage main
 */
abstract class DataManager
{
	/**
	 * @var Base
	 */
	protected static $entity;

	/**
	 * @var array
	 */
	protected static $errors;

	/**
	 * @static
	 * @return Base
	 */
	public static function getEntity()
	{
		$class = get_called_class();

		if (!isset(static::$entity[$class]))
		{
			static::$entity[$class] = Base::getInstance(get_called_class());
		}

		return static::$entity[$class];
	}

	public static function getByPrimary($primary, $parameters = array())
	{
		static::normalizePrimary($primary);
		static::validatePrimary($primary);

		$primaryFilter = array();

		foreach ($primary as $k => $v)
		{
			$primaryFilter['='.$k] = $v;
		}

		if (isset($parameters['filter']))
		{
			$parameters['filter'] = array($primaryFilter, $parameters['filter']);
		}
		else
		{
			$parameters['filter'] = $primaryFilter;
		}

		return static::getList($parameters);
	}

	public static function getById($id)
	{
		return static::getByPrimary($id);
	}

	public static function getRow($parameters)
	{
		$result = static::getList($parameters);
		$row = $result->fetch();

		return is_array($row) ? $row : null;
	}

	public static function getList($parameters = array())
	{
		$query = new Query(static::getEntity());

		if (isset($parameters['select']))
		{
			$query->setSelect($parameters['select']);
		}
		else
		{
			$query->setSelect(array('*'));
		}

		if (isset($parameters['filter']))
		{
			$query->setFilter($parameters['filter']);
		}

		if (isset($parameters['group']))
		{
			$query->setGroup($parameters['group']);
		}

		if (isset($parameters['order']))
		{
			$query->setOrder($parameters['order']);
		}

		if (isset($parameters['limit']))
		{
			$query->setLimit($parameters['limit']);
		}

		if (isset($parameters['offset']))
		{
			$query->setOffset($parameters['offset']);
		}

		if (isset($parameters['count_total']))
		{
			$query->countTotal($parameters['count_total']);
		}

		if (isset($parameters['options']))
		{
			$query->setOptions($parameters['options']);
		}

		if (isset($parameters['runtime']))
		{
			foreach ($parameters['runtime'] as $name => $fieldInfo)
			{
				$query->registerRuntimeField($name, $fieldInfo);
			}
		}

		if (isset($parameters['data_doubling']))
		{
			$parameters['data_doubling'] ? $query->enableDataDoubling() : $query->disableDataDoubling();
		}


		return $query->exec();

		// return array?
	}

	public static function query()
	{
		return new Query(static::getEntity());
	}

	protected static function normalizePrimary(&$primary, $data = array())
	{

		$entity = static::getEntity();
		$entity_primary = $entity->getPrimaryArray();

		if ($primary === null)
		{
			$primary = array();

			// extract primary from data array
			foreach ($entity_primary as $key)
			{
				if ($entity->getField($key)->isAutocomplete())
				{
					continue;
				}

				if (!isset($data[$key]))
				{
					throw new \Exception(sprintf(
						'Primary `%s` was not found when trying to update %s row.', $key, static::getEntity()->getName()
					));
				}

				$primary[$key] = $data[$key];
			}
		}
		elseif (is_scalar($primary))
		{
			if (count($entity_primary) > 1)
			{
				throw new \Exception(sprintf(
					'Require multi primary {`%s`}, but one scalar value "%s" found when trying to update %s row.',
					join('`, `', $entity_primary), $primary, static::getEntity()->getName()
				));
			}

			$primary = array($entity_primary[0] => $primary);
		}
	}

	protected static function validatePrimary($primary)
	{
		if (is_array($primary))
		{
			$entity_primary = static::getEntity()->getPrimaryArray();

			foreach (array_keys($primary) as $key)
			{
				if (!in_array($key, $entity_primary, true))
				{
					throw new \Exception(sprintf(
						'Unknown primary `%s` found when trying to query %s row.',
						$key, static::getEntity()->getName()
					));
				}
			}
		}
		else
		{
			throw new \Exception(sprintf(
				'Unknown type of primary "%s" found when trying to query %s row.', gettype($primary), static::getEntity()->getName()
			));
		}

		// primary values validation
		foreach ($primary as $key => $value)
		{
			if (!is_scalar($value))
			{
				throw new \Exception(sprintf(
					'Unknown value type "%s" for primary "%s" found when trying to query %s row.',
					gettype($value), $key, static::getEntity()->getName()
				));
			}
		}
	}

	protected static function checkFieldsBeforeAdd($data, $throwException = false)
	{
		// check required fields
		foreach (static::getEntity()->getFields() as $field)
		{
			if ($field instanceof ScalarField && $field->isRequired())
			{
				if (!isset($data[$field->getName()]))
				{
					static::registerError('Required '.$field->getName(), $throwException, $field->getName());
				}
			}
		}
	}

	protected static function checkFieldsBeforeUpdate($data, $throwException = false)
	{
	}

	public static function checkFields($data, $action = 'update', $throwException = false)
	{
		if ($action === 'add')
		{
			static::checkFieldsBeforeAdd($data, true);
		}
		elseif ($action === 'update')
		{
			static::checkFieldsBeforeUpdate($data, true);
		}
		else
		{
			throw new \Exception(sprintf('Unknown action "%s" for %s', $action, __METHOD__));
		}

		// check data - fieldname & type & strlen etc.
		foreach ($data as $k => $v)
		{
			if (static::getEntity()->hasField($k) && static::getEntity()->getField($k) instanceof ScalarField)
			{
				$field = static::getEntity()->getField($k);
			}
			elseif (static::getEntity()->hasUField($k))
			{
				// should be continue
				// checking is inside uf manager
				$field = static::getEntity()->getUField($k);
			}
			else
			{
				throw new \Exception(sprintf(
					'Field `%s` not found in entity when trying to update %s row.',
					$k, static::getEntity()->getName()
				));
			}

			if (!$field->validateValue($v))
			{
				// get message from entity
				$errMsgCode = $field->getLangCode().'_INVALID';

				if (!HasMessage($errMsgCode))
				{
					// get default message
					$errMsgCode = 'MAIN_ENTITY_FIELD_INVALID';
				}

				$errMsgText = GetMessage($errMsgCode, array(
					"#FIELD_NAME#" => $field->getName(),
					"#FIELD_TITLE#" => $field->getLangText()
				));

				static::registerError($errMsgText, $throwException , $field->getName());
			}
		}

		if (!$throwException)
		{
			return static::getErrors();
		}

		/*if (static::getEntity()->getUfId() !== null)
		{
			$scalar_row_id = is_array($primary) && count($primary) == 1 ? end($primary) : null;

			if (!$GLOBALS["USER_FIELD_MANAGER"]->CheckFields(static::getEntity()->getUfId(), $scalar_row_id, $data))
			{
				global $APPLICATION;

				if(is_object($APPLICATION) && $APPLICATION->GetException())
				{
					$e = $APPLICATION->GetException();
					static::registerError($e->GetString(), $throwException);
					$APPLICATION->ResetException();
				}
				else
				{
					static::registerError('Unknown error', $throwException);
				}
			}
		}*/
	}

	public static function add(array $data)
	{
		// event PRE

		// check primary
		$primary = null;
		static::normalizePrimary($primary, $data);
		static::validatePrimary($primary);

		// check data
		static::checkFields($data, 'add', true);

		$tableName = static::getEntity()->getDBTableName();
		$clobFields = static::isolateClobNames($data);

		// save data
		$ID = $GLOBALS['DB']->Add($tableName, $data, $clobFields);

		// save Userfields

		// event POST

		return $ID;
	}

	public static function update(array $data, $primary)
	{
		// event PRE

		// check primary
		static::normalizePrimary($primary, $data);
		static::validatePrimary($primary);

		// check data
		static::checkFields($data, 'update', true);

		// save data
		$tableName = static::getEntity()->getDBTableName();
		$clobFields = static::isolateClobNames($data);

		$strUpdate = $GLOBALS['DB']->PrepareUpdate($tableName, $data);

		$strPrimary = array();
		foreach ($primary as $k => $v)
		{
			$strPrimary[] = $k . " = '" . $GLOBALS['DB']->ForSQL($v) . "'";
		}
		$strPrimary = join(' AND ', $strPrimary);

		$strSql = "UPDATE ".$tableName." SET ".$strUpdate." WHERE ".$strPrimary;

		$result = $GLOBALS['DB']->QueryBind(
			$strSql, $clobFields, false, "File: ".__FILE__."<br>Line: ".__LINE__
		);

		// save Userfields

		// event POST

		return $result;
	}

	public static function delete($primary)
	{
		// event PRE

		// check primary
		static::normalizePrimary($primary);
		static::validatePrimary($primary);

		// delete
		$tableName = static::getEntity()->getDBTableName();

		$strPrimary = array();
		foreach ($primary as $k => $v)
		{
			$strPrimary[] = $k . " = '" . $GLOBALS['DB']->ForSQL($v) . "'";
		}
		$strPrimary = join(' AND ', $strPrimary);

		$strSql = "DELETE FROM ".$tableName." WHERE ".$strPrimary;
		$result = $GLOBALS['DB']->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		// event POST
		return $result;
	}

	protected static function isolateClobNames($data)
	{
		$names = array();

		foreach (array_keys($data) as $k)
		{
			if (static::getEntity()->getField($k) instanceof TextField)
			{
				$names[] = $k;
			}
		}

		return $names;
	}

	protected static function registerError($error, $throwException = false, $fieldName = null)
	{
		if ($throwException)
		{
			throw new \Exception($error);
		}
		else
		{
			if ($fieldName !== null)
			{
				self::$errors[get_called_class()][$fieldName][] = $error;
			}
			else
			{
				self::$errors[get_called_class()]['common'][] = $error;
			}

			self::$errors[get_called_class()]['all'][] = $error;
		}
	}

	public static function getErrors()
	{
		if (isset(self::$errors[get_called_class()]))
		{
			$errors = self::$errors[get_called_class()];
			static::cleanErrors();

			return $errors;
		}
		else
		{
			return array();
		}
	}

	public static function cleanErrors()
	{
		if (isset(self::$errors[get_called_class()]))
		{
			self::$errors[get_called_class()] = array();
		}
	}
}