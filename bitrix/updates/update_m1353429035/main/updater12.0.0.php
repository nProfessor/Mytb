<?
$updater->CopyFiles("install/admin", "admin");
$updater->CopyFiles("install/components", "components");
$updater->CopyFiles("install/gadgets", "gadgets");
$updater->CopyFiles("install/js", "js");
$updater->CopyFiles("install/panel", "panel");
$updater->CopyFiles("install/themes", "themes");
$updater->CopyFiles("install/images", "images");

if ($updater->CanUpdateDatabase())
{

	// QC
	
	if (
		$updater->TableExists("b_checklist")
		&& !$GLOBALS["DB"]->Query("select EMAIL from b_checklist WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_checklist ADD EMAIL VARCHAR2(50 CHAR) NULL",
			"MySql" => "alter table b_checklist ADD EMAIL varchar(50)",
			"MSSQL" => "alter table b_checklist ADD EMAIL varchar(50) NULL",
		));
	}
	if (
		$updater->TableExists("b_checklist")
		&& !$GLOBALS["DB"]->Query("select PHONE from b_checklist WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_checklist ADD PHONE VARCHAR2(50 CHAR) NULL",
			"MySql" => "alter table b_checklist ADD PHONE varchar(50)",
			"MSSQL" => "alter table b_checklist ADD PHONE varchar(50) NULL",
		));
	}
	if (
		$updater->TableExists("b_checklist")
		&& !$GLOBALS["DB"]->Query("select SENDED_TO_BITRIX from b_checklist WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_checklist ADD SENDED_TO_BITRIX char(1 CHAR) default 'N' null",
			"MySql" => "alter table b_checklist ADD SENDED_TO_BITRIX char(1) null default 'N'",
			"MSSQL" => "alter table b_checklist ADD SENDED_TO_BITRIX char(1) NULL DEFAULT 'N'",
		));
	}
	if (
		$updater->TableExists("b_checklist")
		&& !$GLOBALS["DB"]->Query("select HIDDEN from b_checklist WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_checklist ADD HIDDEN char(1 CHAR) default 'N' null",
			"MySql" => "alter table b_checklist ADD HIDDEN char(1) null default 'N'",
			"MSSQL" => "alter table b_checklist ADD HIDDEN char(1) NULL DEFAULT 'N'",
		));
	}
	// QC

	if (
		$updater->TableExists("b_module_to_module")
		&& !$GLOBALS["DB"]->Query("select VERSION from b_module_to_module WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_module_to_module ADD VERSION NUMBER(18) NULL",
			"MySql" => "alter table b_module_to_module ADD VERSION int(18) null",
			"MSSQL" => "alter table b_module_to_module ADD VERSION int NULL",
		));
		$updater->Query("update b_module_to_module set VERSION = 1");
	}

	if (
		$updater->TableExists("b_favorite")
		&& !$GLOBALS["DB"]->Query("select MENU_ID from b_favorite WHERE 1=0", true)
	)
	{
		$updater->Query(array(
			"Oracle" => "alter table b_favorite ADD MENU_ID VARCHAR2(255 CHAR) NULL",
			"MySql" => "alter table b_favorite ADD MENU_ID varchar(255)",
			"MSSQL" => "alter table b_favorite ADD MENU_ID varchar(255) NULL",
		));
	}

	if(!$updater->TableExists("b_filters"))
	{
		$updater->Query(array(
			"Oracle" => "
CREATE TABLE B_FILTERS
(
	ID NUMBER(18) NOT NULL,
	USER_ID NUMBER(18) NULL,
	FILTER_ID VARCHAR2(255 CHAR) NOT NULL,
	NAME VARCHAR2(255 CHAR) NOT NULL,
	FIELDS clob NOT NULL,
	COMMON CHAR(1 CHAR) NULL,
	PRESET CHAR(1 CHAR) NULL,
	LANGUAGE_ID CHAR(2 CHAR) NULL,
	PRESET_ID VARCHAR2(255 CHAR) NULL,
	PRIMARY KEY (ID)
)
",
			"MySql" => "
CREATE TABLE b_filters
(
	ID int(18) not null auto_increment,
	USER_ID int(18),
	FILTER_ID varchar(255) not null,
	NAME varchar(255) not null,
	FIELDS text not null,
	COMMON char(1),
	PRESET char(1),
	LANGUAGE_ID char(2),
	PRESET_ID varchar(255) null,
	PRIMARY KEY (ID)
)",
			"MSSQL" => "
CREATE TABLE B_FILTERS
(
	ID int NOT NULL IDENTITY (1, 1),
	USER_ID int NULL,
	FILTER_ID varchar(255) NOT NULL,
	NAME varchar(255) NOT NULL,
	FIELDS text NOT NULL,
	COMMON char(1) NULL,
	PRESET char(1) NULL,
	PRESET_ID varchar(255) NULL,
	LANGUAGE_ID char(2) NULL
)"
		));

		$updater->Query(array(
			"Oracle" => "CREATE SEQUENCE SQ_B_FILTERS START WITH 1 INCREMENT BY 1 NOMINVALUE NOMAXVALUE NOCYCLE NOCACHE NOORDER",
			"MSSQL" => "ALTER TABLE B_FILTERS ADD CONSTRAINT PK_B_FILTERS PRIMARY KEY (ID)"
		));
	}

	if(!$updater->TableExists("b_admin_notify_lang"))
	{
		$updater->Query(array(
			"Oracle" => "CREATE TABLE B_ADMIN_NOTIFY_LANG
						(
							ID NUMBER(18) NOT NULL,
							NOTIFY_ID NUMBER(18) NOT NULL,
							LID CHAR(2 CHAR) NOT NULL,
							MESSAGE CLOB,
							PRIMARY KEY (ID)
						)",
			"MySql" => "CREATE TABLE b_admin_notify_lang
						(
							ID int(18) not null AUTO_INCREMENT,
							NOTIFY_ID int(18) not null,
							LID char(2) not null,
							MESSAGE text,
							primary key (ID),
							index IX_ADM_NTFY_LID (LID),
							unique IX_ADM_NTFY_LANG(NOTIFY_ID, LID)
						)",
			"MSSQL" => "CREATE TABLE B_ADMIN_NOTIFY_LANG
						(
							ID int NOT NULL IDENTITY (1, 1),
							NOTIFY_ID int NOT NULL,
							LID varchar(2) NOT NULL,
							MESSAGE text
						)"
		));
		$updater->Query(array(
			"MSSQL" => "ALTER TABLE B_ADMIN_NOTIFY_LANG ADD CONSTRAINT PK_B_ANL_ID PRIMARY KEY (ID)"
		));
		$updater->Query(array(
			"Oracle" => "CREATE INDEX IX_ADM_NTFY_LID ON B_ADMIN_NOTIFY_LANG(LID)",
			"MSSQL" => "CREATE INDEX IX_ADM_NTFY_LID ON B_ADMIN_NOTIFY_LANG(LID)"
		));

		$updater->Query(array(
			"Oracle" => "CREATE UNIQUE INDEX IX_ADM_NTFY_LANG ON B_ADMIN_NOTIFY_LANG(NOTIFY_ID, LID)",
			"MSSQL" => "CREATE UNIQUE INDEX IX_ADM_NTFY_LANG ON B_ADMIN_NOTIFY_LANG(NOTIFY_ID, LID)",
		));

		$updater->Query(array(
			"Oracle" => "CREATE SEQUENCE SQ_B_ADMIN_NOTIFY_LANG START WITH 1 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER"
		));

		$updater->Query(array(
			"Oracle" => "CREATE OR REPLACE TRIGGER B_ADMIN_NOTIFY_LANG_INSERT
						BEFORE INSERT
						ON B_ADMIN_NOTIFY_LANG
						FOR EACH ROW
						BEGIN
							IF :NEW.ID IS NULL THEN
 								SELECT SQ_B_ADMIN_NOTIFY_LANG.NEXTVAL INTO :NEW.ID FROM dual;
							END IF;
						END;"
		));
	}

}

if($updater->CanUpdateKernel())
{
	$arToDelete = array(
		"modules/main/install/admin/file_dialog_action.php",
		"admin/file_dialog_action.php",
		"modules/main/install/admin/file_dialog_flash_preview.php",
		"admin/file_dialog_flash_preview.php",
		"modules/main/install/admin/file_dialog_load.php",
		"admin/file_dialog_load.php",
		"modules/main/install/admin/file_dialog_manage_config.php",
		"admin/file_dialog_manage_config.php",
		"modules/main/install/admin/file_dialog_upload.php",
		"admin/file_dialog_upload.php",
		"modules/main/classes/general/entity_base.php",
		"modules/main/classes/general/entity_boolean_field.php",
		"modules/main/classes/general/entity_expression_field.php",
		"modules/main/classes/general/entity_field.php",
		"modules/main/classes/general/entity_iblock_section.php",
		"modules/main/classes/general/entity_query.php",
		"modules/main/classes/general/entity_query_chain.php",
		"modules/main/classes/general/entity_query_chain_element.php",
		"modules/main/classes/general/entity_reference_field.php",
		"modules/main/classes/general/entity_scalar_field.php",
		"modules/main/classes/general/entity_site.php",
		"modules/main/classes/general/entity_u_field.php",
		"modules/main/classes/general/entity_user.php",
		"modules/main/classes/general/entity_user_group.php",
		"modules/main/classes/general/entity_utm_user.php",
		"modules/main/classes/general/entity_uts_user.php",
		"modules/main/classes/general/entity_workgroup.php",
		"modules/main/lang/de/classes/general/entity_user.php",
		"modules/main/lang/de/classes/general/entity_workgroup.php",
		"modules/main/lang/en/classes/general/entity_user.php",
		"modules/main/lang/en/classes/general/entity_workgroup.php",
		"modules/main/lang/ru/classes/general/entity_user.php",
		"modules/main/lang/ru/classes/general/entity_workgroup.php",
		"modules/main/classes/general/entity_group.php",
		"modules/main/lib/entity_iblock_section.php",
		"modules/main/lib/data/database.php",
		"modules/main/lib/data/databasepool.php",
		"modules/main/lib/data/mysqldatabase.php",
		"modules/main/lib/data/mysqlquerybuilder.php",
		"modules/main/lib/data/querybuilder.php",
		"modules/main/lib/security/identity.php",
		"modules/main/lib/globalization/culture.php",
		"modules/main/lib/globalization/site.php",
		"modules/main/lib/loc.php",
		"modules/main/lib/localization/site.php",
		"modules/main/lib/entity_base.php",
		"modules/main/lib/entity_boolean_field.php",
		"modules/main/lib/entity_date_field.php",
		"modules/main/lib/entity_datetime_field.php",
		"modules/main/lib/entity_enum_field.php",
		"modules/main/lib/entity_expression_field.php",
		"modules/main/lib/entity_field.php",
		"modules/main/lib/entity_float_field.php",
		"modules/main/lib/entity_integer_field.php",
		"modules/main/lib/entity_query.php",
		"modules/main/lib/entity_query_chain.php",
		"modules/main/lib/entity_query_chain_element.php",
		"modules/main/lib/entity_reference_field.php",
		"modules/main/lib/entity_scalar_field.php",
		"modules/main/lib/entity_string_field.php",
		"modules/main/lib/entity_text_field.php",
		"modules/main/lib/entity_u_field.php",
		"modules/main/lib/entity/entity_base.php",
		"modules/main/lib/entity/entity_boolean_field.php",
		"modules/main/lib/entity/entity_date_field.php",
		"modules/main/lib/entity/entity_datetime_field.php",
		"modules/main/lib/entity/entity_enum_field.php",
		"modules/main/lib/entity/entity_expression_field.php",
		"modules/main/lib/entity/entity_field.php",
		"modules/main/lib/entity/entity_float_field.php",
		"modules/main/lib/entity/entity_integer_field.php",
		"modules/main/lib/entity/entity_query.php",
		"modules/main/lib/entity/entity_query_chain.php",
		"modules/main/lib/entity/entity_query_chain_element.php",
		"modules/main/lib/entity/entity_reference_field.php",
		"modules/main/lib/entity/entity_scalar_field.php",
		"modules/main/lib/entity/entity_string_field.php",
		"modules/main/lib/entity/entity_text_field.php",
		"modules/main/lib/entity/entity_u_field.php",
		"modules/main/lib/entity_group.php",
		"modules/main/lib/entity_site.php",
		"modules/main/lib/entity_user.php",
		"modules/main/lib/entity_user_group.php",
		"modules/main/lib/entity_utm_user.php",
		"modules/main/lib/entity_uts_user.php",
		"modules/main/lib/entity_workgroup.php",
		"modules/main/lang/de/lib/entity_user.php",
		"modules/main/lang/de/lib/entity_workgroup.php",
		"modules/main/lang/en/lib/entity_user.php",
		"modules/main/lang/en/lib/entity_workgroup.php",
		"modules/main/lang/ru/lib/entity_user.php",
		"modules/main/lang/ru/lib/entity_workgroup.php",
		"modules/main/admin/rating_index.php",
		"modules/main/admin/settings_index.php",
		"modules/main/admin/tools_index.php",
		"modules/main/admin/user_index.php",
		"admin/rating_index.php",
		"admin/settings_index.php",
		"admin/tools_index.php",
		"admin/user_index.php",
		"modules/main/lib/data/connectionexception.php",
		"modules/main/lib/data/dbconnection.php",
		"modules/main/lib/data/dbconnectionpool.php",
		"modules/main/lib/data/dbresult.php",
		"modules/main/lib/data/mysqldbconnection.php",
		"modules/main/lib/data/mysqldbconnection_old.php",
		"modules/main/lib/data/mysqldbresult.php",
		"modules/main/lib/data/mysqldbresult_old.php",
		"modules/main/lib/data/mysqlsqlhelper.php",
		"modules/main/lib/data/sqlexception.php",
		"modules/main/lib/data/sqlhelper.php",
		"modules/main/install/panel/main/images/informer-status-bar.png",
		"modules/main/install/panel/main/images/informer-title-blue.gif",
		"modules/main/install/panel/main/images/informer-title-green.gif",
		"modules/main/install/panel/main/images/informer-title-grey.gif",
		"modules/main/lib/db/mysqldbconnection_old.php",
		"modules/main/lib/db/mysqldbresult_old.php",
		"modules/main/admin/all_settings_index.php",
		"modules/main/admin/content_index.php",
		"modules/main/admin/services_index.php",
		"modules/main/admin/webanalytics_index.php",
		"admin/all_settings_index.php",
		"admin/content_index.php",
		"admin/services_index.php",
		"admin/webanalytics_index.php",
		"modules/main/install/panel/main/popup/popup_sprite.png",
		"modules/main/install/mssql/uninstall.sql",
		"modules/main/install/mysql/uninstall.sql",
		"modules/main/install/oracle/uninstall.sql",
		"modules/main/admin/store_index.php",
		"modules/main/install/admin/store_index.php",
		"admin/store_index.php",
		"modules/main/lang/de/admin/store_index.php",
		"modules/main/lang/en/admin/store_index.php",
		"modules/main/lang/ru/admin/store_index.php",
		"modules/main/lib/site_entity.php",
		"modules/main/lib/workgroup.php",
		"modules/main/lang/de/lib/workgroup.php",
		"modules/main/lang/en/lib/workgroup.php",
		"modules/main/lang/ru/lib/workgroup.php",
		"modules/main/lib/currentsite.php",
		"modules/main/install/images/main/spisok_de.gif",
		"modules/main/install/images/main/spisok_en.gif",
		"images/main/spisok_de.gif",
		"images/main/spisok_en.gif",
		"modules/main/include/prolog_auth.php",
		"modules/main/include/epilog_auth.php",
		"modules/main/install/images/main/spisok_de.gif",
		"modules/main/install/images/main/spisok_en.gif",
		"modules/main/lib/currentsite.php",
		"modules/main/install/panel/main/images/logo-popup-center.png",
		"modules/main/install/panel/main/images/logo-popup-left.png",
		"modules/main/install/panel/main/images/logo-popup-right.png",
		"modules/main/install/panel/main/images/logo-popup-sprite.png",
		"modules/main/interface/popup_auth.php",
		"modules/main/install/gadgets/bitrix/admin_index",
		"gadgets/bitrix/admin_index",
		"modules/main/lang/ru/interface/auth/authorize.php",
		"modules/main/lang/ru/interface/auth/change_password.php",
		"modules/main/lang/ru/interface/auth/forgot_password.php",
	);
	foreach($arToDelete as $file)
		CUpdateSystem::DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].$updater->kernelPath."/".$file);
}
?>