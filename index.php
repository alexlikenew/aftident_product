<?php

use Controllers\RootController;
use Controllers\DictionaryController;
use Controllers\DictionaryAdminController;
use Controllers\ModuleController;

$page_start = microtime();

setlocale(LC_ALL, 'en_US.utf-8');
setlocale(LC_TIME, 'pl_PL', 'pl_PL.utf-8', 'pol_pol', 'polish', 'plk_plk', 'poland');
ini_set('url_rewriter.tags', '');
ini_set('arg_separator.output', '&amp;'); // separator zmiennych w adresie (zgodnosc z xhtml 1.0)
ini_set('memory_limit', '64M');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.bug_compat_42', 0);
ini_set('session.bug_compat_warn', 0);
ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE ^ E_STRICT);
session_start();
header("Cache-control: private"); //IE 6 Fix
header("Content-type: text/html; charset=utf-8");
$log_file = __DIR__ . DIRECTORY_SEPARATOR .'logs' . DIRECTORY_SEPARATOR . 'server_errors.log';
ini_set("log_errors", TRUE);
ini_set('error_log', $log_file);


require_once 'config.inc.php';
require_once ROOT_PATH . '/includes/autoload.php';

//if(defined('DEBUG_NEW') && DEBUG_NEW)
    ini_set("display_errors", 1);

$rootController = new RootController();
$moduleController = new ModuleController();

$rootController->setDefaultVariables();
$rootController->prepareParams();
$configController = $rootController->loadConfigController();

$CONF = $configController->load();

//przekierowanie na www
if ($CONF['lang_selection_method'] != 'subdomain') {
    if ($_SERVER['HTTP_HOST'] != 'localhost') {

        if (!preg_match('/www\..*?/', $_SERVER['HTTP_HOST'])) {
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL = "https://";
            } else {
                $pageURL = "http://";
            }
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= "www." . $_SERVER['HTTP_HOST'] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= "www." . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }
            redirect301($pageURL);
        }
    }
}

if ($CONF['lang_selection_method'] != 'old' && $_SERVER['HTTP_HOST'] == 'localhost') {
    echo "Wybór języka poprzez subdomenę lub domenę nie działa gdy adres URL zaczyna się od localhost";
    die();
}

$CONF['base_url'] = BASE_URL;
define('ADMIN_EMAIL', $configController->getOptionExtra('admin_email')['admin_email']);
define('BIURO_EMAIL', $configController->getOptionExtra('biuro_email')['biuro_email']);
define('FIRM_NAME', $CONF['firm_name']);

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
define('CLIENT_IP', $ip);

// ladujemy dostepne jezyki
$languages = $configController->getLang();

$LANG_ID = [];
$LANG_CODE = [];

$normalLang = [];

foreach ($languages as $lang) {
    $LANG_ID[] = $lang['id'];
    $LANG_CODE[] = $lang['code'];
    $normalLang[$lang['id']] = $lang['code'];
}

$LANG_MAIN= $configController->getLangMain();

define('lang_main', $LANG_MAIN['id']);

$rootController->assign('languages', $languages);
$rootController->assign('LANG_MAIN', $LANG_MAIN);
define('LANG_MAIN', $LANG_MAIN['id']);

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define('URL_PREFIX', '');
} else {
    define('URL_PREFIX', 'https://' . $LANG_MAIN['domain']);
}
if (count($languages) > 1) {
    define('lang_multi', true);

} else {
    define('lang_multi', false);
}

$lang = '';
$languagesArr = [];

switch ($CONF['lang_selection_method']) {
    case 'old':
        if ($rootController->get->has('lang')) {
            $lang = $rootController->get->get('lang');
        } else {
            if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
                if (in_array($_SESSION['_id'], $LANG_ID)) {
                    $lang = $_SESSION['lang'];
                } else {
                    $lang = $LANG_MAIN['code'];
                }
            } elseif (isset($_COOKIE['lang']) && !empty($_COOKIE['lang'])) {
                if (in_array($_COOKIE['_id'], $LANG_ID)) {
                    $lang = $_COOKIE['lang'];
                } else {
                    $lang = $LANG_MAIN['code'];
                }
            } else {
                $lang = $LANG_MAIN['code'];
            }
        }
        foreach ($languages as $key => $a) {
            $languagesArr[$key]['name'] = $a['name'];
            $languagesArr[$key]['directory'] = $a['directory'];
            $languagesArr[$key]['url'] = '?lang=' . $a['code'];
            $languagesArr[$key]['code'] = $a['code'];
        }
        break;

    case 'subdomain':

        $temp = explode('.', $_SERVER['HTTP_HOST']);

        if (in_array($temp[0], $LANG_CODE)) {
            $lang = $temp[0];
        } else {
            $lang = $LANG_MAIN['code'];
        }
        foreach ($languages as $key => $a) {
            $languagesArr[$key]['name'] = $a['name'];
            $languagesArr[$key]['directory'] = $a['directory'];
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $url = "https://";
            } else {
                $url = "http://";
            }
            if ($a['code'] == $LANG_MAIN['code']) {
                $url .= 'www.' . $temp[1] . '.' . $temp[2];
            } else {
                $url .= $a['code'] . '.' . $temp[1] . '.' . $temp[2];
            }
            if ($_SERVER["SERVER_PORT"] != "80") {
                $url .= ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $url .= $_SERVER['REQUEST_URI'];
            }
            $languagesArr[$key]['url'] = $url;
            $languagesArr[$key]['code'] = $a['code'];
        }
        break;

    case 'domain':

        foreach ($languages as $key => $a) {
            if ($a['domain'] == $_SERVER['HTTP_HOST']) {
                $lang = $a['code'];
            }
        }

        if (empty($lang)) {
            $lang = $LANG_MAIN['code'];
        }
        foreach ($languages as $key => $a) {
            $languagesArr[$key]['name'] = $a['name'];
            $languagesArr[$key]['directory'] = $a['directory'];
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $url = "https://" . $a['domain'];
            } else {
                $url = "http://" . $a['domain'];
            }
            if ($_SERVER["SERVER_PORT"] != "80") {
                $url .= ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $url .= $_SERVER['REQUEST_URI'];
            }
            $languagesArr[$key]['url'] = $url;
            $languagesArr[$key]['code'] = $a['code'];
        }
        break;

    default:
        break;
}


$oSlownik = new DictionaryController('slownik', $CONF['limit_admin']);

$_id = $configController->getLangActive($lang);

setcookie('lang', $lang, time() + 24 * 3600); // aktywna wersja jezykowa na 1 dzien
setcookie('_id', $_id, time() + 24 * 3600);
$_SESSION['lang'] = $lang; // aktywna wersja jezykowa w danej sesji
$_SESSION['_id'] = $_id;
$translations = $oSlownik->load($_id, $GLOBALS); // ladujemy zawartosc slownika z bazy
foreach($translations as $key=>$val){
    $GLOBALS[$key] = $val;
}
define('_ID', $_id);
define('LANG_DIR', $lang);

define('BASE_URL', $languagesArr[$_id -1]['url']);
$currentLang = [
    'id'    => $_id,
    'code'  => $lang
];

$rootController->assign('current_lang', $currentLang);

// zapobiega powielaniu adresów dla seo w polskim
if ($rootController->get->has('lang') && $rootController->get('lang') == $LANG_MAIN['code']) {
    $host = $_SERVER['HTTP_HOST'];
    $self = $_SERVER['PHP_SELF'];
    redirect301("http://" . $host . $self);
}

$SEOCONF = [];
$SEOCONF['page_title'] = $configController->getOptionLang('page_title');
$SEOCONF['page_title_prefix'] = $configController->getOptionLang('page_title_prefix');
$SEOCONF['page_title_suffix'] = $configController->getOptionLang('page_title_suffix');
$SEOCONF['page_keywords'] = $configController->getOptionLang('page_keywords');
$SEOCONF['page_description'] = $configController->getOptionLang('page_description');

define('TITLE', $SEOCONF['page_title']);
define('TITLE_PREFIX', $SEOCONF['page_title_prefix']);
define('TITLE_SUFFIX', $SEOCONF['page_title_suffix']);
define('KEYWORDS', $SEOCONF['page_keywords']);
define('DESCRIPTION', $SEOCONF['page_description']);

// obsluga szablonow
$tplName = '';

// priorytet ma szablon pobrany metoda get
if ($rootController->get->has('tpl') and file_exists(ROOT_PATH . '/templates/' . $rootController->get->get('tpl'))) {
    $tplName = $rootController->get->get('tpl');
}

$tplName .= empty($tplName) ? $CONF['default_template'] : $tplName; // wybieramy szablon zapisany w konfiguracji CMS
$rootController->setTemplatePath($tplName);            // ladujemy standardowy
// zapisujemy konfiguracje do szablonow
$rootController->assign('CONF', $CONF);
$rootController->assign('SEOCONF', $SEOCONF);
$rootController->assign('LANG', $GLOBALS);
$rootController->assign('BASE_URL', $CONF['base_url']);
$rootController->assign('ROOT_PATH', ROOT_PATH);
$rootController->assign('TPL_URL', $CONF['base_url'] . '/templates/' . $tplName);
define('TPL_URL', $CONF['base_url'] . '/templates/' . $tplName);
define('AKTUALIZACJA', $CONF['aktualizacja']);
if (isset($CONF['check_google']) && $CONF['check_google'] == 1) {
    define('DOMENA', preg_replace('/http:\/\//i', '', $LANG_MAIN['domain']));
}

// operacja na module
// pobieramy nazwe modulu

if(isset($rootController->getParams()[0]) && $rootController->getParams()[0])
    $moduleConfiguration = $moduleController->getModuleConf($rootController->getParams()[0]);

header("Content-type: text/html; charset=utf-8");

// wybieramy userspace
if(isset($rootController->getParams()[0]) && $rootController->getParams()[0])
$userlevel = preg_match('/^admin(.*)?$/i', $rootController->getParams()[0]) ? 'admin' : 'user';

if ($userlevel == 'admin') {
// jestesmy administratorem
    $dictionaryAdminController = new DictionaryAdminController();
    $translations = $dictionaryAdminController->load($_id, $GLOBALS); // ladujemy zawartosc slownika z bazy
    foreach($translations as $key=>$val){
        $GLOBALS[$key] = $val;
    }

    require_once ROOT_PATH . '/includes/admin.inc.php';
    require_once './modules/admin.inc.php';

} else {
// jestesmy uzytkownikiem
    require_once ROOT_PATH . '/includes/user.inc.php';
}

$rootController->generateSitemap();

// ladujemy modul

if ($moduleConfiguration && file_exists(ROOT_PATH . '/modules/' . $moduleConfiguration['file_name'])) {
    require_once './modules/' . $moduleConfiguration['file_name'];
}else if(!$moduleConfiguration && file_exists(ROOT_PATH . '/modules/'.$rootController->getParams()[0].'.inc.php' ) && isset($rootController->post->getAll()['form'])){
    require_once './modules/'.$rootController->getParams()[0].'.inc.php';
}
else if(!$moduleConfiguration ){
    require_once './modules/main.inc.php';
}
else{
    $rootController->displayError('Strona o podanej nazwie nie istnieje!');
}

/*
if($userlevel != 'admin'){
  dump($category);
}
*/
// konczymy prace strony
//$rootController->close();

if (DEBUG_MODE == 1) {
// krotka statystyka
    //echo "\n".'<!--'."\n";
    echo 'Ilość zapytań MySQL: ' . $rootController->count_queries() . "\n";
    echo 'Czas wykonywania zapytań: ' . $rootController->get_execution_time() . "\n";

    echo 'Czas generowania strony: ' . substr(get_micro_time($page_start), 0, 5) . ' sek.' . "\n";
    //echo '-->';
}
?>
