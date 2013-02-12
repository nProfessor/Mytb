<?
if(IsModuleInstalled('socialservices'))
{
	$updater->CopyFiles("install/components", "components");
	//Following copy was parsed out from module installer
	$updater->CopyFiles("install/js", "js");
	$updater->CopyFiles("install/tools", "tools");
}
if(IsModuleInstalled('socialservices') && $updater->CanUpdateDatabase())
{//This was parsed from description.full file. Remove if no event handlers was actually added.
	RegisterModuleDependences('socialnetwork', 'OnFillSocNetLogEvents', 'socialservices', 'CSocServEventHandlers', 'OnFillSocNetLogEvents');
	RegisterModuleDependences('timeman', 'OnTimeManShow', 'socialservices', 'CSocServEventHandlers', 'OnTimeManShow');
	RegisterModuleDependences('socialnetwork', 'OnFillSocNetLogEvents', 'socialservices', 'CSocServEventHandlers', 'OnFillSocNetLogEvents');
	RegisterModuleDependences('timeman', 'OnAfterTMReportDailyAdd', 'socialservices', 'CSocServAuthDB', 'OnAfterTMReportDailyAdd');
	RegisterModuleDependences('timeman', 'OnAfterTMDayStart', 'socialservices', 'CSocServAuthDB', 'OnAfterTMDayStart');
}
?>
<?
if(IsModuleInstalled('socialservices'))
{
	if ($updater->CanUpdateDatabase())
	{
		if (!$updater->TableExists('b_socialservices_user'))
		{
			$updater->Query(array(
				"Mysql" => "CREATE TABLE b_socialservices_user
							(
								ID INT NOT NULL AUTO_INCREMENT,
								LOGIN VARCHAR(100) NOT NULL,
								NAME VARCHAR(100) NULL,
								LAST_NAME VARCHAR(100) NULL,
								EMAIL VARCHAR(100) NULL,
								PERSONAL_PHOTO INT NULL,
								EXTERNAL_AUTH_ID VARCHAR(100) NOT NULL,
								USER_ID INT NOT NULL,
								XML_ID VARCHAR(100) NOT NULL,
								CAN_DELETE CHAR(1) NOT NULL DEFAULT 'Y',
								PERSONAL_WWW VARCHAR(100) NULL,
								PERMISSIONS VARCHAR(555) NULL,
								OATOKEN VARCHAR(250) NULL,
								OASECRET VARCHAR(250) NULL,
								REFRESH_TOKEN VARCHAR(250) NULL,
								SEND_ACTIVITY CHAR(1) NULL DEFAULT 'Y',
								PRIMARY KEY (ID),
								UNIQUE INDEX IX_B_SOCIALSERVICES_USER (XML_ID ASC, EXTERNAL_AUTH_ID ASC) 
							);",
				"Oracle" => "CREATE TABLE b_socialservices_user
							(
								ID NUMBER(18) NOT NULL,
								LOGIN VARCHAR2(100 CHAR) NOT NULL,
								NAME VARCHAR2(100 CHAR) NULL,
								LAST_NAME VARCHAR2(100 CHAR) NULL,
								EMAIL VARCHAR2(100 CHAR) NULL,
								PERSONAL_PHOTO NUMBER(18) NULL,
								EXTERNAL_AUTH_ID VARCHAR2(100 CHAR) NOT NULL,
								USER_ID NUMBER(18) NOT NULL,
								XML_ID VARCHAR2(100 CHAR) NOT NULL,
								CAN_DELETE CHAR(1 CHAR) DEFAULT 'Y' NOT NULL,
								PERSONAL_WWW VARCHAR2(100 CHAR) NULL,
								PERMISSIONS VARCHAR2(555 CHAR) NULL,
								OATOKEN VARCHAR2(250 CHAR) NULL,
								OASECRET VARCHAR2(250 CHAR) NULL,
								REFRESH_TOKEN VARCHAR2(250 CHAR) NULL,
								SEND_ACTIVITY CHAR(1 CHAR) DEFAULT 'Y' NOT NULL,
								PRIMARY KEY (ID)
							)",
				"MSSQL" => "CREATE TABLE b_socialservices_user
							(
								ID INT NOT NULL IDENTITY (1, 1),
								LOGIN VARCHAR(100) NOT NULL,
								NAME VARCHAR(100) NULL,
								LAST_NAME VARCHAR(100) NULL,
								EMAIL VARCHAR(100) NULL,
								PERSONAL_PHOTO INT NULL,
								EXTERNAL_AUTH_ID VARCHAR(100) NOT NULL,
								USER_ID INT NOT NULL,
								XML_ID VARCHAR(100) NOT NULL,
								CAN_DELETE CHAR(1) NOT NULL,
								PERSONAL_WWW VARCHAR(100) NULL,
								PERMISSIONS VARCHAR(555) NULL,
								OATOKEN VARCHAR(250) NULL,
								OASECRET VARCHAR(250) NULL,
								REFRESH_TOKEN VARCHAR(250) NULL,
								SEND_ACTIVITY CHAR(1) NULL,
							)",
			));
			
			$updater->Query(array(
				"MSSQL" => "ALTER TABLE b_socialservices_user ADD CONSTRAINT PK_B_SOCIALSERVICES_USER PRIMARY KEY (ID)",
				"Oracle" => "CREATE SEQUENCE SQ_B_SOCIALSERVICES_USER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER",
				));
				
			$updater->Query(array(
				"Oracle" => "CREATE UNIQUE INDEX IX_B_SOCIALSERVICES_USER ON b_socialservices_user(XML_ID, EXTERNAL_AUTH_ID)",
				"MSSQL" => "CREATE UNIQUE INDEX IX_B_SOCIALSERVICES_USER ON b_socialservices_user(XML_ID, EXTERNAL_AUTH_ID)",
			));
			$updater->Query(array(
				"Oracle" => "CREATE OR REPLACE TRIGGER B_SOCIALSERVICES_USER_INSERT
							BEFORE INSERT
							ON b_socialservices_user
							FOR EACH ROW
							BEGIN
								IF :NEW.ID IS NULL THEN
									SELECT SQ_B_SOCIALSERVICES_USER.NEXTVAL INTO :NEW.ID FROM dual;
								END IF;
							END;",
				"MSSQL" => "ALTER TABLE b_socialservices_user ADD CONSTRAINT DF_B_SOCIALSERVICES_USER_SEND_ACTIVITY DEFAULT 'Y' FOR SEND_ACTIVITY",
			));
			$updater->Query(array(
				"MSSQL" => "ALTER TABLE b_socialservices_user ADD CONSTRAINT DF_B_SOCIALSERVICES_USER_CAN_DELETE DEFAULT 'Y' FOR CAN_DELETE",
			));
				
		}
		if (!$DB->Query("select PERMISSIONS from b_socialservices_user WHERE 1=0", true))
			{
				$updater->Query(array(
					"MySQL" => "alter table b_socialservices_user add PERMISSIONS VARCHAR(555) NULL",
					"Oracle" => "alter table b_socialservices_user add PERMISSIONS VARCHAR2(555 CHAR) NULL",
					"MSSQL" => "alter table b_socialservices_user add PERMISSIONS VARCHAR(555) NULL",
				));
			}
		if (!$DB->Query("select OATOKEN from b_socialservices_user WHERE 1=0", true))
			{
				$updater->Query(array(
					"MySQL" => "alter table b_socialservices_user add OATOKEN VARCHAR(250) NULL",
					"Oracle" => "alter table b_socialservices_user add OATOKEN VARCHAR2(250 CHAR) NULL",
					"MSSQL" => "alter table b_socialservices_user add OATOKEN VARCHAR(250) NULL",
				));
			}
			
		if (!$DB->Query("select OASECRET from b_socialservices_user WHERE 1=0", true))
			{
				$updater->Query(array(
					"MySQL" => "alter table b_socialservices_user add OASECRET VARCHAR(250) NULL",
					"Oracle" => "alter table b_socialservices_user add OASECRET VARCHAR2(250 CHAR) NULL",
					"MSSQL" => "alter table b_socialservices_user add OASECRET VARCHAR(250) NULL",
				));
			}
		if (!$DB->Query("select REFRESH_TOKEN from b_socialservices_user WHERE 1=0", true))
			{
				$updater->Query(array(
					"MySQL" => "alter table b_socialservices_user add REFRESH_TOKEN VARCHAR(250) NULL",
					"Oracle" => "alter table b_socialservices_user add REFRESH_TOKEN VARCHAR2(250 CHAR) NULL",
					"MSSQL" => "alter table b_socialservices_user add REFRESH_TOKEN VARCHAR(250) NULL",
				));
			}
		if (!$DB->Query("select SEND_ACTIVITY from b_socialservices_user WHERE 1=0", true))
			{
				$updater->Query(array(
					"MySQL" => "alter table b_socialservices_user add SEND_ACTIVITY CHAR(1) NULL DEFAULT 'Y'",
					"Oracle" => "alter table b_socialservices_user add SEND_ACTIVITY CHAR(1 CHAR) DEFAULT 'Y' NOT NULL",
					"MSSQL" => "alter table b_socialservices_user add SEND_ACTIVITY CHAR(1) NULL CONSTRAINT DF_B_SOCIALSERVICES_USER_SEND_ACTIVITY DEFAULT 'Y'",
				));
			}
	}
	$updater->CopyFiles("install/components", "components");
}
?>
<?
if($updater->CanUpdateKernel())
{
	$arToDelete = array(
		"modules/socialservices/classes/general/socserv.ajax.php",
		"modules/socialservices/lang/ru/classes/general/socserv.ajax.php",
	);
	foreach($arToDelete as $file)
		CUpdateSystem::DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].$updater->kernelPath."/".$file);
}
?>