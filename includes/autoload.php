<?php
define('DS', DIRECTORY_SEPARATOR);
require_once ROOT_PATH . DS.'includes'.DS.'vendor'.DS.'autoload.php';
use Symfony\Component\ErrorHandler\Debug;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
require_once ROOT_PATH . DS.  'includes'.DS.'functions.inc.php';

function classLoader($className){

    $appPath =ROOT_PATH . DS .'includes'. DS ;
    $file = '';

    try{

        if(preg_match('/\\\\/', $className)){
            $check = substr($className, 0, 1);
            if($check === '\\')
                $className = substr($className, 1);
            $file = $appPath . str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
            require_once $file;

        }
        else{
            if(DEBUG_NEW)
                dump($className);
            $logger = new Logger('autoload-error');
            $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/autoloader.log', Logger::ERROR));
            $logger->error(date('y-m-d H:i:s').' Class: '.$file.' server info: '. json_encode([
                    'QUERY_STRING'  => $_SERVER['QUERY_STRING'],
                    'SCRIPT_URL'    => $_REQUEST['SCRIPT_URL'],
                ]));
        }
    }
    catch(\Exception $e){
        dump($className);
        dump($e->getMessage());
        dump($file);
        dump($className);
    }

}
spl_autoload_register('classLoader');

