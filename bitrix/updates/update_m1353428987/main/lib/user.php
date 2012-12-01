<?php

namespace Bitrix\Main;

use Bitrix\Main\Entity;

class UserEntity extends Entity\Base
{
	protected function __construct() {}

	public function initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->uf_id = 'USER';

		global $DB;

		$this->fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'LOGIN' => array(
				'data_type' => 'string'
			),
			'PASSWORD' => array(
				'data_type' => 'string'
			),
			'EMAIL' => array(
				'data_type' => 'string'
			),
			'ACTIVE' => array(
				'data_type' => 'boolean'
			),
			'DATE_REGISTER' => array(
				'data_type' => 'datetime'
			),
			'DATE_REG_SHORT' => array(
				'data_type' => 'datetime',
				'expression' => array(
					$DB->DatetimeToDateFunction('%s'), 'DATE_REGISTER'
				)
			),
			'NAME' => array(
				'data_type' => 'string'
			),
			'PERSONAL_PHONE' => array(
				'data_type' => 'string'
			),
			'PERSONAL_MOBILE' => array(
				'data_type' => 'string'
			),
			'SECOND_NAME' => array(
				'data_type' => 'string'
			),
			'LAST_NAME' => array(
				'data_type' => 'string'
			),
			'LID' => array(
				'data_type' => 'string'
			),
			'WORK_POSITION' => array(
				'data_type' => 'string'
			),
			'PERSONAL_BIRTHDAY' => array(
				'data_type' => 'date'
			),
			'SHORT_NAME' => array(
				'data_type' => 'string',
				'expression' => array(
					$DB->Concat("%s","' '", "UPPER(".$DB->Substr("%s", 1, 1).")", "'.'"),
					'LAST_NAME', 'NAME'
				)
			),
			'EXTERNAL_AUTH_ID' => array(
				'data_type' => 'string'
			),
			'UTS_OBJECT' => array(
				'data_type' => 'UtsUser',
				'reference' => array('=this.ID' => 'ref.VALUE_ID')
			)
		);
	}

}

class User extends Entity\DataManager
{
	public static function checkFieldsBeforeAdd($data)
	{
		$external_auth_id = null;

		if (!is_set($data, "EXTERNAL_AUTH_ID") || !strlen(trim($data["EXTERNAL_AUTH_ID"])))
		{
			if (!isset($data["LOGIN"]))
			{
				static::$errors[] = GetMessage("user_login_not_set");
			}

			if (!isset($data["PASSWORD"]))
			{
				static::$errors[] = GetMessage("user_pass_not_set");
			}

			if (!isset($data["EMAIL"]))
			{
				static::$errors[] = GetMessage("user_email_not_set");
			}

			static::checkInternalAuthFields($data);
		}
		else
		{
			$external_auth_id = $data["EXTERNAL_AUTH_ID"];
		}

		if (is_set($data, 'LOGIN'))
		{
			static::checkLoginExists($data["LOGIN"], null, $external_auth_id);
		}
	}

	public static function checkFieldsBeforeUpdate($data, $primary)
	{
		$external_auth_id = null;

		if (!is_set($data, "EXTERNAL_AUTH_ID"))
		{
			$user = static::getByPrimary($primary, array('select' => array('EXTERNAL_AUTH_ID')));

			if (is_array($user) && !strlen($user['EXTERNAL_AUTH_ID']))
			{
				static::checkInternalAuthFields($data);
			}
			else
			{
				$external_auth_id = $user['EXTERNAL_AUTH_ID'];
			}
		}
		else
		{
			$external_auth_id = $data['EXTERNAL_AUTH_ID'];
		}

		if (is_set($data, 'LOGIN'))
		{
			static::checkLoginExists($data["LOGIN"], $primary['ID'], $external_auth_id);
		}
	}

	protected static function checkInternalAuthFields($data, $primary = null)
	{
		if (is_set($data, "LOGIN") && $data["LOGIN"] != trim($data["LOGIN"]))
		{
			static::$errors[] = GetMessage("LOGIN_WHITESPACE");
		}

		if (is_set($data, "LOGIN") && strlen($data["LOGIN"])<3)
		{
			static::$errors[] = GetMessage("MIN_LOGIN");
		}

		if (is_set($data, "PASSWORD"))
		{
			if (array_key_exists("GROUP_ID", $data))
			{
				$groups = array();

				if (is_array($data["GROUP_ID"]))
				{
					foreach ($data["GROUP_ID"] as $group)
					{
						if (is_array($group))
						{
							$groups[] = $group["GROUP_ID"];
						}
						else
						{
							$groups[] = $group;
						}
					}
				}

				$policy = static::getGroupPolicy($groups);
			}
			elseif ($primary !== null)
			{
				$policy = static::getGroupPolicy($primary['ID']);
			}
			else
			{
				$policy = static::getGroupPolicy(array());
			}

			// вынести валидацию пароля в password entity field?  email аналогично
			$password_min_length = intval($policy["PASSWORD_LENGTH"]);

			if ($password_min_length <= 0)
			{
				$password_min_length = 6;
			}

			if (strlen($data["PASSWORD"]) < $password_min_length)
			{
				static::$errors[] = GetMessage("MAIN_FUNCTION_REGISTER_PASSWORD_LENGTH", array("#LENGTH#" => $policy["PASSWORD_LENGTH"]));
			}

			if (($policy["PASSWORD_UPPERCASE"] === "Y") && !preg_match("/[A-Z]/", $data["PASSWORD"]))
			{
				static::$errors[] = GetMessage("MAIN_FUNCTION_REGISTER_PASSWORD_UPPERCASE");
			}

			if (($policy["PASSWORD_LOWERCASE"] === "Y") && !preg_match("/[a-z]/", $data["PASSWORD"]))
			{
				static::$errors[] = GetMessage("MAIN_FUNCTION_REGISTER_PASSWORD_LOWERCASE");
			}

			if (($policy["PASSWORD_DIGITS"] === "Y") && !preg_match("/[0-9]/", $data["PASSWORD"]))
			{
				static::$errors[] = GetMessage("MAIN_FUNCTION_REGISTER_PASSWORD_DIGITS");
			}

			if (($policy["PASSWORD_PUNCTUATION"] === "Y") && !preg_match("/[,.<>\\/?;:'\"[\\]\{\}\\\\|`~!@#\$%^&*()_+=-]/", $data["PASSWORD"]))
			{
				static::$errors[] = GetMessage("MAIN_FUNCTION_REGISTER_PASSWORD_PUNCTUATION");
			}
		}

		if (is_set($data, "EMAIL"))
		{
			if (strlen($data["EMAIL"])<3 || !check_email($data["EMAIL"], true))
			{
				static::$errors[] = GetMessage("WRONG_EMAIL");
			}
			elseif (($primary === null) && COption::GetOptionString("main", "new_user_email_uniq_check", "N") === "Y")
			{
				$user = static::getRow(array('filter' => array('=EMAIL' => $data['EMAIL'])));

				if (is_array($user))
				{
					static::$errors[] = GetMessage("USER_WITH_EMAIL_EXIST", array("#EMAIL#" => htmlspecialchars($data["EMAIL"])));
				}
			}
		}

		if (is_set($data, "PASSWORD") && is_set($data, "CONFIRM_PASSWORD") && $data["PASSWORD"]!=$data["CONFIRM_PASSWORD"])
		{
			static::$errors[] = GetMessage("WRONG_CONFIRMATION");
		}

		if (is_array($data["GROUP_ID"]) && count($data["GROUP_ID"]) > 0)
		{
			if (is_array($data["GROUP_ID"][0]) && count($data["GROUP_ID"][0]) > 0)
			{
				foreach ($data["GROUP_ID"] as $group)
				{
					if (strlen($group["DATE_ACTIVE_FROM"])>0 && !CheckDateTime($group["DATE_ACTIVE_FROM"]))
					{
						$error = str_replace("#GROUP_ID#", $group["GROUP_ID"], GetMessage("WRONG_DATE_ACTIVE_FROM"));
						static::$errors[] = $error;
					}

					if (strlen($group["DATE_ACTIVE_TO"])>0 && !CheckDateTime($group["DATE_ACTIVE_TO"]))
					{
						$error = str_replace("#GROUP_ID#", $group["GROUP_ID"], GetMessage("WRONG_DATE_ACTIVE_TO"));
						static::$errors[] = $error;
					}
				}
			}
		}
	}

	protected static function checkLoginExists($login, $id = null, $external_auth_id = null)
	{
		global $DB;

		$result = $DB->Query(
			"SELECT 'x' ".
				"FROM b_user ".
				"WHERE LOGIN='".$DB->ForSql($login, 50)."' ".
				" ".($id === false ? "" : " AND ID<>".intval($id)).
				" ".($external_auth_id !== null ? "	AND EXTERNAL_AUTH_ID='".$DB->ForSql($external_auth_id)."' " : " AND (EXTERNAL_AUTH_ID IS NULL OR ".$DB->Length("EXTERNAL_AUTH_ID")."<=0)")
		);

		if($result->fetch())
		{
			static::$errors[] = str_replace("#LOGIN#", htmlspecialchars($login), GetMessage("USER_EXIST"));
		}
	}

	public static function checkFields($data, $action = 'update', $throwException = false)
	{
		// 1. Способ накопления ошибок (массив?)
		// 2. во внешней авторизации пропускаются проверки, которые должны быть стандартными
		//    может передавать в checkFields параметр excludes? а может просто сделать копию data без этих полей

		if (is_set($data, "PERSONAL_PHOTO"))
		{
			if ((strlen($data["PERSONAL_PHOTO"]["name"])<=0 && strlen($data["PERSONAL_PHOTO"]["del"])<=0))
			{
				unset($data["PERSONAL_PHOTO"]);
			}
			else
			{
				$result = CFile::CheckImageFile($data["PERSONAL_PHOTO"]);

				if (strlen($result) > 0)
				{
					static::$errors[] = $result;
				}
			}
		}

//		if(is_set($data, "PERSONAL_BIRTHDAY") && strlen($data["PERSONAL_BIRTHDAY"])>0 && !CheckDateTime($data["PERSONAL_BIRTHDAY"]))
//		{
//			static::$errors[] = GetMessage("WRONG_PERSONAL_BIRTHDAY");
//		}

		if (is_set($data, "WORK_LOGO"))
		{
			if ((strlen($data["WORK_LOGO"]["name"])<=0 && strlen($data["WORK_LOGO"]["del"])<=0))
			{
				unset($data["WORK_LOGO"]);
			}
			else
			{
				$result = CFile::CheckImageFile($data["WORK_LOGO"]);

				if (strlen($result) > 0)
				{
					static::$errors[] = $result;
				}
			}
		}

		parent::checkFields($data, $action, $throwException);
	}

	public static function getGroupPolicy($user_id)
	{
		return array();
	}
}