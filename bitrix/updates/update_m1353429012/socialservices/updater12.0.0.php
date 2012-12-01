<?
if(IsModuleInstalled('socialservices'))
{
	$updater->CopyFiles("install/components", "components");
	//Following copy was parsed out from module installer
	$updater->CopyFiles("install/js", "js");
	//Following copy was parsed out from module installer
	$updater->CopyFiles("install/tools", "tools");
}
//There is .sql file in update. Do not forget alter DB properly.
if(IsModuleInstalled('socialservices'))
{
	if ($updater->CanUpdateDatabase())
	{
		if(!$updater->TableExists("b_socialservices_user"))
		{	
			$updater->Query(array(
				"MySQL"  => "CREATE TABLE b_socialservices_user (
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
							PRIMARY KEY (ID),
							UNIQUE INDEX IX_B_SOCIALSERVICES_USER (XML_ID ASC, EXTERNAL_AUTH_ID ASC)
							)",
				"MSSQL"  => "CREATE TABLE B_SOCIALSERVICES_USER (
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
							PERSONAL_WWW VARCHAR(100) NULL
							)",
				"Oracle"  => "CREATE TABLE B_SOCIALSERVICES_USER (
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
							PRIMARY KEY (ID)
							)",
			));

			$updater->Query(array(
				"MSSQL"  => "ALTER TABLE B_SOCIALSERVICES_USER ADD CONSTRAINT PK_B_SOCIALSERVICES_USER PRIMARY KEY (ID)",
				"Oracle" => "CREATE SEQUENCE SQ_B_SOCIALSERVICES_USER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER",
			));
			$updater->Query(array(
				"MSSQL"  => "ALTER TABLE B_SOCIALSERVICES_USER ADD CONSTRAINT DF_B_SOCIALSERVICES_USER_CAN_DELETE DEFAULT 'Y' FOR CAN_DELETE",
			));
			$updater->Query(array(
				"Oracle"  => "CREATE UNIQUE INDEX IX_B_SOCIALSERVICES_USER ON B_SOCIALSERVICES_USER(XML_ID, EXTERNAL_AUTH_ID)",
				"MSSQL"  => "CREATE UNIQUE INDEX IX_B_SOCIALSERVICES_USER ON B_SOCIALSERVICES_USER(XML_ID, EXTERNAL_AUTH_ID)",
			));
			$updater->Query(array(
				"Oracle" => "CREATE OR REPLACE TRIGGER B_SOCIALSERVICES_USER_INSERT
							BEFORE INSERT
							ON B_SOCIALSERVICES_USER
							FOR EACH ROW
							BEGIN
								IF :NEW.ID IS NULL THEN
									SELECT SQ_B_SOCIALSERVICES_USER.NEXTVAL INTO :NEW.ID FROM dual;
								END IF;
							END;"
			));
		}
	}
}
if(IsModuleInstalled('socialservices') && $updater->CanUpdateDatabase())
{//This was parsed from description.full file. Remove if no event handlers was actually added.
	RegisterModuleDependences("main", "OnUserDelete", "socialservices", "CSocServAuthDB", "OnUserDelete");
}
?>
