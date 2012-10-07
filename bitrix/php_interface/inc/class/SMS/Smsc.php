<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 22.09.12
 * Time: 11:54
 * To change this template use File | Settings | File Templates.
 */
class Smsc
{
    private $SMSC_LOGIN = "sforge"; // логин клиента
    private $SMSC_PASSWORD = "89265529608"; // пароль или MD5-хеш пароля в нижнем регистре
    private $SMSC_POST = TRUE; // использовать метод POST
    private $SMSC_HTTPS; // использовать HTTPS протокол
    private $SMSC_CHARSET = "utf-8"; // кодировка сообщения: utf-8, koi8-r или windows-1251 (по умолчанию)
    private $SMSC_DEBUG; // флаг отладки
    private $SMTP_FROM; // e-mail адрес отправителя

    function __construct()
    {

    }


    /**
     * Функция отправки SMS
     *
     *
     * @param        $phones  - список телефонов через запятую или точку с запятой
     * @param        $message - отправляемое сообщение
     *
     * @param int    $translit- переводить или нет в транслит (1,2 или 0)
     * @param int    $time    - необходимое время доставки в виде строки (DDMMYYhhmm, h1-h2, 0ts, +m)
     * @param int    $id      - идентификатор сообщения. Представляет собой 32-битное число в диапазоне от 1 до 2147483647.
     * @param int    $format  - формат сообщения (0 - обычное sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin, 5 - bin-hex, 6 - ping-sms)
     * @param bool   $sender  - имя отправителя (Sender ID). Для отключения Sender ID по умолчанию необходимо в качестве имени передать пустую строку или точку.
     * @param string $query   - строка дополнительных параметров, добавляемая в URL-запрос ("valid=01:00&maxsms=3&tz=2")
     *
     * @return array
     */

    function send_sms($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = FALSE, $query = "")
    {
        static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1");

        $m = $this->_smsc_send_cmd("send", "cost=3&phones=" . urlencode($phones) . "&mes=" . urlencode($message) .
                                    "&translit=$translit&id=$id" . ($format > 0
            ? "&" . $formats[$format]
            : "") .
                                    ($sender === FALSE
                                        ? ""
                                        : "&sender=" . urlencode($sender)) . "&charset=" . $this->SMSC_CHARSET .
                                    ($time
                                        ? "&time=" . urlencode($time)
                                        : "") . ($query
            ? "&$query"
            : ""));

        // (id, cnt, cost, balance) или (id, -error)

        if ($this->SMSC_DEBUG) {
            if ($m[1] > 0)
                echo "Сообщение отправлено успешно. ID: $m[0], всего SMS: $m[1], стоимость: $m[2] руб., баланс: $m[3] руб.\n";
            else
                echo "Ошибка №", -$m[1], $m[0]
                    ? ", ID: " . $m[0]
                    : "", "\n";
        }

        return $m;
    }


    /**
     * SMTP версия функции отправки SMS
     *
     * @param        $phones
     * @param        $message
     * @param int    $translit
     * @param int    $time
     * @param int    $id
     * @param int    $format
     * @param string $sender
     *
     * @return bool
     */
    function send_sms_mail($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = "")
    {
        return mail("send@send.smsc.ru", "",
            $this->SMSC_LOGIN . ":" . $this->SMSC_PASSWORD . ":$id:$time:$translit,$format,$sender:$phones:$message",
            "From: " . $this->SMTP_FROM . "\nContent-Type: text/plain; charset=" . $this->SMSC_CHARSET . "\n");
    }


    /**
     * Функция получения стоимости SMS
     *
     * @param        $phones   - список телефонов через запятую или точку с запятой
     * @param        $message  - отправляемое сообщение
     * @param int    $translit - переводить или нет в транслит (1,2 или 0)
     * @param int    $format   - формат сообщения (0 - обычное sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin, 5 - bin-hex, 6 - ping-sms)
     * @param bool   $sender   - имя отправителя (Sender ID)
     * @param string $query    - строка дополнительных параметров, добавляемая в URL-запрос ("list=79999999999:Ваш пароль: 123\n78888888888:Ваш пароль: 456")
     *
     * @return array возвращает массив (<стоимость>, <количество sms>) либо массив (0, -<код ошибки>) в случае ошибки
     */
    function get_sms_cost($phones, $message, $translit = 0, $format = 0, $sender = FALSE, $query = "")
    {
        static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1");

        $m = $this->_smsc_send_cmd("send", "cost=1&phones=" . urlencode($phones) . "&mes=" . urlencode($message) .
                                    ($sender === FALSE
                                        ? ""
                                        : "&sender=" . urlencode($sender)) . "&charset=" . $this->SMSC_CHARSET .
                                    "&translit=$translit" . ($format > 0
            ? "&" . $formats[$format]
            : "") . ($query
            ? "&$query"
            : ""));

        // (cost, cnt) или (0, -error)
        if ($this->SMSC_DEBUG) {
            if ($m[1] > 0)
                echo "Стоимость рассылки: $m[0] руб. Всего SMS: $m[1]\n";
            else
                echo "Ошибка №", -$m[1], "\n";
        }

        return $m;
    }


    /**
     * Функция проверки статуса отправленного SMS или HLR-запроса
     *
     * @param     $id   - ID cообщения
     * @param     $phone- номер телефона
     * @param int $all  - вернуть все данные отправленного SMS, включая текст сообщения (0 или 1)
     *
     * @return array
     * возвращает массив:
     * для SMS-сообщения:
     * (<статус>, <время изменения>, <код ошибки доставки>)
     *
     * для HLR-запроса:
     * (<статус>, <время изменения>, <код ошибки sms>, <код IMSI SIM-карты>, <номер сервис-центра>, <код страны регистрации>, <код оператора>,
     * <название страны регистрации>, <название оператора>, <название роуминговой страны>, <название роумингового оператора>)
     *
     * При $all = 1 дополнительно возвращаются элементы в конце массива:
     * (<время отправки>, <номер телефона>, <стоимость>, <sender id>, <название статуса>, <текст сообщения>)
     *
     *  либо массив (0, -<код ошибки>) в случае ошибки
     */
    function get_status($id, $phone, $all = 0)
    {
        $m = $this->_smsc_send_cmd("status", "phone=" . urlencode($phone) . "&id=" . $id . "&all=" . (int)$all);

        // (status, time, err, ...) или (0, -error)

        if ($this->SMSC_DEBUG) {
            if ($m[1] != "" && $m[1] >= 0)
                echo "Статус SMS = $m[0]", $m[1]
                    ? ", время изменения статуса - " . date("d.m.Y H:i:s", $m[1])
                    : "", "\n";
            else
                echo "Ошибка №", -$m[1], "\n";
        }

        if ($all && count($m) > 9 && (!isset($m[14]) || $m[14] != "HLR")) // ',' в сообщении
            $m = explode(",", implode(",", $m), 9);

        return $m;
    }


    /**
     * Функция получения баланса
     * @return bool возвращает баланс в виде строки или false в случае ошибки
     */
    function get_balance()
    {
        $m = $this->_smsc_send_cmd("balance"); // (balance) или (0, -error)

        if ($this->SMSC_DEBUG) {
            if (!isset($m[1]))
                echo "Сумма на счете: ", $m[0], " руб.\n";
            else
                echo "Ошибка №", -$m[1], "\n";
        }

        return isset($m[1])
            ? FALSE
            : $m[0];
    }


    /**
     * Функция вызова запроса. Формирует URL и делает 3 попытки чтения
     *
     * @param        $cmd
     * @param string $arg
     *
     * @return array
     */
    function _smsc_send_cmd($cmd, $arg = "")
    {
        $url = ($this->SMSC_HTTPS
            ? "https"
            : "http") . "://smsc.ru/sys/$cmd.php?login=" . urlencode($this->SMSC_LOGIN) . "&psw=" .
               urlencode($this->SMSC_PASSWORD) . "&fmt=1&" . $arg;

        $i = 0;
        do {
            if ($i)
                sleep(2);

            $ret = $this->_smsc_read_url($url);
        } while ($ret == "" && ++$i < 3);

        if ($ret == "") {
            if ($this->SMSC_DEBUG)
                echo "Ошибка чтения адреса: $url\n";

            $ret = ","; // фиктивный ответ
        }

        return explode(",", $ret);
    }


    /**
     * Функция чтения URL. Для работы должно быть доступно:
     *  curl или fsockopen (только http) или включена опция allow_url_fopen для file_get_contents
     *
     * @param $url
     *
     * @return bool|mixed|string
     */
    function _smsc_read_url($url)
    {
        $ret  = "";
        $post = $this->SMSC_POST || strlen($url) > 2000;

        if (function_exists("curl_init")) {
            static $c = 0; // keepalive

            if (!$c) {
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($c, CURLOPT_TIMEOUT, 10);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            }

            if ($post) {
                list($url, $post) = explode('?', $url, 2);
                curl_setopt($c, CURLOPT_POST, TRUE);
                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
            }

            curl_setopt($c, CURLOPT_URL, $url);

            $ret = curl_exec($c);
        } elseif (!$this->SMSC_HTTPS && function_exists("fsockopen")) {
            $m = parse_url($url);

            $fp = fsockopen($m["host"], 80, $errno, $errstr, 10);

            if ($fp) {
                fwrite($fp, ($post
                    ? "POST $m[path]"
                    : "GET $m[path]?$m[query]") . " HTTP/1.1\r\nHost: smsc.ru\r\nUser-Agent: PHP" . ($post
                    ? "\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($m['query'])
                    : "") . "\r\nConnection: Close\r\n\r\n" . ($post
                    ? $m['query']
                    : ""));

                while (!feof($fp))
                    $ret .= fgets($fp, 1024);
                list(, $ret) = explode("\r\n\r\n", $ret, 2);

                fclose($fp);
            }
        } else
            $ret = file_get_contents($url);

        return $ret;
    }

}