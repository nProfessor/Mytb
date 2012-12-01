<?
if (php_sapi_name() != 'cli')
	die('Must be started from command line');

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../../');
define('DOCUMENT_ROOT', rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/'));
define('LANGUAGE_ID', 'en');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
while(ob_end_flush());
set_time_limit(0);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/dump.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/backup.php");

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

function IntOption($name)
{
	global $arParams;
	return $arParams[$name];
}

function ShowBackupStatus($str)
{
	global $arParams;
	static $time;
	if (!$time)
		$time = microtime(1);
	if ($arParams['show_status'])
		echo round(microtime(1)-$time, 2).' sec	'.$str."\n";
}

function haveTime()
{
	return true;
}

function RaiseErrorAndDie($strError)
{
	echo 'Error: '.str_replace('<br>',"\n",$strError)."\n";
	die();
}

/*
$arUserParams = array(
	'dump_base' => false,
	'dump_file_kernel' => false,
	'dump_file_public' => false,
	'dump_send_to_bucket_id' => 2,
	'dump_integrity_check' => 0,
	'dump_do_clouds' => 1,
	'dump_cloud_2' => 1,
);
*/
$arParams = array(
	'show_status' => true,

	'dump_file_kernel' => true,
	'dump_file_public' => true,

	'dump_base' => true,
	'dump_base_skip_stat' => false,
	'dump_base_skip_search' => false,
	'dump_base_skip_log' => false,

	'dump_archive_size_limit' => 1024*1024*1024,
	'dump_max_file_size' => 0,
	'skip_symlinks' => true,
	'dump_use_compression' => true,
	'dump_integrity_check' => true,

	'dump_do_clouds' => false,
	'dump_cloud_0' => false,
	'dump_send_to_bucket_id' => 0,

	'skip_mask' => false,
	'skip_mask_array' => array(),

	'dump_encrypt_key' => '',
);

if (is_array($arUserParams))
	$arParams = array_merge($arParams, $arUserParams);
$arParams['disk_space'] = COption::GetOptionInt('main','disk_space',0);

$NS = array();
if (($arc_name = $argv[1]) && !is_dir($arc_name))
{
	$NS['arc_name'] = $arc_name;
	$NS['dump_name'] = str_replace(array('.tar','.gz','.enc'),'',$arc_name).'.sql';
}
else
{
	$arc_name = CBackup::GetArcName();
	$NS['arc_name'] = $arc_name.($arParams['dump_encrypt_key'] ? ".enc" : ".tar").(IntOption('dump_use_compression') ? ".gz" : '');
	$NS['dump_name'] = $arc_name.'.sql';
}

$after_file = str_replace('.sql','_after_connect.sql',$NS['dump_name']);
ShowBackupStatus('Backup started to file: '.$NS['arc_name']);


if (IntOption('dump_base'))
{
	ShowBackupStatus('Dumping database...');
	$bres = CBackup::BaseDump($NS['dump_name'], 0, -1);

	if ($bres['end'])
	{
		$rs = $DB->Query('SHOW VARIABLES LIKE "character_set_results"');
		if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
			file_put_contents($after_file, "SET NAMES '".$f['Value']."';\n");

		$rs = $DB->Query('SHOW VARIABLES LIKE "collation_database"');
		if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
			file_put_contents($after_file, "ALTER DATABASE `<DATABASE>` COLLATE ".$f['Value'].";\n",8);
	}

	ShowBackupStatus('Archiving database dump...');
	$tar = new CTar;
	$tar->EncryptKey = $arParams['dump_encrypt_key'];
	$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
	$tar->gzip = IntOption('dump_use_compression');
	$tar->path = DOCUMENT_ROOT;

	if (!$tar->openWrite($NS["arc_name"]))
		RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

	if (!$tar->ReadBlockCurrent && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
		$tar->addFile($f);

	$Block = $tar->Block;
	while(($r = $tar->addFile($NS['dump_name'])) && $tar->ReadBlockCurrent > 0);
	$NS["data_size"] += 512 * ($tar->Block - $Block);
	
	if ($r === false)
		RaiseErrorAndDie(implode('<br>',$tar->err));

	$tar->addFile($after_file);
	unlink($NS["dump_name"]) && (!file_exists($after_file) || unlink($after_file));

	$NS['arc_size'] = 0;
	$name = $NS["arc_name"];
	while(file_exists($name))
	{
		$size = filesize($name);
		$NS['arc_size'] += $size;
		if (IntOption("disk_space") > 0)
			CDiskQuota::updateDiskQuota("file", $size, "add");
		$name = CTar::getNextName($name);
	}
	$tar->close();
}

if ($arDumpClouds = CBackup::CheckDumpClouds())
{
	ShowBackupStatus('Downloading cloud files...');
	foreach($arDumpClouds as $arBucket)
	{
		$id = $arBucket['ID'];

		$obCloud = new CloudDownload($id);
		$res = $obCloud->Scan('');
	}
}

if (CBackup::CheckDumpFiles() || CBackup::CheckDumpClouds())
{
	ShowBackupStatus('Archiving files...');
	$DOCUMENT_ROOT_SITE = DOCUMENT_ROOT;
	if (!defined('DOCUMENT_ROOT_SITE'))
		define('DOCUMENT_ROOT_SITE', $DOCUMENT_ROOT_SITE);

	$tar = new CTar;
	$tar->EncryptKey = $arParams['dump_encrypt_key'];
	$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
	$tar->gzip = IntOption('dump_use_compression');
	$tar->path = DOCUMENT_ROOT_SITE;

	if (!$tar->openWrite($NS["arc_name"]))
		RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

	$Block = $tar->Block;
	$DirScan = new CDirRealScan;

	if (!IntOption('dump_base') && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
		$tar->addFile($f);

	$r = $DirScan->Scan(DOCUMENT_ROOT_SITE);
	$NS["data_size"] += 512 * ($tar->Block - $Block);
	$tar->close();

	if ($r === false)
		RaiseErrorAndDie(implode('<br>',$DirScan->err));

	$NS["cnt"] += $DirScan->FileCount;

	$NS['arc_size'] = 0;
	$name = $NS["arc_name"];
	while(file_exists($name))
	{
		$size = filesize($name);
		$NS['arc_size'] += $size;
		if (IntOption("disk_space") > 0)
			CDiskQuota::updateDiskQuota("file", $size, "add");
		$name = CTar::getNextName($name);
	}
	DeleteDirFilesEx(BX_ROOT.'/backup/clouds');
}

if (IntOption('dump_integrity_check'))
{
	ShowBackupStatus('Checking archive integrity...');
	$tar = new CTarCheck;
	$tar->EncryptKey = $arParams['dump_encrypt_key'];

	if (!$tar->openRead($NS["arc_name"]))
		RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS_READ').'<br>'.implode('<br>',$tar->err));
	else
	{
		while($r = $tar->extractFile());
		if ($r === false)
			RaiseErrorAndDie(implode('<br>',$tar->err));
	}
	$tar->close();
}

if (IntOption('dump_send_to_bucket_id'))
{
	ShowBackupStatus('Sending backup to the cloud...');
	if (!CModule::IncludeModule('clouds'))
		RaiseErrorAndDie(GetMessage("MAIN_DUMP_NO_CLOUDS_MODULE"));

	while(haveTime())
	{
	xdebug_start_trace();
		$file_size = filesize($NS["arc_name"]);
		$obUpload = new CCloudStorageUpload(substr($NS['arc_name'],strlen(DOCUMENT_ROOT)));
		if (!$obUpload->isStarted())
		{
			if (!$obUpload->Start(IntOption('dump_send_to_bucket_id'), $file_size))
			{
				if ($e = $APPLICATION->GetException())
					$strError = $e->GetString();
				else
					$strError = GetMessage('MAIN_DUMP_INT_CLOUD_ERR');
				RaiseErrorAndDie($strError);
			}
		}

		if (!$fp = fopen($NS['arc_name'],'rb'))
			RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_OPEN_FILE").$NS['arc_name']);

		while($obUpload->getPos() < $file_size)
		{
			$part = fread($fp, $obUpload->getPartSize());
			while($obUpload->hasRetries())
				if($res = $obUpload->Next($part))
					break;
			if (!$res)
			{
				$obUpload->Delete();
				RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']));
			}
		}
		fclose($fp);

		if (!$obUpload->Finish())
		{
			$obUpload->Delete();
			RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']));
		}

		$oBucket = new CCloudStorageBucket(IntOption('dump_send_to_bucket_id'));
		$oBucket->IncFileCounter($file_size);

		if (file_exists($arc_name = CTar::getNextName($NS['arc_name'])))
			$NS['arc_name'] = $arc_name; # GOTO 223
		else // finish
		{
			$name = preg_replace('#\.[0-9]+$#','',$NS['arc_name']);
			while(file_exists($name))
			{
				$size = filesize($name);
				if (unlink($name) && IntOption("disk_space") > 0)
					CDiskQuota::updateDiskQuota("file",$size , "del");
				$name = CTar::getNextName($name);
			}

			break; 
		}
	}
}
ShowBackupStatus("Finished.\n\nData size: ".$NS['data_size']."\nArchive size: ".$NS['arc_size']."\n");
