<?php
namespace Classes;

use \mysqli as mysqli;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Connection{
    private $host;
    private $user;
    private $password;
    private $name;

    /**
     *
     * @var mysqli
     */
    public static $link;

    private static $instances = [];

    private function __construct($host, $user, $password, $name) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->name = $name;
        self::$link = false;
    }


    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Fuck off!");
    }

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            if(!defined('DB_HOST'))
                throw new \Exception("Connection error!");
            self::$instances[$subclass] = new static(base64_decode(DB_HOST), base64_decode(DB_USER), base64_decode(DB_PASS), base64_decode(DB_NAME));
        }
        return self::$instances[$subclass];
    }

    function getHost() {
        return $this->host;
    }

    function setHost($host) {
        $this->host = $host;
    }

    function getUser() {
        return $this->login;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function getPassword() {
        return $this->password;
    }

    function setPass($password) {
        $this->password = $password;
    }

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getLink():mysqli {
        return self::$link;
    }

    function connect() {
        if (empty($this->host) || empty($this->user) || empty($this->name)) {
            die();
        }
        try{
            self::$link = new mysqli($this->host, $this->user, $this->password);
        }catch(\Exception $e){
            $error = 'Connection error, please contact to administrator';
            $logger = new Logger('db_connection ');
            $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/db-connection.log', Logger::ERROR));
            $logger->error(date('y-m-d H:i:s').' Wystąpił błąd podczas łączenia się z serwerem baz danych!  Błąd nr: '. $e->getMessage().' Błąd: '.mysqli_connect_error()  );
            return false;

        }

        if (!self::$link->select_db($this->name)) {
            $error = 'Connection error, please contact to administrator';
            $logger = new Logger('db_connection ');
            $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/db-connection.log', Logger::ERROR));
            $logger->error(date('y-m-d H:i:s').' Wystąpił błąd podczas wybierania bazy danych!!  Błąd nr: '. $this->link->errno.' Błąd: '.$this->link->error  );
            return false;
        }

        $this->setCharset('utf8');
        return true;
    }

    function close() {
        self::$link->close();
    }

    function setCharset($charset) {
        self::$link->query("SET NAMES '" . $charset . "'");
    }

    function getCharset() {
        return mysqli_character_set_name($this->connectId);
    }



    function real_escape_string($string) {
        return self::$link->real_escape_string($string);
    }

    /**
     * Rozpoczyna transakcję. Zapis nastąpi dopiero w momencie wywołania COMMIT.
     */
    public function startTransaction()
    {
        self::$link->autocommit(false);
    }

    /**
     * Zapis transakcji.
     */
    public function commitTransaction()
    {
        self::$link->commit();
        self::$link->autocommit(true);
    }

    /**
     * Cofnięcie transakcji. Usuwa z bazy wpisy dodane w trakcje transakcji.
     */
    public function rollbackTransaction()
    {
        self::$link->rollback();
        self::$link->autocommit(true);
    }
}
