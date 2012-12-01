<?
$MESS["MAIN_DUMP_FILE_CNT"] = "Dateien komprimiert:";
$MESS["MAIN_DUMP_FILE_SIZE"] = "Dateigröße:";
$MESS["MAIN_DUMP_FILE_FINISH"] = "Backup ist vollständig";
$MESS["MAIN_DUMP_FILE_MAX_SIZE"] = "Dateien ausschließen, die größer sind als (0 - ohne Begrenzung):";
$MESS["MAIN_DUMP_FILE_STEP"] = "Schritt:";
$MESS["MAIN_DUMP_FILE_STEP_SLEEP"] = "Interval:";
$MESS["MAIN_DUMP_FILE_STEP_sec"] = "Sekunden";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_b"] = "B";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_kb"] = "KB";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_mb"] = "MB ";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_gb"] = "GB ";
$MESS["MAIN_DUMP_FILE_DUMP_BUTTON"] = "Archivieren";
$MESS["MAIN_DUMP_FILE_STOP_BUTTON"] = "Stop";
$MESS["MAIN_DUMP_FILE_KERNEL"] = "Kernel archivieren:";
$MESS["MAIN_DUMP_FILE_NAME"] = "Name";
$MESS["FILE_SIZE"] = "Dateigröße";
$MESS["MAIN_DUMP_FILE_TIMESTAMP"] = "Geändert";
$MESS["MAIN_DUMP_FILE_PUBLIC"] = "Öffentlichen Teil archivieren:";
$MESS["MAIN_DUMP_FILE_TITLE"] = "Dateien";
$MESS["MAIN_DUMP_BASE_STAT"] = "Statistik";
$MESS["MAIN_DUMP_BASE_SINDEX"] = "Suchindex";
$MESS["MAIN_DUMP_BASE_IGNORE"] = "Aus dem Archiv ausschließen:";
$MESS["MAIN_DUMP_BASE_TRUE"] = "Datenbank archivieren:";
$MESS["MAIN_DUMP_BASE_TITLE"] = "Datenbank";
$MESS["MAIN_DUMP_BASE_SIZE"] = "MB";
$MESS["MAIN_DUMP_PAGE_TITLE"] = "Backup";
$MESS["MAIN_DUMP_TAB"] = "Backup";
$MESS["MAIN_DUMP_SITE_PROC"] = "Komprimieren...";
$MESS["MAIN_DUMP_ARC_SIZE"] = "Archivgröße:";
$MESS["MAIN_DUMP_TABLE_FINISH"] = "Tabellen bearbeitet:";
$MESS["MAIN_DUMP_ACTION_DOWNLOAD"] = "Download";
$MESS["MAIN_DUMP_DELETE"] = "Löschen";
$MESS["MAIN_DUMP_ALERT_DELETE"] = "Wollen Sie die Datei wirklich löschen?";
$MESS["MAIN_DUMP_FILE_PAGES"] = "Backup-Kopie";
$MESS["MAIN_RIGHT_CONFIRM_EXECUTE"] = "Achtung! Das Entpacken der Sicherungskopie auf einer funktionierenden Seite kann das Archiv beschädigen! Wollen Sie fortfahren?";
$MESS["MAIN_DUMP_RESTORE"] = "Entpacken";
$MESS["MAIN_DUMP_ENCODE"] = "Achtung! Sie benutzten eine codierte Produktversion";
$MESS["MAIN_DUMP_MYSQL_ONLY"] = "Das Backupsystem arbeitet nur mit MySQL Datenbankdaten.<br>Bitte verwenden Sie externe Tools um eine Datenbankkopie zu erstellen.";
$MESS["MAIN_DUMP_HEADER_MSG"] = "Um das Seitenarchiv zum anderen Host zu übertragen, fügen Sie bitte im Root-Verzeichnis der neuen Seite das Wiederherstellungsskript <a href='/bitrix/admin/restore_export.php'>restore.php</a> und das Archiv selbst ein. Dann geben Sie in der Browser-Zeile &quot;&lt;Seitenname&gt;/restore.php&quot; und folgen Sie den Instruktionen.";
$MESS["MAIN_DUMP_SKIP_SYMLINKS"] = "Symbolische Links zu Verzeichnissen überspringen:";
$MESS["MAIN_DUMP_MASK"] = "Dateien und Verzeichnisse aus dem Archiv ausschließen (nach Maske):";
$MESS["MAIN_DUMP_MORE"] = "Mehr...";
$MESS["MAIN_DUMP_FOOTER_MASK"] = "Für die Ausschlussmaske gelten folgenden Regeln:
<p>
 <li>Die Maskenvorlage kann Symbole &quot;*&quot; beinhalten, die einem beliebigen Anzahl verschiedener Symbole, die im Datei- oder Ordnernamen enthalten sind, entsprechen;</li>
 <li>Wenn am Anfang ein Slash oder ein Backslash steht (&quot;/&quot; oder &quot;\\&quot;), gilt der Pfad vom Root-Verzeichnis aus;</li>
 <li>Im Gegenfall wird die Vorlage auf jede Datei und jeden Ordner angewendet;</li>
 <p>Vorlagenbeispiele:</p>
 <li>/content/photo - Ausschluß des ganzen Ordners /content/photo;</li>
 <li>*.zip - Ausschluß aller Dateien mit der Dateiendung &quot;zip&quot;;</li>
 <li>.access.php - Ausschluß aller Dateien &quot;.access.php&quot;;</li>
 <li>/files/download/*.zip - Ausschluß aller Dateien mit der Dateiendung &quot;zip&quot; im /files/download Ordner;</li>
 <li>/files/d*/*.ht* - Ausschluß der Dateien aus den Ordner, die mit &quot;/files/d&quot anfangen und Dateiendungen, die mit &quot;ht&quot; anfangen.</li>";
$MESS["MAIN_DUMP_ERROR"] = "Fehler";
$MESS["DUMP_NO_PERMS"] = "Keine Berechtigung auf dem Server für Archiverstellung";
$MESS["DUMP_NO_PERMS_READ"] = "Fehler beim Öffnen des Archivs zum Lesen";
$MESS["DUMP_DB_CREATE"] = "Dumperstellung für die Datenbank";
$MESS["DUMP_CUR_PATH"] = "Aktueller Pfad:";
$MESS["INTEGRITY_CHECK"] = "Integritätsprüfung";
$MESS["CURRENT_POS"] = "Aktuelle Position:";
$MESS["TAB_STANDARD"] = "Standard";
$MESS["TAB_STANDARD_DESC"] = "Standardparameter der Durchführung der Datensicherung";
$MESS["TAB_ADVANCED"] = "Erweiterte";
$MESS["TAB_ADVANCED_DESC"] = "Spezialparameter der Durchführung der Datensicherung";
$MESS["MODE_DESC"] = "Es wird ein komplettes Archiv des öffentlichen Teils   <b>der aktuellen Website</b> (für eine Multisitekonfiguration auf verschiedenen Domänen), <b>des Systemkernels</b> und <b>der Datenbank</b> (nur für MySQL) erstellt, welches das System komplett wiederherstellen und auf einen anderen Server übertragen kann. Wählen Sie den gewünschten Modus aus und, wenn erforderlich, stellen Sie zusätzliche Parameter auf der Registerkarte &quot;<b>Erweiterte</b>&quot ein.";
$MESS["MODE_VPS"] = "Extraserver oder VPS (zeitoptimal)";
$MESS["MODE_SHARED"] = "Standardhosting (geeignet für die meisten Websites)";
$MESS["MODE_SLOW"] = "Sicherer Modus (diesen benutzen, wenn andere Modi nicht funktionieren: ohne Komprimierung, mit Pausen zwischen den Schritten)";
$MESS["PUBLIC_PART"] = "Öffentlicher Teil der Website:";
$MESS["SERVER_LIMIT"] = "Servereinschränkungen";
$MESS["STEP_LIMIT"] = "Schrittdauer:";
$MESS["DISABLE_GZIP"] = "Archivkomprimierung ausschalten (reduziert Prozessorbelastung):";
$MESS["INTEGRITY_CHECK_OPTION"] = "Wenn abgeschlossen, die Archivintegrität prüfen:";
$MESS["MAIN_DUMP_DB_PROC"] = "Dump der Datenbank komprimieren";
$MESS["CDIR_FOLDER_ERROR"] = "Fehler beim Bearbeiten des Ordners: ";
$MESS["CDIR_FOLDER_OPEN_ERROR"] = "Fehler beim Öffnen des Ordners: ";
$MESS["CDIR_FILE_ERROR"] = "Fehler beim Bearbeiten der Datei:";
$MESS["BACKUP_NO_PERMS"] = "Keine Berechtigung für einen Eintrag in den Ordner /bitrix/backup";
$MESS["TIME_SPENT"] = "Verwendete Zeit:";
$MESS["TIME_H"] = "St.";
$MESS["TIME_M"] = "Min.";
$MESS["TIME_S"] = "Sek.";
$MESS["MAIN_DUMP_FOLDER_ERR"] = "Der Ordner #FOLDER# ist schreibgeschützt.";
$MESS["MAIN_DUMP_NO_CLOUDS_MODULE"] = "Das Modul der Cloud-Speicher ist nicht installiert";
$MESS["MAIN_DUMP_INT_CLOUD_ERR"] = "Fehler der Initialisierung des Cloud-Speichers. Versuchen Sie bitte später erneut.";
$MESS["MAIN_DUMP_ERR_FILE_SEND"] = "Die Datei konnte in die Cloud nicht verschoben werden: ";
$MESS["MAIN_DUMP_ERR_OPEN_FILE"] = "Die Datei konnte zum Lesen nicht geöffnet werden: ";
$MESS["MAIN_DUMP_SUCCESS_SENT"] = "Das Archiv wurde in den Cloud-Speicher erfolgreich verschoben";
$MESS["MAIN_DUMP_CLOUDS_DOWNLOAD"] = "Dateien aus den Cloud-Speichern herunterladen";
$MESS["MAIN_DUMP_FILES_DOWNLOADED"] = "Dateien heruntergeladen";
$MESS["MAIN_DUMP_FILES_SIZE"] = "Größe der heruntergeladenen Dateien";
$MESS["MAIN_DUMP_DOWN_ERR_CNT"] = "Dateien beim Herunterladen übersprungen";
$MESS["MAIN_DUMP_FILE_SENDING"] = "Archiv in die Cloud verschieben";
$MESS["MAIN_DUMP_USE_THIS_LINK"] = "Nutzen Sie diesen Link für Übertragung auf einen anderen Server mit";
$MESS["MAIN_DUMP_ERR_COPY_FILE"] = "Die Datei konnte nicht kopiert werden: ";
$MESS["MAIN_DUMP_ERR_INIT_CLOUD"] = "Der Cloud-Speicher konnte nicht angebunden werden";
$MESS["MAIN_DUMP_ERR_FILE_RENAME"] = "Fehler beim Umbenennen der Datei: ";
$MESS["MAIN_DUMP_ERR_NAME"] = "Der Archivname kann nur lateinischen Buchstaben, Ziffern, Bindestrich und Punkt enthalten";
$MESS["MAIN_DUMP_FILE_SIZE1"] = "Archivgröße";
$MESS["MAIN_DUMP_LOCATION"] = "Platzierung";
$MESS["MAIN_DUMP_PARTS"] = "Teile: ";
$MESS["MAIN_DUMP_LOCAL"] = "lokal";
$MESS["MAIN_DUMP_GET_LINK"] = "Link für Übertragung anfordern";
$MESS["MAIN_DUMP_SEND_CLOUD"] = "In die Cloud verschieben ";
$MESS["MAIN_DUMP_SEND_FILE_CLOUD"] = "Archiv in den Cloud-Speicher verschieben";
$MESS["MAIN_DUMP_RENAME"] = "Umbenennen";
$MESS["MAIN_DUMP_ARC_NAME_W_O_EXT"] = "Archivname ohne Erweiterung";
$MESS["MAIN_DUMP_CLOUD_STORAGES"] = "Cloud-Speicher";
$MESS["MAIN_DUMP_ARC_LOCATION"] = "Archiv-Platzierung: ";
$MESS["MAIN_DUMP_LOCAL_DISK"] = "auf der Festplatte";
$MESS["MAIN_DUMP_ARC_FROM_CLOUD"] = "Dateien ins Archiv verschieben aus den Cloud-Speichern:";
$MESS["MAIN_DUMP_EVENT_LOG"] = "Event-Protokoll";
$MESS["MAIN_DUMP_ENC_ERR_CYR"] = "ru only";
$MESS["MAIN_DUMP_CYR_LETTERS"] = "ru only";
$MESS["MAIN_DUMP_INTEGRITY_CHECK"] = "Überprüfung der Archivintegrität";
$MESS["MAIN_DUMP_ENC_PASS_DESC"] = "Das Archivpasswort muss mindestens 6 Zeichen enthalten.";
$MESS["MAIN_DUMP_EMPTY_PASS"] = "Archivpasswort wurde nicht angegeben.";
$MESS["MAIN_DUMP_SECURITY"] = "Sicherheit";
$MESS["MAIN_DUMP_NOT_INSTALLED"] = "Das PHP-Modul Mcrypt ist nicht installiert.";
$MESS["MAIN_DUMP_NO_ENC_FUNCTIONS"] = "Verschlüsselung ist nicht möglich. Wenden Sie sich bitte an Ihren Systemadministrator.";
$MESS["MAIN_DUMP_ENABLE_ENC"] = "Archiv der Datensicherung verschlüsseln";
$MESS["MAIN_DUMP_ENC_PASS"] = "Archivpasswort (mindestens 6 Zeichen):";
$MESS["MAIN_DUMP_SAVE_PASS"] = "Stellen Sie bitte sicher, dass Ihr Passwort in einem sicheren Ort aufbewahrt wird. Sie werden die Dateien aus dem Archiv nicht entpacken können, wenn Ihr Passwort verloren geht.";
$MESS["MAIN_DUMP_MAX_ARCHIVE_SIZE"] = "Maximale Größe einer Archiv-Einheit (MB):";
?>