<?php
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2010 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "utilities/dump.php");

if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!defined("START_EXEC_TIME"))
	define("START_EXEC_TIME", microtime(true));

if (!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0777);

if (!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0666);

IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/backup.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/fileman.php");
$bMcrypt = function_exists('mcrypt_encrypt');

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('DOCUMENT_ROOT', rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/'));

$com_marker = "--";
$filr_id = "";
$sTableID = "tbl_dump";

$oSort = new CAdminSorting($sTableID, "timestamp", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$path = BX_ROOT."/backup";

// define('DUMP_DEBUG_MODE', true);
// xdebug_start_trace();

if($_REQUEST['process'] == "Y")
{
	if (!check_bitrix_sessid())
		die(GetMessage("DUMP_MAIN_SESISON_ERROR"));

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	if($_REQUEST['action'] == 'start')
	{
		$NS = Array();

		if(!file_exists(DOCUMENT_ROOT.BX_ROOT."/backup"))
			mkdir(DOCUMENT_ROOT.BX_ROOT."/backup", BX_DIR_PERMISSIONS);

		if(!file_exists(DOCUMENT_ROOT.BX_ROOT."/backup/index.php"))
		{
			$f = fopen(DOCUMENT_ROOT.BX_ROOT."/backup/index.php","w");
			fwrite($f,"<head><meta http-equiv=\"REFRESH\" content=\"0;URL=/bitrix/admin/index.php\"></head>");
			fclose($f);
		}

		if(!is_dir(DOCUMENT_ROOT.BX_ROOT."/backup") || !is_writable(DOCUMENT_ROOT.BX_ROOT."/backup"))
			RaiseErrorAndDie(GetMessage("MAIN_DUMP_FOLDER_ERR",array('#FOLDER#' => DOCUMENT_ROOT.BX_ROOT.'/backup')));

		DeleteDirFilesEx(BX_ROOT.'/backup/clouds');

		$bUseCompression = $_REQUEST['dump_disable_gzip'] != 'Y';
		if(!extension_loaded('zlib') || !function_exists("gzcompress"))
			$bUseCompression = False;

		if ($bMcrypt && $_REQUEST['dump_encrypt_key'])
		{
			$NS['dump_encrypt_key'] = $_REQUEST['dump_encrypt_key'];
			COption::SetOptionInt("main", "dump_encrypt", 1);
		}
		else
			COption::SetOptionInt("main", "dump_encrypt", 0);

		COption::SetOptionInt("main", "dump_use_compression", $bUseCompression);
		COption::SetOptionInt("main", "dump_max_exec_time", intval($_REQUEST['dump_max_exec_time']) < 5 ? 5 : $_REQUEST['dump_max_exec_time']);
		COption::SetOptionInt("main", "dump_max_exec_time_sleep", $_REQUEST['dump_max_exec_time_sleep']);

		$dump_archive_size_limit = intval($_REQUEST['dump_archive_size_limit']);
		if ($dump_archive_size_limit > 2048)
			$dump_archive_size_limit = 2048;
		if ($dump_archive_size_limit == 0)
			$dump_archive_size_limit = 1024;
		COption::SetOptionInt("main", "dump_archive_size_limit", $dump_archive_size_limit * 1024 * 1024);
		COption::SetOptionInt("main", "dump_integrity_check", $_REQUEST['dump_integrity_check'] == 'Y');

		$NS['BUCKET_ID'] = intval($_REQUEST['dump_bucket_id']);
		COption::SetOptionInt("main", "dump_bucket_id", $_REQUEST['dump_bucket_id']);

		COption::SetOptionInt("main", "dump_max_file_size", $_REQUEST['max_file_size']);
		COption::SetOptionInt("main", "dump_file_public", $_REQUEST['dump_file_public'] == 'Y');
		COption::SetOptionInt("main", "dump_file_kernel", $_REQUEST['dump_file_kernel'] == 'Y');
		COption::SetOptionInt("main", "dump_base", $DB->type == 'MYSQL' ? ($_REQUEST['dump_base'] == 'Y') : 0);
		COption::SetOptionInt("main", "dump_base_skip_stat", $_REQUEST['dump_base_skip_stat'] == 'Y');
		COption::SetOptionInt("main", "dump_base_skip_search", $_REQUEST['dump_base_skip_search'] == 'Y');
		COption::SetOptionInt("main", "dump_base_skip_log", $_REQUEST['dump_base_skip_log'] == 'Y');
		COption::SetOptionInt("main", "skip_symlinks", $_REQUEST['skip_symlinks'] == 'Y');

		$bDumpCloud = false;
		if ($arAllBucket = CBackup::GetBucketList())
		{
			foreach($arAllBucket as $arBucket)
			{
				if ($res = $_REQUEST['dump_cloud'][$arBucket['ID']] == 'Y')
					$bDumpCloud = true;
				COption::SetOptionInt('main', 'dump_cloud_'.$arBucket['ID'], $res);
			}
		}
		COption::SetOptionInt("main", "dump_do_clouds", $bDumpCloud);

		$skip_mask = $_REQUEST['skip_mask'] == 'Y';
		COption::SetOptionInt("main", "skip_mask", $skip_mask);

		$skip_mask_array = array();
		if ($skip_mask && is_array($_REQUEST['arMask']))
		{
			$arMask = array_unique($_REQUEST['arMask']);
			foreach($arMask as $mask)
				if (trim($mask))
				{
					$mask = rtrim(str_replace('\\','/',trim($mask)),'/');
					$skip_mask_array[] = $mask;
				}
			COption::SetOptionString("main", "skip_mask_array", serialize($skip_mask_array));
		}
		$NS["step"] = 1;
		$NS['st_row'] = -1;

		$prefix = '';
		if ($_REQUEST['dump_site_id'])
		{
			$NS['dump_site_id'] = $_REQUEST['dump_site_id'];
			$prefix .= '_'.$NS['dump_site_id'];
		}
		if ($bDumpCloud)
			$prefix .= '_cloud';

		$arc_name = CBackup::GetArcName($prefix);

		$NS['dump_name'] = $arc_name.".sql";
		$NS['arc_name'] = $arc_name.($NS['dump_encrypt_key'] ? ".enc" : ".tar").($bUseCompression ? ".gz" : '');
	}
	elseif($_REQUEST['action'] == 'cloud_send')
	{
		$NS = Array();
		$NS['cloud_send'] = 1;
		$NS['arc_name'] = $name = DOCUMENT_ROOT.BX_ROOT.'/backup/'.str_replace(array('..','/','\\'),'',$_REQUEST['f_id']);
		$NS['arc_size'] = filesize($NS['arc_name']);
		$NS['BUCKET_ID'] = intval($_REQUEST['dump_bucket_id']);
		while(file_exists($name = CTar::getNextName($name)))
			$NS['arc_size'] += filesize($name);
		$NS['step'] = 6;
	}
	elseif($_REQUEST['action'] == 'check_archive')
	{
		$NS = Array();
		$NS['arc_name'] = $name = DOCUMENT_ROOT.BX_ROOT.'/backup/'.str_replace(array('..','/','\\'),'',$_REQUEST['f_id']);
		$NS['step'] = 5;
		$NS['dump_encrypt_key'] = '111111';
	}
	else
	{
		$NS = $_SESSION['BX_DUMP_STATE'];
		$ar = unserialize(COption::GetOptionString("main","skip_mask_array"));
		$skip_mask_array = is_array($ar) ? $ar : array();
	}

	$after_file = str_replace('.sql','_after_connect.sql',$NS['dump_name']);

	// Step 1: Dump
	if($NS["step"] == 1)
	{
		if (IntOption('dump_base'))
		{
			$bres = CBackup::BaseDump($NS["dump_name"], intval($NS["num"]), intval($NS["st_row"]));
			$NS["ptab"] = $bres["ptab"];
			$NS["num"] = $bres["num"];
			$NS["st_row"] = $bres["st_row"];

			if ($bres['end'])
			{
				$rs = $DB->Query('SHOW VARIABLES LIKE "character_set_results"');
				if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
					file_put_contents($after_file, "SET NAMES '".$f['Value']."';\n");

				$rs = $DB->Query('SHOW VARIABLES LIKE "collation_database"');
				if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
					file_put_contents($after_file, "ALTER DATABASE `<DATABASE>` COLLATE ".$f['Value'].";\n",8);

				$NS["step"]++;
			}
		}
		else
			$NS["step"]++;
	}

	// Step 2: pack dump 
	if($NS["step"] == 2)
	{
		if (IntOption('dump_base'))
		{
			if (haveTime())
			{
				$tar = new CTar;
				$tar->EncryptKey = $NS['dump_encrypt_key'];
				$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
				$tar->gzip = IntOption('dump_use_compression');
				$tar->path = DOCUMENT_ROOT;
				$tar->ReadBlockCurrent = intval($NS['ReadBlockCurrent']);

				if (!$tar->openWrite($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

				if (!$tar->ReadBlockCurrent && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
					$tar->addFile($f);

				$Block = $tar->Block;
				while(haveTime() && ($r = $tar->addFile($NS['dump_name'])) && $tar->ReadBlockCurrent > 0);
				$NS["data_size"] += 512 * ($tar->Block - $Block);
				
				if ($r === false)
					RaiseErrorAndDie(implode('<br>',$tar->err));

				$NS["ReadBlockCurrent"] = $tar->ReadBlockCurrent;

				if($tar->ReadBlockCurrent == 0)
				{
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

					$NS["step"]++;
				}
				$tar->close();
			}
		}
		else
			$NS["step"]++;
	}

	// Step 3: Download Cloud Files 
	if($NS["step"] == 3)
	{
		if ($arDumpClouds = CBackup::CheckDumpClouds())
		{
			if (haveTime())
			{
				foreach($arDumpClouds as $arBucket)
				{
					$id = $arBucket['ID'];
					if ($NS['bucket_finished_'.$id])
						continue;

					$obCloud = new CloudDownload($id);
					$obCloud->last_bucket_path = $NS['last_bucket_path'];
					if ($res = $obCloud->Scan(''))
						$NS['bucket_finished_'.$id] = true;
					else // partial
					{
						$NS['last_bucket_path'] = $obCloud->path;
						$NS['download_cnt'] += $obCloud->download_cnt;
						$NS['download_size'] += $obCloud->download_size;
						if ($c = count($NS->arSkipped))
							$NS['download_skipped'] += $c;
						break;
					}
				}

				if ($res) // finish
					$NS['step']++;
			}
		}
		else
			$NS["step"]++;
	}
	
	// Step 4: Tar Files
	if($NS["step"] == 4)
	{
		if (CBackup::CheckDumpFiles() || CBackup::CheckDumpClouds())
		{
			if (haveTime())
			{
				$DOCUMENT_ROOT_SITE = DOCUMENT_ROOT;
				if ($NS['dump_site_id'])
				{
					$rs = CSite::GetList($by='sort', $order='asc', array('ID' => $NS['dump_site_id']));
					if ($f = $rs->Fetch())
					{
						$DOCUMENT_ROOT_SITE = rtrim(str_replace('\\','/',$f['ABS_DOC_ROOT']),'/');
					//	$DIR = rtrim(str_replace('\\','/',$f['DIR']),'/');
					}
				}
				if (!defined('DOCUMENT_ROOT_SITE'))
					define('DOCUMENT_ROOT_SITE', $DOCUMENT_ROOT_SITE);

				$tar = new CTar;
				$tar->EncryptKey = $NS['dump_encrypt_key'];
				$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
				$tar->gzip = IntOption('dump_use_compression');
				$tar->path = DOCUMENT_ROOT_SITE;
				$tar->ReadBlockCurrent = intval($NS['ReadBlockCurrent']);

				if (!$tar->openWrite($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

				$Block = $tar->Block;
				$DirScan = new CDirRealScan;

				if (!$NS['startPath'])
				{
					if (!IntOption('dump_base') && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
						$tar->addFile($f);
				}
				else
					$DirScan->startPath = $NS['startPath'];

				$r = $DirScan->Scan(DOCUMENT_ROOT_SITE);//.$DIR);
				$NS["data_size"] += 512 * ($tar->Block - $Block);
				$tar->close();

				if ($r === false)
					RaiseErrorAndDie(implode('<br>',$DirScan->err));

				$NS["ReadBlockCurrent"] = $tar->ReadBlockCurrent;
				$NS["startPath"] = $DirScan->nextPath;
				$NS["cnt"] += $DirScan->FileCount;

				if ($r !== 'BREAK') // finish
				{
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
					$NS["step"]++;
				}
			}
		}
		else
			$NS["step"]++;
	}

	// Step 5: Integrity check
	if($NS["step"] == 5)
	{
		if (IntOption('dump_integrity_check') || $NS['check_archive'])
		{
			if (haveTime())
			{
				$tar = new CTarCheck;
				$tar->EncryptKey = $NS['dump_encrypt_key'];

				if (!$tar->openRead($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS_READ').'<br>'.implode('<br>',$tar->err));
				else
				{
					if(($Block = intval($NS['Block'])) && !$tar->SkipTo($Block))
						RaiseErrorAndDie(implode('<br>',$tar->err));

					while(($r = $tar->extractFile()) && haveTime());

					$NS["Block"] = $tar->Block;

					if ($r === false)
						RaiseErrorAndDie(implode('<br>',$tar->err));
					if ($r === 0)
						$NS["step"]++;
				}
				$tar->close();
			}
		}
		else
			$NS["step"]++;
	}

	// Step 6: Send to the cloud 
	if($NS["step"] == 6)
	{
		if ($NS['BUCKET_ID'])
		{
			if (haveTime())
			{
				if (!CModule::IncludeModule('clouds'))
					RaiseErrorAndDie(GetMessage("MAIN_DUMP_NO_CLOUDS_MODULE"));

				$file_size = filesize($NS["arc_name"]);
				$obUpload = new CCloudStorageUpload(substr($NS['arc_name'],strlen(DOCUMENT_ROOT)));
				if (!$obUpload->isStarted())
				{
					if (!$obUpload->Start(intval($NS['BUCKET_ID']), $file_size))
					{
						if ($e = $APPLICATION->GetException())
							$strError = $e->GetString();
						else
							$strError = GetMessage('MAIN_DUMP_INT_CLOUD_ERR');
						RaiseErrorAndDie($strError);
					}
				}

				if ($fp = fopen($NS['arc_name'],'rb'))
				{
					fseek($fp, $obUpload->getPos());
					$part = fread($fp, $obUpload->getPartSize());
					fclose($fp);
					while($obUpload->hasRetries())
						if($res = $obUpload->Next($part))
							break;
					if ($res)
					{
						$pos = $obUpload->getPos();
						if ($pos >= $file_size) // file ended
						{
							if($obUpload->Finish())
							{
								$NS['pos'] += $file_size;
								$oBucket = new CCloudStorageBucket($NS['BUCKET_ID']);
								$oBucket->IncFileCounter($file_size);

								if (file_exists($arc_name = CTar::getNextName($NS['arc_name'])))
									$NS['arc_name'] = $arc_name;
								else
								{
									$name = preg_replace('#\.[0-9]+$#','',$NS['arc_name']);
									while(file_exists($name))
									{
										$size = filesize($name);
										if (unlink($name) && IntOption("disk_space") > 0)
											CDiskQuota::updateDiskQuota("file",$size , "del");
										$name = CTar::getNextName($name);
									}

									$NS["step"]++;
								}
							}
							else
							{
								$obUpload->Delete();
								RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']));
							}
						} // partial
						else
							$pos += $NS['pos'];
					}
					else
					{
						$obUpload->Delete();
						RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']));
					}
				}
				else
					RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_OPEN_FILE").$NS['arc_name']);
			}
		}
		else
			$NS["step"]++;
	}


	$NS["time"] += workTime();
	$_SESSION['BX_DUMP_STATE'] = $NS;

	if ($NS["step"] > 6) // Finish
	{
		$title = $NS['cloud_send'] ? GetMessage("MAIN_DUMP_SUCCESS_SENT") : GetMessage("MAIN_DUMP_FILE_FINISH");
		$status_msg = '';
		
		if ($NS["num"])
			$status_msg .= GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".$NS["num"]."</b><br>";
		if ($NS["cnt"])
			$status_msg .= GetMessage("MAIN_DUMP_FILE_CNT")." <b>".$NS["cnt"]."</b><br>";
		if ($NS["data_size"])
			$status_msg .= GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".HumanSize($NS["data_size"])."</b><br>";
		if ($NS["arc_size"])
			$status_msg .= GetMessage("MAIN_DUMP_ARC_SIZE")." <b>".HumanSize($NS["arc_size"])."</b><br>";

		$status_msg .= GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';

		CAdminMessage::ShowMessage(array(
			"MESSAGE" => $title,
			"DETAILS" => $status_msg ,
			"TYPE" => "OK",
			"HTML" => true));

?>
		<?echo bitrix_sessid_post()?>
		<script>
			EndDump();
			RefreshList();
		</script>
<?
	}
	else // Partial
	{
		switch ($NS['step'])
		{
			case 1:
				$status_title = GetMessage('DUMP_DB_CREATE');
				$status_msg = GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".($NS["num"])."</b>"
					.'<br>'.GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
			break;
			case 2:
				$arc_size = file_exists($NS['arc_name']) ? filesize($NS['arc_name']) : 0;
				$status_title = GetMessage("MAIN_DUMP_DB_PROC");
				$status_msg = 
				GetMessage('CURRENT_POS').' <b>'.HumanSize($arc_size) .'</b>  (<b>'.round(100 * $arc_size / filesize($NS['dump_name'])).'%</b>) '
					.'<br>'.GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
			break;
			case 3:
				$status_title = GetMessage("MAIN_DUMP_CLOUDS_DOWNLOAD");
				$status_msg = GetMessage("MAIN_DUMP_FILES_DOWNLOADED").": <b>".intval($NS["download_cnt"])."</b><br>".
				GetMessage("MAIN_DUMP_FILES_SIZE").": <b>".HumanSize($NS["download_size"])."</b><br>";
				if ($NS['download_skipped'])
					$status_msg .= GetMessage("MAIN_DUMP_DOWN_ERR_CNT").': <b>'.$NS['download_skipped'].'</b><br>';
				$status_msg .= GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
			break;
			case 4:
				$status_title = GetMessage("MAIN_DUMP_SITE_PROC");
				$status_msg = GetMessage("MAIN_DUMP_FILE_CNT")." <b>".intval($NS["cnt"])."</b><br>".
				GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".HumanSize($NS["data_size"])."</b> ";

				if (is_object($DirScan))
					$status_msg.= '<br>'.GetMessage('DUMP_CUR_PATH').' <b>'.substr($DirScan->nextPath,strlen(DOCUMENT_ROOT_SITE)).'</b>';

				$status_msg .= '<br>'.GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
			break;
			case 5:
				$status_title = GetMessage('INTEGRITY_CHECK');
				$status_msg = GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".HumanSize($NS["arc_size"])."</b><br>".
				GetMessage('CURRENT_POS').' <b>'.HumanSize($NS['Block'] * 512).'</b>  '.($NS['data_size'] ? '(<b>'.round(100 * $NS['Block'] * 512 / $NS['data_size']).'%</b>) ' : '')
					.'<br>'.GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
			break;
			case 6:
				$status_title = GetMessage("MAIN_DUMP_FILE_SENDING");
				$status_msg = GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".HumanSize($NS["arc_size"])."</b><br>".
				GetMessage('CURRENT_POS').' <b>'.HumanSize($pos).'</b>  (<b>'.round(100 * $pos / $NS['arc_size']).'%</b>) '
					.'<br>'.GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';
		}

		CAdminMessage::ShowMessage(array(
			"MESSAGE" => $status_title,
			"DETAILS" =>  $status_msg,
			"TYPE" => "OK",
			"HTML" => true));
			
//		echo '<input type=button onclick="AjaxSend(\'?process=Y&'.bitrix_sessid_get().'\')" value="Next">';
		?>
		<script>
			window.setTimeout("if(!stop)AjaxSend('?process=Y&<?=bitrix_sessid_get()?>')",<?=1000 * IntOption("dump_max_exec_time_sleep")?>);
		</script>
		<?
	}
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
	die();
}

if ($_REQUEST['action'])
{
	if (!check_bitrix_sessid())
		die(GetMessage("DUMP_MAIN_SESISON_ERROR"));

	if ($_REQUEST['action'] == 'key_info')
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");
		$arUpdateList = CUpdateClient::GetUpdatesList($errorMessage, LANGUAGE_ID, $stableVersionsOnly);
		if (is_array($ar = &$arUpdateList['ERROR'][0]))
			echo '<div style="color:red">'.GetMessage("DUMP_MAIN_ERROR").$ar['#'].'</div>';
		elseif (is_array($ar = &$arUpdateList['CLIENT'][0]['@']))
		{
			echo '<table style="margin:4px;padding:2px;border:1px solid #ccc">'.
			'<tr><td>'.GetMessage("DUMP_MAIN_REGISTERED").':</td><td>'.htmlspecialcharsbx($ar['NAME']).'</td></tr>'.
			'<tr><td>'.GetMessage("DUMP_MAIN_EDITION").':</td><td>'.htmlspecialcharsbx($ar['LICENSE']).'</td></tr>'.
			'<tr><td>'.GetMessage("DUMP_MAIN_ACTIVE_FROM").':</td><td>'.$ar['DATE_FROM'].'</td></tr>'.
			'<tr><td>'.GetMessage("DUMP_MAIN_ACTIVE_TO").':</td><td>'.$ar['DATE_TO'].'</td></tr>'.
			'</table>';
		}
		else
			echo '<div style="color:red">'.GetMessage("DUMP_MAIN_ERR_GET_INFO").'</div>';
		die();
	}
	elseif ($_REQUEST['action'] == 'download')
	{
		$name = $path.'/'.$_REQUEST['f_id'];
		echo '<script>';

		if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
				{
					while($obBucket->FileExists($name))
					{
						echo 'window.open("'.htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name))).'");'."\n";
						$name = CTar::getNextName($name);
					}
				}
			}
		}
		else
		{
			while(file_exists(DOCUMENT_ROOT.$name))
			{
				echo 'window.open("'.htmlspecialcharsbx($name).'");'."\n";
				$name = CTar::getNextName($name);
			}
		}
		echo '
			EndDump();
		</script>';
		die();
	}
	elseif ($_REQUEST['action'] == 'copy')
	{
		$name = $path.'/'.$_REQUEST['f_id'];
		echo '
		<script>
		';

		$url = '';
		if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
					$url = htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name)));
			}
		}
		else
		{
			$host = COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']);
			$url = 'http://'.htmlspecialcharsbx($host.$name);
		}
		if ($url)
			echo 'window.prompt("'.GetMessage("MAIN_DUMP_USE_THIS_LINK").' restore.php", "'.htmlspecialcharsbx($url).'");'."\n";
		echo '
			EndDump();
		</script>';
		die();
	}
	elseif ($_REQUEST['action'] == 'restore')
	{
		if (!copy($f = DOCUMENT_ROOT.BX_ROOT.'/modules/main/admin/restore.php', DOCUMENT_ROOT.'/restore.php'))
			RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_COPY_FILE").htmlspecialcharsbx($f));

		$url = '';
		$name = $path.'/'.$_REQUEST['f_id'];
		if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
					$url = 'arc_down_url='.htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name)));
			}
		}
		else
			$url = 'local_arc_name='.htmlspecialcharsbx($name);
		if ($url)
			echo '<script>document.location = "/restore.php?Step=1&'.$url.'";</script>';
		echo '<script>EndDump();</script>';
		die();
	}
}
$DB->Query("UNLOCK TABLES",true);

######### Admin list #######
$arFilterFields = array();
$lAdmin->InitFilter($arFilterFields);
$lAdmin->BeginPrologContent();

$site = CSite::GetSiteByFullPath(DOCUMENT_ROOT);
if ($arID = $lAdmin->GroupAction())
{
	foreach ($arID as $ID)
	{

		if (strlen($ID) <= 0)
			continue;

		if ($APPLICATION->GetFileAccessPermission(array($site, $path."/".$ID))< "W")
			continue;
		switch ($_REQUEST['action'])
		{
			case "delete":
				if (preg_match('#^([0-9]+)_(.+)$#', $ID, $regs))
				{
					$BUCKET_ID = $regs[1];
					$item = $regs[2];

					if ($BUCKET_ID)
					{
						if (CModule::IncludeModule('clouds'))
						{
							$obBucket = new CCloudStorageBucket($BUCKET_ID);
							if ($obBucket->Init())
							{
								$name = $path.'/'.$item;
								while($obBucket->FileExists($name))
								{
									$file_size = $obBucket->GetFileSize($name);
									if ($obBucket->DeleteFile($name))
										$obBucket->DecFileCounter($file_size);
									$name = CTar::getNextName($name);
								}

								$e = $APPLICATION->GetException();
								if(is_object($e))
									$lAdmin->AddGroupError($e->GetString(), $ID);
							}
							else
								$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_INIT_CLOUD"), $ID);
						}
					}
					else
					{
						while(file_exists(DOCUMENT_ROOT.$path.'/'.$item))
						{
							if ($strWarning_tmp = CFileMan::DeleteEx(Array($site, CFileMan::NormalizePath($path."/".$item))))
								$lAdmin->AddGroupError($strWarning_tmp, $ID);

							$item = CTar::getNextName($item);
						}
					}
				}
			break;
			case "rename":
				if (preg_match('#^[a-z0-9\-\._]+$#i',$_REQUEST['name']))
				{
					$arName = ParseFileName($_REQUEST['ID']);
					$new_name = $_REQUEST['name'].'.'.$arName['ext'];

					if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
					{
						// Not realized 'cos no cloud API
					}
					else
					{
						while(file_exists(DOCUMENT_ROOT.$path.'/'.$ID))
						{
							if (!rename(DOCUMENT_ROOT.$path.'/'.$ID, DOCUMENT_ROOT.$path.'/'.$new_name))
							{
								$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_FILE_RENAME").htmlspecialcharsbx($ID), $ID);
								break;
							}

							$ID = CTar::getNextName($ID);
							$new_name = CTar::getNextName($new_name);
						}
					}
				}
				else
					$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_NAME"), $ID);
			break;
		}
	}
}
InitSorting();

$arDirs = array();
$arFiles = array();
$arTmpFiles = array();
$arFilter = array();

GetDirList(Array($site, $path), $arDir, $arTmpFiles, $arFilter, Array($by=>$order), "F");

// Clouds
if (($arAllBucket = CBackup::GetBucketList()) && $_REQUEST['mode'])
{
	foreach($arAllBucket as $arBucket)
	{
		if ($arCloudFiles = CBackup::GetBucketFileList($arBucket['ID'], BX_ROOT.'/backup/'))
		{
			foreach($arCloudFiles['file'] as $k=>$v)
			{
				$arTmpFiles[] = array(
					'NAME' => $v,
					'SIZE' => $arCloudFiles['file_size'][$k],
					'DATE' => '',
					'PERMISSION' => 'X',
					'BUCKET_ID' => $arBucket['ID'],
					'PLACE' => htmlspecialcharsbx($arBucket['BUCKET'].' ('.$arBucket['SERVICE_ID'].')')

				);
			}
		}
	}
}
$arWriteBucket = CBackup::GetBucketList($arFilter = array('READ_ONLY' => 'Y'));

$arParts = array();
$arSize = array();
foreach($arTmpFiles as $k=>$ar)
{
	if (preg_match('#^(.*\.(enc|tar|gz))(\.[0-9]+)?$#',$ar['NAME'],$regs))
	{
		$arParts[intval($ar['BUCKET_ID']).$regs[1]]++;
		$arSize[$regs[1]] += $ar['SIZE'];
		if (!$regs[3])
			$arFiles[] = $ar;
	}
}

$rsDirContent = new CDBResult;
$rsDirContent->InitFromArray($arFiles);
$rsDirContent->sSessInitAdd = $path;
$rsDirContent = new CAdminResult($rsDirContent, $sTableID);
$rsDirContent->NavStart(20);

// установка строки навигации
$lAdmin->NavText($rsDirContent->GetNavPrint(GetMessage("MAIN_DUMP_FILE_PAGES")));
$lAdmin->AddHeaders(array(
		array("id"=>"NAME", "content"=>GetMessage("MAIN_DUMP_FILE_NAME"), "sort"=>"name", "default"=>true),
		array("id"=>"SIZE","content"=>GetMessage("MAIN_DUMP_FILE_SIZE1"), "sort"=>"size", "default"=>true),
		$arAllBucket ? array("id"=>"PLACE","content"=>GetMessage("MAIN_DUMP_LOCATION"), "default"=>true) : null,
		array("id"=>"DATE", "content"=>GetMessage('MAIN_DUMP_FILE_TIMESTAMP'), "sort"=>"timestamp", "default"=>true)
));

while($Elem = $rsDirContent->NavNext(true, "f_"))
{
	$BUCKET_ID = intval($f_BUCKET_ID);
	$row =& $lAdmin->AddRow($BUCKET_ID.'_'.$f_NAME, $Elem);

	$c = $arParts[$BUCKET_ID.$f_NAME];
	if ($c > 1)
	{
		$parts = ' ('.GetMessage("MAIN_DUMP_PARTS").$c.')';
		$size = $arSize[$f_NAME];
	}
	else
	{
		$parts = '';
		$size = $f_SIZE;
	}

	$row->AddField("NAME", '<img src="/bitrix/images/fileman/types/'.CFileMan::GetFileTypeEx($f_NAME).'.gif" width="16" height="16" border=0 alt="">&nbsp;'.$f_NAME.$parts);
	$row->AddField("SIZE", HumanSize($size));
	$row->AddField("PLACE", $f_PLACE ? $f_PLACE : GetMessage("MAIN_DUMP_LOCAL"));
	$row->AddField("DATE", $f_DATE);

	$arActions = Array();

	if ($f_PERMISSION >= "R")
	{
		$arActions[] = array(
			"ICON" => "download",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MAIN_DUMP_ACTION_DOWNLOAD"),
			"ACTION" => "AjaxSend('?action=download&f_id=".$f_NAME."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
		);
		$arActions[] = array(
			"ICON" => "link",
			"TEXT" => GetMessage("MAIN_DUMP_GET_LINK"),
			"ACTION" => "AjaxSend('?action=copy&f_id=".$f_NAME."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
		);
		$arActions[] = array(
			"ICON" => "restore",
			"TEXT" => GetMessage("MAIN_DUMP_RESTORE"),
			"ACTION" => "if(confirm('".GetMessage("MAIN_RIGHT_CONFIRM_EXECUTE")."')) AjaxSend('?action=restore&f_id=".$f_NAME."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
		);

		if ($f_PERMISSION >= 'W')
		{
			if ($arWriteBucket && !$BUCKET_ID)
			{
				$arActions[] = array("SEPARATOR" => true);

				foreach($arWriteBucket as $f)
					$arActions[] = array(
						"ICON" => "clouds",
						"TEXT" => GetMessage("MAIN_DUMP_SEND_CLOUD").htmlspecialcharsbx('"'.$f['BUCKET'].'"'),
						"ACTION" => "if(confirm('".GetMessage("MAIN_DUMP_SEND_FILE_CLOUD")."?')) AjaxSend('?f_id=".urlencode($f_NAME)."&process=Y&action=cloud_send&dump_bucket_id=".$f['ID']."&".bitrix_sessid_get()."')"
					);
			}

			$arActions[] = array("SEPARATOR" => true);

			if (defined('DUMP_DEBUG_MODE'))
				$arActions[] = array(
					"ICON" => "archive",
					"TEXT" => 'DEBUG - '.GetMessage("INTEGRITY_CHECK"),
					"ACTION" => "AjaxSend('?f_id=".urlencode($f_NAME)."&process=Y&action=check_archive&".bitrix_sessid_get()."')"
				);

			if (!$BUCKET_ID)
			{
				$arName = ParseFileName($f_NAME);
				$arActions[] = array(
					"ICON" => "rename",
					"TEXT" => GetMessage("MAIN_DUMP_RENAME"),
					"ACTION" => "if(name=prompt('".GetMessage("MAIN_DUMP_ARC_NAME_W_O_EXT")."','".htmlspecialcharsbx($arName['name'])."')) tbl_dump.GetAdminList('/bitrix/admin/dump.php?ID=".urlencode($f_NAME)."&action=rename&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."&BUCKET_ID=".$BUCKET_ID."&name='+name);"
				);
			}
			$arActions[] = array(
				"ICON" => "delete",
				"TEXT" => GetMessage("MAIN_DUMP_DELETE"),
				"ACTION" => "if(confirm('".GetMessage('MAIN_DUMP_ALERT_DELETE')."')) ".$lAdmin->ActionDoGroup($BUCKET_ID.'_'.$f_NAME, "delete")
			);
		}
	}
	$row->AddActions($arActions);
}

// "подвал" списка
$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $rsDirContent->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE")
	)
);

$lAdmin->CheckListMode();


$APPLICATION->SetTitle(GetMessage("MAIN_DUMP_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
$bBitrixCloud = 0;

$aTabs = array();
$aTabs[] = array("DIV"=>"std", "TAB"=>GetMessage("TAB_STANDARD"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TAB_STANDARD_DESC"));
if ($bBitrixCloud)
	$aTabs[] = array("DIV"=>"bxcloud", "TAB"=>GetMessage("DUMP_MAIN_BITRIX_CLOUD"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("DUMP_MAIN_BITRIX_CLOUD_DESC"));
$aTabs[] = array("DIV"=>"adv", "TAB"=>GetMessage("TAB_ADVANCED"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TAB_ADVANCED_DESC"));

$editTab = new CAdminTabControl("editTab", $aTabs, true, true);

?>

<div id="dump_result_div"></div>
<? 
if (defined('DUMP_DEBUG_MODE'))
	echo '<div style="color:red">DEBUG MODE</div>
	<input type=button value=Next onclick="AjaxSend(\'?process=Y&'.bitrix_sessid_get().'\')">';
echo BeginNote();
echo GetMessage("MAIN_DUMP_HEADER_MSG");
echo EndNote();

CAdminFileDialog::ShowScript(
	Array
	(
		"event" => "__bx_select_dir",
		"arResultDest" => Array("FUNCTION_NAME" => "mnu_SelectValue"),
		"arPath" => Array('PATH'=>"/"),
		"select" => 'D',
		"operation" => 'O',
		"showUploadTab" => false,
		"showAddToMenuTab" => false,
		"allowAllFiles" => true,
		"SaveConfig" => true 
	)
);		
?>
<script language="JavaScript">
function GetLicenseInfo()
{
	ShowWaitWindow();
	CHttpRequest.Action = function(result)
	{
		CloseWaitWindow();
		document.getElementById('license_info').innerHTML = result;
	}
	CHttpRequest.Send('?action=key_info&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
}

var numRows=0;
function AddTableRow()
{
	oTable = document.getElementById('skip_mask_table');
	numRows = oTable.rows.length;
	oRow = oTable.insertRow(-1);
	oCell = oRow.insertCell(0);
	oCell.innerHTML = '<input name="arMask[]" id="mnu_FILES_' + numRows  +'" size=30><input type="button" id="mnu_FILES_btn_' + numRows  + '" value="..." onclick="showMenu(this, '+ numRows  +')">';
}

var currentID;

function showMenu(div, id)
{
	currentID = id;
	__bx_select_dir();
}

function mnu_SelectValue(filename, path, site, title, menu)
{
	document.getElementById('mnu_FILES_' + currentID).value = path + (path == '/' ? '' : '/') + filename;
}

function CheckActiveStart()
{

	if (bxc = document.fd1.bxc_enable)
	{
		document.fd1.bxc_password.disabled = !bxc.checked;
		if (ob = document.fd1.dump_bucket_id)
			ob.disabled = bxc.checked;
		if (ob = document.fd1.dump_encrypt)
		{
			if (bxc.checked)
				ob.checked = true;
			ob.disabled = bxc.checked;
		}
		if (ob = document.fd1.dump_encrypt_key)
			ob.disabled = bxc.checked;
		if (ob = document.fd1.dump_encrypt_key_confirm)
			ob.disabled = bxc.checked;
	}

	if (ob = document.fd1.dump_base)
	{
		document.fd1.dump_base_skip_stat.disabled = !ob.checked;
		document.fd1.dump_base_skip_search.disabled = !ob.checked;
		document.fd1.dump_base_skip_log.disabled = !ob.checked;
	}

	noFiles = !document.fd1.dump_file_public.checked && !document.fd1.dump_file_kernel.checked;
	document.fd1.max_file_size.disabled = noFiles;
	document.fd1.skip_symlinks.disabled = noFiles;
	document.fd1.skip_mask.disabled = noFiles;

	if (ob = document.fd1.dump_site_id)
		ob.disabled = !document.fd1.dump_file_public.checked;

	noMask = noFiles || !document.fd1.skip_mask.checked;

	oTable = document.getElementById('skip_mask_table');
	numRows = oTable.rows.length;
	for(i=0;i<numRows;i++)
	{
		document.getElementById('mnu_FILES_'+i).disabled = noMask;
		document.getElementById('mnu_FILES_btn_'+i).disabled = noMask;
	}

	<?
	if ($arAllBucket)
	{
		foreach($arAllBucket as $arBucket)
			echo 'noFiles = noFiles && !document.getElementById("dump_cloud_'.$arBucket['ID'].'").checked'."\n";
	}
	?>

	document.getElementById('more_button').disabled = noMask;
	document.getElementById('start_button').disabled = noFiles && !document.fd1.dump_base.checked;
}

function CheckActiveMode()
{
	standard = document.fd1.dump_file_public.checked && document.fd1.dump_file_kernel.checked && (document.fd1.max_file_size.value == 0) && !document.fd1.skip_symlinks.checked && !document.fd1.skip_mask.checked && document.fd1.dump_integrity_check.checked;

	if (standard && (ob_dump = document.fd1.dump_base))
		standard = ob_dump.checked && !document.fd1.dump_base_skip_stat.checked && !document.fd1.dump_base_skip_search.checked && !document.fd1.dump_base_skip_log.checked;

	if (!standard)
		return;

	if (document.fd1.dump_max_exec_time.value == 20 && document.fd1.dump_max_exec_time_sleep.value == 3 && !document.fd1.dump_disable_gzip.checked)
		document.getElementById('shared_profile').checked = true;
	else if (document.fd1.dump_max_exec_time.value == 45 && document.fd1.dump_max_exec_time_sleep.value == 0 && !document.fd1.dump_disable_gzip.checked)
		document.getElementById('vps_profile').checked = true;
	else if (document.fd1.dump_max_exec_time.value == 15 && document.fd1.dump_max_exec_time_sleep.value == 10 && document.fd1.dump_disable_gzip.checked)
		document.getElementById('slow_profile').checked = true;

	CheckActiveStart();
}

BX.ready(function()
	{
		CheckActiveMode();
		<?=$arAllBucket ? 'RefreshList();' : ''?>
	}
);

function SetMode(val)
{
	document.fd1.dump_file_public.checked = true;
	document.fd1.dump_file_kernel.checked = true;
	document.fd1.max_file_size.value = 0;
	document.fd1.skip_symlinks.checked = false;
	document.fd1.skip_mask.checked = false;

	if (ob_dump = document.fd1.dump_base)
	{
		ob_dump.checked = true;
		document.fd1.dump_base_skip_stat.checked = false;
		document.fd1.dump_base_skip_search.checked = false;
		document.fd1.dump_base_skip_log.checked = false;
	}


	switch (val)
	{
		case 'shared':
			document.fd1.dump_max_exec_time.value = 20;
			document.fd1.dump_max_exec_time_sleep.value = 3;
			document.fd1.dump_disable_gzip.checked = false;
			document.fd1.dump_integrity_check.checked = true;
		break;
		case 'vps':
			document.fd1.dump_max_exec_time.value = 45;
			document.fd1.dump_max_exec_time_sleep.value = 0;
			document.fd1.dump_disable_gzip.checked = false;
			document.fd1.dump_integrity_check.checked = true;
		break;
		case 'slow':
		default:
			document.fd1.dump_max_exec_time.value = 15;
			document.fd1.dump_max_exec_time_sleep.value = 10;
			document.fd1.dump_disable_gzip.checked = true;
			document.fd1.dump_integrity_check.checked = true;
		break;
	}
	CheckActiveStart();
}

var stop;
function StartDump()
{
	if ((ob = document.fd1.dump_encrypt) && ob.checked)
	{
		ob = document.fd1.dump_encrypt_key;
		l = ob.value.length;

		strError = '';
		if (!l)
			strError = "<?=GetMessage("MAIN_DUMP_EMPTY_PASS")?>";
		else if (l < 6)
			strError = "<?=GetMessage("MAIN_DUMP_ENC_PASS_DESC")?>";
<? if (LANGUAGE_ID == 'ru') { ?>
		else if (/[<?=GetMessage("MAIN_DUMP_CYR_LETTERS")?>]/.test(ob.value))
			strError = "<?=GetMessage("MAIN_DUMP_ENC_ERR_CYR")?>";
		else if (ob.value != document.forms.fd1.dump_encrypt_key_confirm.value)
			strError = "<?=GetMessage("DUMP_MAIN_ERR_PASS_CONFIRM")?>";
<? } ?>
		if (strError)
		{
			alert(strError);
			editTab.SelectTab('adv');
			ob.focus();
			return false;
		}
	}

	queryString='lang=<?echo htmlspecialcharsbx(LANGUAGE_ID)?>';
	queryString+='&process=Y';
	queryString+='&action=start';

	queryString+='&dump_max_exec_time=' + encodeURIComponent(document.fd1.dump_max_exec_time.value);
	queryString+='&dump_max_exec_time_sleep=' + encodeURIComponent(document.fd1.dump_max_exec_time_sleep.value);
	queryString+='&dump_archive_size_limit=' + encodeURIComponent(document.fd1.dump_archive_size_limit.value);

	if (document.fd1.dump_disable_gzip.checked)
		queryString += '&dump_disable_gzip=Y';

	if (document.fd1.dump_integrity_check.checked)
		queryString += '&dump_integrity_check=Y';
	
	if (ob = document.fd1.dump_bucket_id)
		queryString += '&dump_bucket_id=' + ob.value;

	if(document.fd1.dump_file_public.checked)
		queryString +='&dump_file_public=Y';

	if(document.fd1.dump_file_kernel.checked)
			queryString+='&dump_file_kernel=Y';

	if(document.fd1.skip_symlinks.checked)
			queryString+='&skip_symlinks=Y';

	if(document.fd1.skip_mask.checked)
	{
		queryString+='&skip_mask=Y';

		oTable = document.getElementById('skip_mask_table');
		numRows = oTable.rows.length;

		for(i=0;i<numRows;i++)
			queryString+='&arMask[]=' + document.getElementById('mnu_FILES_'+i).value;
	}

	if(document.fd1.dump_file_public.checked || document.fd1.dump_file_kernel.checked)
		queryString+='&max_file_size=' + document.fd1.max_file_size.value;

	if(document.fd1.dump_base.checked)
	{
		queryString +='&dump_base=Y';

		if(document.fd1.dump_base_skip_stat.checked)
			queryString +='&dump_base_skip_stat=Y';
		if(document.fd1.dump_base_skip_search.checked)
			queryString +='&dump_base_skip_search=Y';
		if(document.fd1.dump_base_skip_log.checked)
			queryString +='&dump_base_skip_log=Y';
	}

	if (ob = document.fd1.dump_site_id)
		queryString += '&dump_site_id=' + ob.value;

	<?
	if ($arAllBucket)
	{
		foreach($arAllBucket as $arBucket)
		{
		?>
			if (document.getElementById('dump_cloud_<?=$arBucket['ID']?>').checked)
				queryString += '&dump_cloud[<?=$arBucket['ID']?>]=Y';
		<?
		}
	}
	?>

	if ((ob = document.fd1.dump_encrypt) && ob.checked)
		queryString += '&dump_encrypt_key=' + encodeURIComponent(document.fd1.dump_encrypt_key.value);

	queryString += '&<?=bitrix_sessid_get()?>';

	document.getElementById('dump_result_div').innerHTML='';
	AjaxSend('dump.php', queryString);
}

function EndDump()
{
	stop = true;
	document.getElementById('stop_button').disabled = true;
	document.getElementById('start_button').disabled = false;
}

function AjaxSend(url, data)
{
	stop = false;
	document.getElementById('stop_button').disabled=false;
	document.getElementById('start_button').disabled=true;

	ShowWaitWindow();
	CHttpRequest.Action = function(result)
	{
		CloseWaitWindow();
		if (stop)
			RefreshList();
		else
			document.getElementById('dump_result_div').innerHTML = result;
	}
	if (data)
		CHttpRequest.Post(url, data);
	else
		CHttpRequest.Send(url);
}

function RefreshList()
{
	tbl_dump.GetAdminList('/bitrix/admin/dump.php?lang=<?=LANGUAGE_ID?>');
}
</script>


	<form name="fd1" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>" method="GET">
	<?
	$editTab->Begin();
	$editTab->BeginNextTab();
	?>

	<tr>
		<td colspan=2 align=center><? 
		echo BeginNote();
		echo GetMessage('MODE_DESC');
		echo EndNote();
		?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><label><input type="radio" name=arc_profile value=shared id='shared_profile' onclick="SetMode(this.value)"> <?=GetMessage('MODE_SHARED')?></label></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><label><input type="radio" name=arc_profile value=vps id='vps_profile' onclick="SetMode(this.value)"> <?=GetMessage('MODE_VPS')?></label></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><label><input type="radio" name=arc_profile value=slow id='slow_profile' onclick="SetMode(this.value)"> <?=GetMessage('MODE_SLOW')?></label></td>
	</tr>
	<?
	if ($bBitrixCloud) // bitrix cloud
	{

		$editTab->BeginNextTab();

		if (!$_REQUEST['bxc_ready'])
		{
			include($_SERVER['DOCUMENT_ROOT'].'/bitrix/license_key.php');
			?>
			<tr class="heading">
				<td colspan="2"><?=GetMessage("DUMP_MAIN_ACTIVATION")?></td>
			</tr>
			<tr>
				<td colspan=2 align=center><? 
				echo BeginNote();
				echo GetMessage("DUMP_MAIN_ACTIVATION_INFO");
				echo EndNote();
				?></td>
			</tr>
			<tr>
				<td valign=top><?=GetMessage("DUMP_MAIN_LICENSE_KEY")?></td>
				<td><?=htmlspecialcharsbx($LICENSE_KEY)?> <a href="/bitrix/admin/settings.php?lang=<?=LANGUAGE_ID?>&mid=main&tabControl_active_tab=edit5" target=_blank><?=GetMessage("DUMP_MAIN_CHANGE")?></a><br><a href="javascript:GetLicenseInfo()"><?=GetMessage("DUMP_MAIN_KEY_INFO")?></a><div id=license_info></div></td>
			</tr>
			<tr>
				<td><?=GetMessage("DUMP_MAIN_PASSWORD_CLOUD")?></td>
				<td><input name="bxc_password" size=30 autocomplete=off></td>
			</tr>
			<tr>
				<td><?=GetMessage("DUMP_MAIN_PASSWORD_CONFIRM")?></td>
				<td><input name="bxc_confirm_password" size=30 autocomplete=off></td>
			</tr>
			<tr>
				<td><?=GetMessage("DUMP_MAIN_AGREE")?>:</td>
				<td><input type="checkbox" name="bxc_rules"></td>
			</tr>
			<tr>
				<td align="center" colspan="2"><input type=button value="<?=GetMessage("DUMP_MAIN_ACT_BUTTON")?>" onclick="document.location='?bxc_ready=Y&<?=$editTab->name?>_active_tab=bxcloud';"></td>
			</tr>
		<?
		}
		else
		{
			$total = 5;
			$used = 1.2;
			$used_p = floor(100 * $used/$total);
		?>
			<style>
				.status-done {
					border:1px solid #CCC;
					border-right:none;
					background:#9ec62e;
					float:left;
	/*				height:14px;*/
					width:<?=ceil(0.9 * $used_p)?>%;
				}
				.status-todo {
					border:1px solid #CCC;
					border-left:none;
					float:left;
	/*				height:14px;*/
					width:<?=floor(0.9 * (100 - $used_p))?>%;
				}
				.status-num {
					float:left;
				}
			</style>
			<tr class="heading">
				<td colspan="2"><?=GetMessage("DUMP_MAIN_SAVE_TO_THE_CLOUD")?></td>
			</tr>
			<tr>
				<td width=40%><?=GetMessage("DUMP_MAIN_DISK_SPACE")?></td>
				<td><?=GetMessage("DUMP_MAIN_USED")?><?=$used?> <?=GetMessage("DUMP_MAIN_FROM")?><?=$total?> <?=GetMessage("DUMP_MAIN_GB")?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="status-done">&nbsp;</div>
					<div class="status-todo">&nbsp;</div>
					<div class="status-num">&nbsp;<?=$used_p?>%</div>
				</td>
			</tr>
			<tr>
				<td><?=GetMessage("DUMP_MAIN_SAVE_TO_1CBITRIX")?></td>
				<td><input name="bxc_enable" type=checkbox onclick="CheckActiveStart()"></td>
			</tr>
			<tr>
				<td><?=GetMessage("DUMP_MAIN_PASSWORD_FLD")?></td>
				<td><input name="bxc_password" size=30 autocomplete=off disabled></td>
			</tr>
			<tr>
				<td colspan=2 align=center><? 
				echo BeginNote();
				echo GetMessage("DUMP_MAIN_USED_PASS");
				echo EndNote();
				?></td>
			</tr>
		<?
		}
	}

	$editTab->BeginNextTab();

	if ($arAllBucket)
	{
	?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("MAIN_DUMP_CLOUD_STORAGES")?></td>
		</tr>
	<?
	}

	if ($arWriteBucket)
	{
	?>
		<tr>
			<td><?=GetMessage("MAIN_DUMP_ARC_LOCATION")?></td>
			<td>
				<select name=dump_bucket_id <?=$bMcrypt?'OnChange="if(this.value>0){document.fd1.dump_encrypt.checked=true;document.fd1.dump_encrypt_key.disabled=false;}"' : ''?>>
				<?
					echo '<option value="0">'.GetMessage("MAIN_DUMP_LOCAL_DISK").'</option>';
					foreach($arWriteBucket as $f)
						echo '<option value="'.$f['ID'].'" '.(IntOption("dump_bucket_id") == $f['ID'] ? "selected" : "").'>'.htmlspecialcharsbx($f['BUCKET'].' ('.$f['SERVICE_ID'].')').'</option>';
				?>
				</select>
			</td>
		</tr>
	<?
	}

	if ($arAllBucket)
	{
	?>
		<tr>
			<td valign=top><?=GetMessage("MAIN_DUMP_ARC_FROM_CLOUD")?></td>
			<td>
				<?
					foreach($arAllBucket as $arBucket)
						echo '<div><label><input type="checkbox" id="dump_cloud_'.$arBucket['ID'].'" OnClick="CheckActiveStart()" '.(IntOption("dump_cloud_".$arBucket['ID']) ? "checked" : "").'> '.htmlspecialcharsbx($arBucket['BUCKET'].' ('.$arBucket['SERVICE_ID'].')').'</label></div>';
				?>
			</td>
		</tr>
	<?
	}
	?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("MAIN_DUMP_SECURITY")?></td>
	</tr>
	<?
	if (!$bMcrypt)
	{
	?>
	<tr>
		<td colspan=2 align=center> 
		<?
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("MAIN_DUMP_NOT_INSTALLED"),
			"DETAILS" => GetMessage("MAIN_DUMP_NO_ENC_FUNCTIONS"),
			"TYPE" => "ERROR",
			"HTML" => true));
		?>
		</td>
	</tr>
	<?
	}
	else
	{
	?>
	<tr>
		<td style="width:50%"><?=GetMessage("MAIN_DUMP_ENABLE_ENC")?></td>
		<td><input type="checkbox" name="dump_encrypt" value="Y" OnClick="document.fd1.dump_encrypt_key.disabled=!this.checked;" <?=(IntOption("dump_encrypt") ? "checked" : "")?> <? if(!$bMcrypt) echo "disabled"; ?>></td>
	</tr>
	<tr>
		<td style="width:50%"><?=GetMessage("MAIN_DUMP_ENC_PASS")?></td>
		<td><input type="password" autocomplete="off" name="dump_encrypt_key" size=20 maxlength=30 <?=(!IntOption("dump_encrypt") ? "disabled" : "")?>></td>
	</tr>
	<tr>
		<td style="width:50%"><?=GetMessage("DUMP_MAIN_PASSWORD_CONFIRM")?></td>
		<td><input type="password" autocomplete="off" name="dump_encrypt_key_confirm" size=20 maxlength=30 <?=(!IntOption("dump_encrypt") ? "disabled" : "")?>></td>
	</tr>
	<tr>
		<td colspan=2 align=center>
		<?
			echo BeginNote();
			echo GetMessage("MAIN_DUMP_SAVE_PASS");
			echo EndNote();
		?>
		</td>
	</tr>
	<?
	}
	?>

	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_DUMP_FILE_TITLE")?></td>
	</tr>
	<tr>
		<td style="width:50%"><?echo GetMessage("MAIN_DUMP_FILE_KERNEL")?></td>
		<td><input type="checkbox" name="dump_file_kernel" value="Y" OnClick="CheckActiveStart()" <?=IntOption("dump_file_kernel") ? "checked" : ''?>></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_PUBLIC")?></td>
		<td><input type="checkbox" name="dump_file_public" value="Y" OnClick="CheckActiveStart()" <?=IntOption("dump_file_public") ? "checked" : ''?>></td>
	</tr>
	<?
	$arSitePath = array();
	$res = CSite::GetList($by='sort', $order='asc', array('ACTIVE'=>'Y'));
	while($f = $res->Fetch())
	{
		$root = rtrim($f['ABS_DOC_ROOT'],'/');
		if (is_dir($root))
			$arSitePath[$root] = array($f['ID'] => '['.$f['ID'].'] '.$f['NAME']);
//		$arSitePath[$f['ABS_DOC_ROOT'].rtrim($f['DIR'],'/')] = array($f['ID'] => '['.$f['ID'].'] '.$f['NAME']);
	}

	if (count($arSitePath) > 1)
	{
	?>
	<tr>
		<td><?=GetMessage('PUBLIC_PART')?></td>
		<td>
			<select name=dump_site_id>
			<?
				foreach($arSitePath as $path => $val)
				{
					$path = rtrim(str_replace('\\','/',$path),'/');
					list($k,$v) = each($val);
					echo '<option value="'.htmlspecialcharsbx($k).'"'.($path == DOCUMENT_ROOT ? ' selected' : '').'>'.htmlspecialcharsbx($v).'</option>';
				}
			?>
			</select>
		</td>
	</tr>
	<?
	}
	?>
	<?
		$bNoFiles = !CBackup::CheckDumpFiles();
	?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE")?></td>
		<td><input type="text" name="max_file_size" size="10" value="<?=IntOption("dump_max_file_size")?>" <?=CBackup::CheckDumpFiles() ? '' : "disabled"?>>
		<?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE_kb")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_SKIP_SYMLINKS")?></td>
		<td><input type="checkbox" name="skip_symlinks" <?=$bNoFiles?'disabled':''?> value="Y" <?=IntOption("skip_symlinks") ? "checked" : ''?>></td>
	</tr>
	<? $bMask = IntOption("skip_mask"); ?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_MASK")?><span class="required"><sup>1</sup></span></td>
		<td><input type="checkbox" name="skip_mask" <?=$bNoFiles?'disabled':''?> value="Y" <?=$bMask?" checked":'';?> onclick="CheckActiveStart()">
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<table id="skip_mask_table" cellspacing=0 cellpadding=0>
			<?
			$i=-1;

			$res = unserialize(COption::GetOptionString("main","skip_mask_array"));
			$skip_mask_array = is_array($res)?$res:array();

			foreach($skip_mask_array as $mask)
			{
				$i++;
				echo
				'<tr><td>
					<input name="arMask[]" id="mnu_FILES_'.$i.'" value="'.htmlspecialcharsbx($mask).'" '.(!$bMask||$bNoFiles?'disabled':'').' size=30>'.
					'<input type="button" id="mnu_FILES_btn_'.$i.'" '.(!$bMask||$bNoFiles?'disabled':'').' value="..." onclick="showMenu(this, \''.$i.'\')">'.
				'</tr>';
			}
			$i++;
			?>
				<tr><td><input name="arMask[]" id="mnu_FILES_<?=$i?>" size=30 <?=!$bMask||$bNoFiles?'disabled':'';?>><input type="button" id="mnu_FILES_btn_<?=$i?>" value="..." onclick="showMenu(this, '<?=$i?>')" <?=!$bMask||$bNoFiles?'disabled':'';?>></tr>
			</table>
			<input type=button id="more_button" value="<?=GetMessage('MAIN_DUMP_MORE')?>" onclick="AddTableRow()" <?=(!$bMask||$bNoFiles?'disabled':'')?>>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_DUMP_BASE_TITLE")?></td>
	</tr>
	<?
	if ($DB->type != 'MYSQL') 
	{
		$strDisableNotMysql = 'disabled';
	?>
	<tr>
		<td colspan=2 align=center><? 
		echo BeginNote();
		echo GetMessage('MAIN_DUMP_MYSQL_ONLY');
		echo EndNote();
		?></td>
	</tr>
	<? 
	} else 
		$strDisableNotMysql = '';
	?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_BASE_TRUE")?></td>
		<td><input type="checkbox" <?=$strDisableNotMysql?> name="dump_base" OnClick="CheckActiveStart()" <?=IntOption("dump_base") ? "checked" : "" ?>><?= " (".getTableSize("")." ".GetMessage("MAIN_DUMP_BASE_SIZE").") " ;?>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("MAIN_DUMP_BASE_IGNORE")?></td>
		<td><label><input type="checkbox" <?=$strDisableNotMysql?> name="dump_base_skip_stat" <?=IntOption("dump_base_skip_stat") ? "checked" : "" ?>> <? echo GetMessage("MAIN_DUMP_BASE_STAT")." (".getTableSize("b_stat")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><label><input type="checkbox" <?=$strDisableNotMysql?> name="dump_base_skip_search" value="Y" <?=IntOption("dump_base_skip_search") ? "checked" : "" ?>> <? echo GetMessage("MAIN_DUMP_BASE_SINDEX")." (".getTableSize("b_search")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><label><input type="checkbox" <?=$strDisableNotMysql?> name="dump_base_skip_log" value="Y"<?=IntOption("dump_base_skip_log") ? "checked" : "" ?>> <? echo GetMessage("MAIN_DUMP_EVENT_LOG")." (".getTableSize("b_event_log")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage('SERVER_LIMIT')?></td>
	</tr>
	<tr>
		<td><?=GetMessage('STEP_LIMIT')?></td>
		<td>
			<input name="dump_max_exec_time" value="<?=IntOption("dump_max_exec_time")?>" size=2>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>,
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_SLEEP")?>
			<input name="dump_max_exec_time_sleep" value="<?=IntOption("dump_max_exec_time_sleep")?>" size=2>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage('DISABLE_GZIP')?></td>
		<td><input type="checkbox" name="dump_disable_gzip" <?=IntOption('dump_use_compression') ? '' : 'checked' ?>>
	</tr>
	<tr>
		<td><?=GetMessage('INTEGRITY_CHECK_OPTION')?></td>
		<td><input type="checkbox" name="dump_integrity_check" <?=IntOption('dump_integrity_check') ? 'checked' : '' ?>>
	</tr>
	<tr>
		<td><?=GetMessage("MAIN_DUMP_MAX_ARCHIVE_SIZE")?></td>
		<td><input name="dump_archive_size_limit" value="<?=intval(COption::GetOptionString('main', 'dump_archive_size_limit', 1024 * 1024 * 1024)) / 1024 / 1024?>" size=4></td>
	</tr>

	<?$editTab->Buttons();
	?>
	<input type="button" id="start_button" value="<?=GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON")?>" <?=!CBackup::CheckDumpFiles() && !CBackup::CheckDumpClouds() && !IntOption("dump_base") ? "disabled" : ''?> OnClick="StartDump();">
	<input type="button" id="stop_button" value="<?=GetMessage("MAIN_DUMP_FILE_STOP_BUTTON")?>" OnClick="EndDump();" disabled>

	<?
	$editTab->End();
	?>
	</form>

<?
$lAdmin->DisplayList();

echo BeginNote();
echo '<span class=required><sup>1</sup></span> '.GetMessage("MAIN_DUMP_FOOTER_MASK");
echo EndNote();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

#################################################
################## FUNCTIONS
function IntOption($name)
{
	static $CACHE;
	if (!$CACHE[$name])
		$CACHE[$name] = COption::GetOptionInt("main", $name, 0);
	return $CACHE[$name];
}

function getTableSize($prefix)
{
	global $DB;
	if ($DB->type != 'MYSQL')
		return 0;
	$size = 0;

	$sql = "SHOW TABLE STATUS LIKE '".$DB->ForSql($prefix)."%'";
	$res = $DB->Query($sql);

	while($row = $res->Fetch())
		$size += $row["Data_length"];

	return round($size/(1048576), 2);
}

function haveTime()
{
	return microtime(true) - START_EXEC_TIME < IntOption("dump_max_exec_time");
}

function workTime()
{
	return microtime(true) - START_EXEC_TIME;
}

function HumanSize($num0, $show0 = false)
{
	$num = $num0;
	$i=0;
	$ar = array(GetMessage('MAIN_DUMP_FILE_MAX_SIZE_b'),GetMessage('MAIN_DUMP_FILE_MAX_SIZE_kb'),GetMessage('MAIN_DUMP_FILE_MAX_SIZE_mb'),GetMessage('MAIN_DUMP_FILE_MAX_SIZE_gb'));
	while($num > 1024)
	{
		$num /= 1024;
		$i++;
	}
	$num = round($num,1);
	return $num." ".$ar[$i].($show0 ? ' ('.$num0.')' : '');
}

function HumanTime($t)
{
	$ar = array(GetMessage('TIME_S'),GetMessage('TIME_M'),GetMessage('TIME_H'));
	if ($t < 60)
		return sprintf('%d '.$ar[0], $t);
	if ($t < 3600)
		return sprintf('%d '.$ar[1].' %d '.$ar[0], floor($t/60), $t%60);
	return sprintf('%d '.$ar[2].'%d '.$ar[1].' %d '.$ar[0], floor($t/3600), floor($t%3600/60), $t%60);
}

function RaiseErrorAndDie($strError)
{
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("MAIN_DUMP_ERROR"),
		"DETAILS" =>  $strError,
		"TYPE" => "ERROR",
		"HTML" => true));
	echo '<script>EndDump();</script>';
	die();
}

function ParseFileName($name)
{
	if (preg_match('#^(.+)\.(tar.*)$#', $name, $regs))
		return array('name' => $regs[1], 'ext' => $regs[2]);
	elseif (preg_match('#^(.+)\.([^\.]+)$#', $name, $regs))
		return array('name' => $regs[1], 'ext' => $regs[2]);
	return array('name' => $name, 'ext' => '');
}
?>
