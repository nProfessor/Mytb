<?
$MESS["MAIN_DUMP_FILE_CNT"] = "Файлов сжато:";
$MESS["MAIN_DUMP_FILE_SIZE"] = "Размер файлов:";
$MESS["MAIN_DUMP_FILE_FINISH"] = "Создание резервной копии завершено";
$MESS["MAIN_DUMP_FILE_MAX_SIZE"] = "Исключить из архива файлы размером более (0 - без ограничения):";
$MESS["MAIN_DUMP_FILE_STEP_SLEEP"] = "интервал:";
$MESS["MAIN_DUMP_FILE_STEP_sec"] = "сек.";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_b"] = "б ";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_kb"] = "кб ";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_mb"] = "Мб ";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_gb"] = "Гб ";
$MESS["MAIN_DUMP_FILE_DUMP_BUTTON"] = "Создать резервную копию";
$MESS["MAIN_DUMP_FILE_STOP_BUTTON"] = "Остановить";
$MESS["MAIN_DUMP_FILE_KERNEL"] = "Архивировать ядро:";
$MESS["MAIN_DUMP_FILE_NAME"] = "Имя";
$MESS["FILE_SIZE"] = "Размер файла";
$MESS["MAIN_DUMP_FILE_TIMESTAMP"] = "Изменен";
$MESS["MAIN_DUMP_FILE_PUBLIC"] = "Архивировать публичную часть:";
$MESS["MAIN_DUMP_BASE_STAT"] = "статистику";
$MESS["MAIN_DUMP_BASE_SINDEX"] = "поисковый индекс";
$MESS["MAIN_DUMP_BASE_SIZE"] = "МБ";
$MESS["MAIN_DUMP_PAGE_TITLE"] = "Резервное копирование";
$MESS["MAIN_DUMP_SITE_PROC"] = "Сжатие...";
$MESS["MAIN_DUMP_ARC_SIZE"] = "Размер архива:";
$MESS["MAIN_DUMP_TABLE_FINISH"] = "Обработано таблиц:";
$MESS["MAIN_DUMP_ACTION_DOWNLOAD"] = "Скачать";
$MESS["MAIN_DUMP_DELETE"] = "Удалить";
$MESS["MAIN_DUMP_ALERT_DELETE"] = "Вы уверены, что хотите удалить файл?";
$MESS["MAIN_DUMP_FILE_PAGES"] = "Резервные копии";
$MESS["MAIN_RIGHT_CONFIRM_EXECUTE"] = "Внимание! Восстановление резервной копии на действующем сайте может привести к повреждению сайта! Продолжить?";
$MESS["MAIN_DUMP_RESTORE"] = "Восстановить";
$MESS["MAIN_DUMP_MYSQL_ONLY"] = "Система резервного копирования работает только с базой данных MySQL. Пожалуйста, используйте внешние инструменты для создания резервной копии базы данных.";
$MESS["MAIN_DUMP_HEADER_MSG"] = "Для переноса резервной копии сайта на другой хостинг поместите в корневой папке нового сайта скрипт для восстановления <a href='/bitrix/admin/restore_export.php'>restore.php</a>, затем наберите в строке браузера &quot;&lt;имя сайта&gt;/restore.php&quot; и следуйте инструкциям по распаковке.<br>Подробная инструкция доступна в <a href='http://dev.1c-bitrix.ru/api_help/main/going_remote.php' target=_blank>разделе справки</a>.";
$MESS["MAIN_DUMP_SKIP_SYMLINKS"] = "Пропускать символические ссылки на директории:";
$MESS["MAIN_DUMP_MASK"] = "Исключить из архива файлы и директории по маске:";
$MESS["MAIN_DUMP_MORE"] = "Ещё...";
$MESS["MAIN_DUMP_FOOTER_MASK"] = "Для маски исключения действуют следующие правила:
	<p>
	<li>шаблон маски может содержать символы &quot;*&quot;, которые соответствуют любому количеству любых символов в имени файла или папки;</li>
	<li>если в начале стоит косая черта (&quot;/&quot; или &quot;\\&quot;), путь считается от корня сайта;</li>
	<li>в противном случае шаблон применяется к каждому файлу или папке;</li>
	<p>Примеры шаблонов:</p>
	<li>/content/photo - исключить целиком папку /content/photo;</li>
	<li>*.zip - исключить файлы с расширением &quot;zip&quot;;</li>
	<li>.access.php - исключить все файлы &quot;.access.php&quot;;</li>
	<li>/files/download/*.zip - исключить файлы с расширением &quot;zip&quot; в директории /files/download;</li>
	<li>/files/d*/*.ht* - исключить файлы из директорий, начинающихся на &quot;/files/d&quot;  с расширениями, начинающимися на &quot;ht&quot;.</li>
	";
$MESS["MAIN_DUMP_ERROR"] = "Ошибка";
$MESS["ERR_EMPTY_RESPONSE"] = "Произошла ошибка на стороне сервера: получен пустой ответ. Обратитесь к хостеру для уточнения проблемы в журнале ошибок по дате запроса: #DATE#";
$MESS["DUMP_NO_PERMS"] = "Нет прав на сервере на создание резервной копии";
$MESS["DUMP_NO_PERMS_READ"] = "Ошибка открытия архива на чтение";
$MESS["DUMP_DB_CREATE"] = "Создание дампа базы данных";
$MESS["DUMP_CUR_PATH"] = "Текущий путь:";
$MESS["INTEGRITY_CHECK"] = "Проверка целостности";
$MESS["CURRENT_POS"] = "Текущая позиция:";
$MESS["STEP_LIMIT"] = "Длительность шага:";
$MESS["DISABLE_GZIP"] = "Отключить компрессию архива (снижение нагрузки на процессор):";
$MESS["INTEGRITY_CHECK_OPTION"] = "Проверить целостность архива после завершения:";
$MESS["MAIN_DUMP_DB_PROC"] = "Сжатие дампа базы данных";
$MESS["TIME_SPENT"] = "Затрачено времени:";
$MESS["TIME_H"] = "час.";
$MESS["TIME_M"] = "мин.";
$MESS["TIME_S"] = "сек.";
$MESS["MAIN_DUMP_FOLDER_ERR"] = "Папка #FOLDER# недоступна на запись";
$MESS["MAIN_DUMP_NO_CLOUDS_MODULE"] = "Модуль облачных хранилищ не установлен";
$MESS["MAIN_DUMP_INT_CLOUD_ERR"] = "Ошибка инициализации облачного хранилища. Попробуйте повторить отправку позднее.";
$MESS["MAIN_DUMP_ERR_FILE_SEND"] = "Не удалось отправить файл в облако: ";
$MESS["MAIN_DUMP_ERR_OPEN_FILE"] = "Не удалось открыть файл на чтение: ";
$MESS["MAIN_DUMP_SUCCESS_SENT"] = "Резервная копия успешно передана в облачное хранилище";
$MESS["MAIN_DUMP_CLOUDS_DOWNLOAD"] = "Загрузка файлов из облачных хранилищ";
$MESS["MAIN_DUMP_FILES_DOWNLOADED"] = "Загружено файлов";
$MESS["MAIN_DUMP_FILES_SIZE"] = "Размер загруженных файлов";
$MESS["MAIN_DUMP_DOWN_ERR_CNT"] = "Пропущено при загрузке";
$MESS["MAIN_DUMP_FILE_SENDING"] = "Передача резервной копии в облако";
$MESS["MAIN_DUMP_USE_THIS_LINK"] = "Используйте эту ссылку для переноса на другой сервер через";
$MESS["MAIN_DUMP_ERR_COPY_FILE"] = "Не удалось скопировать файл: ";
$MESS["MAIN_DUMP_ERR_INIT_CLOUD"] = "Не удалось подключить облачное хранилище";
$MESS["MAIN_DUMP_ERR_FILE_RENAME"] = "Ошибка переименования файла: ";
$MESS["MAIN_DUMP_ERR_NAME"] = "Имя файла может содержать только латинские буквы, цифры, дефис и точку";
$MESS["MAIN_DUMP_FILE_SIZE1"] = "Размер архива";
$MESS["MAIN_DUMP_LOCATION"] = "Размещение";
$MESS["MAIN_DUMP_PARTS"] = "частей: ";
$MESS["MAIN_DUMP_LOCAL"] = "локально";
$MESS["MAIN_DUMP_GET_LINK"] = "Получить ссылку для переноса";
$MESS["MAIN_DUMP_SEND_CLOUD"] = "Отправить в облако ";
$MESS["MAIN_DUMP_SEND_FILE_CLOUD"] = "Отправить резервную копию в облачное хранилище";
$MESS["MAIN_DUMP_RENAME"] = "Переименовать";
$MESS["MAIN_DUMP_ARC_NAME_W_O_EXT"] = "Имя файла без расширения";
$MESS["MAIN_DUMP_ARC_NAME"] = "Имя архива";
$MESS["MAIN_DUMP_ARC_LOCATION"] = "Размещение резервной копии: ";
$MESS["MAIN_DUMP_LOCAL_DISK"] = "в папке сайта";
$MESS["MAIN_DUMP_EVENT_LOG"] = "журнал событий";
$MESS["MAIN_DUMP_ENC_PASS_DESC"] = "С целью безопасности пароль для шифрования архива должен быть не менее 6 символов";
$MESS["MAIN_DUMP_EMPTY_PASS"] = "Не задан пароль для шифрования архива";
$MESS["MAIN_DUMP_NOT_INSTALLED"] = "Не установлен PHP модуль Mcrypt";
$MESS["MAIN_DUMP_NO_ENC_FUNCTIONS"] = "Функции шифрования недоступны, использование облачного хранилища 1С-Битрикс невозможно. Обратитесь к системному администратору для решения проблемы";
$MESS["MAIN_DUMP_ENABLE_ENC"] = "Шифровать данные резервной копии:";
$MESS["MAIN_DUMP_ENC_PASS"] = "Пароль для шифрования архива (не менее 6 символов):";
$MESS["MAIN_DUMP_SAVE_PASS"] = "Внимание! Пароль нигде не сохраняется. Запишите его в надежном месте, без знания этого пароля восстановить резервную копию не удастся.";
$MESS["MAIN_DUMP_MAX_ARCHIVE_SIZE"] = "Максимальный размер одной части архива (МБ):";
$MESS["DUMP_MAIN_SESISON_ERROR"] = "Ваша сессия истекла. Перезагрузите страницу.";
$MESS["DUMP_MAIN_ERROR"] = "Ошибка! ";
$MESS["DUMP_MAIN_REGISTERED"] = "Зарегистрировано";
$MESS["DUMP_MAIN_EDITION"] = "Редакция";
$MESS["DUMP_MAIN_ACTIVE_FROM"] = "Начало активности";
$MESS["DUMP_MAIN_ACTIVE_TO"] = "Окончание активности";
$MESS["DUMP_MAIN_ERR_GET_INFO"] = "Не удалось получить информацию о ключе с сервера обновлений";
$MESS["DUMP_MAIN_BITRIX_CLOUD"] = "облако 1С-Битрикс";
$MESS["DUMP_MAIN_BITRIX_CLOUD_DESC"] = "Облачное хранилище &quot;1С-Битрикс&quot;";
$MESS["DUMP_MAIN_ERR_PASS_CONFIRM"] = "Введённые пароли не совпадают";
$MESS["DUMP_MAIN_PASSWORD_CONFIRM"] = "Повтор пароля:";
$MESS["DUMP_MAIN_MAKE_ARC"] = "Резервное копирование";
$MESS["MAKE_DUMP_FULL"] = "Создание полной резервной копии";
$MESS["DUMP_MAIN_PARAMETERS"] = "Параметры";
$MESS["DUMP_MAIN_EXPERT_SETTINGS"] = "Экспертные настройки";
$MESS["DUMP_MAIN_ENC_ARC"] = "Шифрование архива";
$MESS["DUMP_MAIN_SITE"] = "Сайт:";
$MESS["DUMP_MAIN_IN_THE_CLOUD"] = "в облаке";
$MESS["DUMP_MAIN_IN_THE_BXCLOUD"] = "в облаке &quot;1С-Битрикс&quot;";
$MESS["DUMP_MAIN_ENABLE_EXPERT"] = "Включить экспертные настройки резервной копии";
$MESS["DUMP_MAIN_CHANGE_SETTINGS"] = "Изменение экспертных настроек может привести к созданию нецелостного архива и невозможности его восстановления. Вы должны хорошо понимать, что делаете.";
$MESS["DUMP_MAIN_ARC_CONTENTS"] = "Содержимое резервной копии";
$MESS["DUMP_MAIN_DOWNLOAD_CLOUDS"] = "Скачать и поместить в архив данные облачных хранилищ:";
$MESS["DUMP_MAIN_ARC_DATABASE"] = "Архивировать базу данных";
$MESS["DUMP_MAIN_DB_EXCLUDE"] = "Исключить из базы данных:";
$MESS["DUMP_MAIN_ARC_MODE"] = "Режим архивации";
$MESS["DUMP_MAIN_MULTISITE"] = "Если <a href=/bitrix/admin/site_admin.php target=_blank>настроено</a> несколько сайтов с разными путями к корневой папке веб-сервера, они архивируются и восстанавливаются отдельно. При этом полный архив делается один раз. А затем, при архивации других сайтов, вам надо будет исключить ядро и базу данных через <b>экспертные</b> настройки. Если восстановление делается на другом сервере, символьные ссылки на папки <b>bitrix</b> и <b>upload</b> вам необходимо будет создать вручную.";
$MESS["BCL_BACKUP_USAGE"] = "Использовано места: #USAGE# из #QUOTA#.";
$MESS["DUMP_BXCLOUD_NA"] = "Облачное хранилище &quot;1С-Битрикс&quot; недоступно";
$MESS["DUMP_ERR_NON_ASCII"] = "Во избежание проблем с восстановлением резервной копии в пароле не допускаются символы национального алфавита";
$MESS["DUMP_MAIN_BXCLOUD_INFO"] = "Компания &quot;1С-Битрикс&quot; бесплатно предоставляет место в облаке для хранения трех резервных копий на каждую активную лицензию. Доступ к резервным копиям осуществляется по лицензионному ключу и паролю. Без знания пароля никто, включая сотрудников &quot;1С-Битрикс&quot;, не сможет получить доступ к вашим данным.";
$MESS["MAIN_DUMP_BXCLOUD_ENC"] = "При размещении резервной копии в облачном хранилище &quot;1С-Битрикс&quot; отключить шифрование нельзя.";
?>