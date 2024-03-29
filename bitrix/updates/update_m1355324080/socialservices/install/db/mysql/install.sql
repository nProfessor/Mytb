CREATE TABLE b_socialservices_user
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
);