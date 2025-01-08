<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

// funkcja pobiera nazwe modulu z adresu URL
function get_module_name() {
    global $rootController, $moduleController, $currentLang, $LANG_MAIN;

    $search = '(^' . BASE_URL . '\//|'; // usuwamy /BASE_URL
    $search.= '/$|';     // usuwamy slash na koncu
    $search.= '\?.*|';    // usuwamy query-string
    $search.= '\.php|';    // usuwamy .php
    $search.= '\.html|';    // usuwamy .html
    $search.= '\.htm)';    // usuwamy .htm

    $uri = $_SERVER['REQUEST_URI'];
    $uri_clean = preg_replace($search, '', $uri);
    $params = explode('/', $uri_clean);

    if($params[0] == '')
        $params = array_values(array_filter($params));

    $module = $params[0];

    if($currentLang['id'] != $LANG_MAIN['id'])
        $module = $moduleController->getModuleConf($module);

    if ($module == 'main') {
        redirect301(BASE_URL . '/');
    }

    // sprawdzamy czy modul glowny o podanej nazwie istnieje
    if (!file_exists(ROOT_PATH . '/modules/' . $module . '.inc.php')) {
        // nie istnieje - ladujemy modul domyslny
        $module = 'main.inc.php';
    } else {
        $module = $module . '.inc.php';
        // poprawiamy indeksy tablicy parametrow (zmniejszamy o 1)
        for ($i = 1; $i < count($params); $i++) {
            $params[$i - 1] = $params[$i];
        }
        array_pop($params);
    }

    $rootController->setParams($params);

    return $module;
}

// funkcja robi przekierowanie na wskazany url
function redirect301($url) {
    if (empty($url)) {
        exit($GLOBALS['_PAGE_REDIRECT']);
    }

    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $url);
    exit;
}

// funkcja sluzy do pomiarow czasu generowania strony
function get_micro_time($page_start) {
    $page_start = explode(" ", $page_start);
    $page_start = (double) $page_start[1] + (double) $page_start[0];
    $page_stop = explode(" ", microtime());
    $page_stop = (double) $page_stop[1] + (double) $page_stop[0];
    return $page_stop - $page_start;
}

// funkcja sprawdza, czy uzytkownik jest zalogowany
function user_is_logged($level = 'admin') {
    global $configController;

    $pass = $configController->getOption($level . '_pass');

    if (isset($_SESSION[$level . '_auth'])) {
        if ($_SESSION[$level . '_auth'] == session_id() . $pass) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// funkcja loguje uzytkownika
function user_log_in($level = 'admin') {
    global $configController, $rootController;
    $pass = $configController->LoadOption($level . '_pass');

    if (md5($_POST['pass']) == $pass) {
        $_SESSION[$level . '_auth'] = session_id();
        $_SESSION[$level . '_auth'].= $pass;
        return true;
    } else {
        $rootController->setError($GLOBALS['_USER_BAD_PASS']);
        return false;
    }
}

// funkcja wylogowuje uzytkownika
function user_log_out($level = 'admin') {
    unset($_SESSION[$level . '_auth']);
}

function make_url($text) {
    setlocale(LC_CTYPE, 'pl_PL.UTF-8');
    $text = htmlspecialchars_decode($text, ENT_QUOTES);

    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function make_label($text) {
    setlocale(LC_CTYPE, 'pl_PL.UTF-8');
    $text = htmlspecialchars_decode($text, ENT_QUOTES);

    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '_', $text);

    // trim
    $text = trim($text, '_');

    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^_\w]+~', '', $text);

    return $text;
}

// funkcja zwraca adres URL ze zmiennej
function make_url_old($str) {
    $search[0] = '\'';
    $replace[0] = '';
    $search[1] = '\\';
    $replace[1] = '';
    $search[2] = '?';
    $replace[2] = '';
    $search[3] = ' ';
    $replace[3] = '-';
    $search[4] = '/';
    $replace[4] = '';
    $search[5] = '&quot;';
    $replace[5] = '';
    $search[6] = '&amp;';
    $replace[6] = '-and-';
    $search[7] = '"';
    $replace[7] = '';
    $search[8] = '#';
    $replace[8] = '';
    $search[9] = ';';
    $replace[9] = '';
    $search[10] = ':';
    $replace[10] = '';
    $search[11] = ',';
    $replace[11] = '';
    $search[12] = '.';
    $replace[12] = '-';
    $search[13] = '!';
    $replace[13] = '';
    $search[14] = '(';
    $replace[14] = '';
    $search[15] = ')';
    $replace[15] = '';
    $search[16] = '–';
    $replace[16] = '-';
    $search[17] = '%';
    $replace[17] = '';
    $search[18] = '”';
    $replace[18] = '';
    $search[19] = '„';
    $replace[19] = '';
    $search[20] = '+';
    $replace[20] = '';
    $search[21] = '[';
    $replace[21] = '';
    $search[22] = ']';
    $replace[22] = '';
    $search[23] = '&';
    $replace[23] = '-and-';

    $str = preg_replace('/#[a-z0-9]+(;)?/', '', $str);

    $str = strip_pl(str_replace($search, $replace, strip_tags($str)));

    $str = preg_replace("/[^\x9\xA\xD\x20-\x7F]/", "-", $str);

    return rawurlencode(strtolower($str));
}

// funkcja usuwa polskie znaki z tekstu
function strip_pl($str) {
    $search = array('Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż', 'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż');
    $replace = array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z');
    return str_replace($search, $replace, $str);
}

/* funkcja sprawdza czy podany e-mail jest prawidlowy (wrapper) */

function checkEmail($email) {

    return filter_var($email, FILTER_VALIDATE_EMAIL);
    //return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{1,})*\.([a-z]{2,}){1}$/i', $email);
}

// funkcja "multi" stripslashes - dziala na calej tablicy
function mstripslashes(&$array) {
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                mstripslashes($val);
            } else {
                $array[$key] = stripslashes($val);
            }
        }
    }
    return $array;
}

// funkcja "multi" strip_tags - dziala na calej tablicy
function mstrip_tags(&$array, $tags = '') {
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                mstrip_tags($val);
            } else {
                $array[$key] = strip_tags($val, $tags);
            }
        }
    }
    return $array;
}

// funkcja "multi" addslashes - dziala na calej tablicy
function maddslashes(&$array) {
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                maddslashes($val);
            } else {
                $array[$key] = addslashes($val);
            }
        }
    }
    return $array;
}
/*
// funkcja wyswietla zawartosc zmiennej
function dump($zmienna) {
    echo '<pre style="color: red;">';
    print_r($zmienna);
    echo '</pre>';
}
*/
// funkcja zmienia nazwe obrazka wedlug wytycznych
function changeFilename($name, $nr, $file) {
    $lastDot = strrpos($file, '.');
    $ext = substr($file, $lastDot, strlen($file));
    $name = str_replace(' ', '_', strip_pl($name));
    $filename = strtolower($name . $nr . $ext);
    return $filename;
}

// funkcja generuje nazwe miniatury
function nameThumb($filename = '', $append = '_s', $otherExt = false) {
    $lastDot = strrpos($filename, '.');
    $ext = substr($filename, $lastDot, strlen($filename));
    $filename = str_replace($ext, '', $filename);
    if($otherExt)
        return $filename . $append . '.'. $otherExt;
    return $filename . $append . $ext;
}

// funkcja sprawdza poprownosc tokena
function checkToken($tokenid, $token) {
    $tokens = file(ROOT_PATH . '/js/token/tokens.txt');
    $tok = $tokens[$tokenid];

    if (trim($tok) == trim($token) and !empty($token) and !empty($tokenid)) {
        return true;
    } else {
        return false;
    }
}

function miesiac($nr) {
    switch ($nr) {
        case '1': return 'Styczeń';
        case '2': return 'Luty';
        case '3': return 'Marzec';
        case '4': return 'Kwiecień';
        case '5': return 'Maj';
        case '6': return 'Czerwiec';
        case '7': return 'Lipiec';
        case '8': return 'Sierpień';
        case '9': return 'Wrzesień';
        case '10': return 'Październik';
        case '11': return 'Listopad';
        case '12': return 'Grudzień';
    }
}

function miesiac2($nr) {
    switch ($nr) {
        case '1': return 'Stycznia';
        case '2': return 'Lutego';
        case '3': return 'Marca';
        case '4': return 'Kwietnia';
        case '5': return 'Maja';
        case '6': return 'Czerwca';
        case '7': return 'Lipca';
        case '8': return 'Sierpnia';
        case '9': return 'Września';
        case '10': return 'Października';
        case '11': return 'Listopada';
        case '12': return 'Grudnia';
    }
}

function parseCena($cena) {
    $cena = str_replace(',', '.', $cena);
    $cena = str_replace(' ', '', $cena);
    return $cena;
}

function leadingZeros($num, $numDigits) {
    return sprintf("%0" . $numDigits . "d", $num);
}

//include this file whenever you have to use imageconvolution...
//you can use in your project, but keep the comment below :)
//great for any image manipulation library
//Made by Chao Xu(Mgccl) 2/28/07
//www.webdevlogs.com
//V 1.0
if (!function_exists('imageconvolution')) {

    function imageconvolution($src, $filter, $filter_div, $offset) {
        if ($src == NULL) {
            return 0;
        }

        $sx = imagesx($src);
        $sy = imagesy($src);
        $srcback = ImageCreateTrueColor($sx, $sy);
        ImageCopy($srcback, $src, 0, 0, 0, 0, $sx, $sy);

        if ($srcback == NULL) {
            return 0;
        }

        #FIX HERE
        #$pxl array was the problem so simply set it with very low values
        $pxl = array(1, 1);
        #this little fix worked for me as the undefined array threw out errors

        for ($y = 0; $y < $sy; ++$y) {
            for ($x = 0; $x < $sx; ++$x) {
                $new_r = $new_g = $new_b = 0;
                $alpha = imagecolorat($srcback, $pxl[0], $pxl[1]);
                $new_a = $alpha >> 24;

                for ($j = 0; $j < 3; ++$j) {
                    $yv = min(max($y - 1 + $j, 0), $sy - 1);
                    for ($i = 0; $i < 3; ++$i) {
                        $pxl = array(min(max($x - 1 + $i, 0), $sx - 1), $yv);
                        $rgb = imagecolorat($srcback, $pxl[0], $pxl[1]);
                        $new_r += (($rgb >> 16) & 0xFF) * $filter[$j][$i];
                        $new_g += (($rgb >> 8) & 0xFF) * $filter[$j][$i];
                        $new_b += ($rgb & 0xFF) * $filter[$j][$i];
                    }
                }

                $new_r = ($new_r / $filter_div) + $offset;
                $new_g = ($new_g / $filter_div) + $offset;
                $new_b = ($new_b / $filter_div) + $offset;

                $new_r = ($new_r > 255) ? 255 : (($new_r < 0) ? 0 : $new_r);
                $new_g = ($new_g > 255) ? 255 : (($new_g < 0) ? 0 : $new_g);
                $new_b = ($new_b > 255) ? 255 : (($new_b < 0) ? 0 : $new_b);

                $new_pxl = ImageColorAllocateAlpha($src, (int) $new_r, (int) $new_g, (int) $new_b, $new_a);
                if ($new_pxl == -1) {
                    $new_pxl = ImageColorClosestAlpha($src, (int) $new_r, (int) $new_g, (int) $new_b, $new_a);
                }
                if (($y >= 0) && ($y < $sy)) {
                    imagesetpixel($src, $x, $y, $new_pxl);
                }
            }
        }
        imagedestroy($srcback);
        return 1;
    }

}

function truncateWord($string, $limit, $delimiter) {
    $new = preg_replace('/\s+?(\S+)?$/', '', substr($string . ' ', 0, $limit));

    if (strlen($string) > $limit) {
        $new .= $delimiter;
    }

    return $new;
}

function deleteDir($dir) {
    if (!file_exists($dir))
        return true;
    if (!is_dir($dir) || is_link($dir))
        return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..')
            continue;
        if (!deleteDir($dir . DIRECTORY_SEPARATOR . $item)) {
            chmod($dir . DIRECTORY_SEPARATOR . $item, 0777);
            if (!deleteDir($dir . DIRECTORY_SEPARATOR . $item))
                return false;
        };
    }
    return rmdir($dir);
}

function prepareString($string, $strip_tag = false, $hsc = false) {
    $string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
    if ($strip_tag) {
        $string = strip_tags($string);
    }
    $string = trim($string);
    if ($hsc) {
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    return $string;
}

if(!function_exists('getCSRFToken')){
    function getCSRFToken(){
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }

        return $_SESSION['token'];
    }
}

if(!function_exists('showResult')){
    function showResult($state, $message = ''){
        $response = [
            'status'  => $state ? 'success' : 'false',
            'message' =>  $message,

        ];

        echo json_encode($response);

        die;
    }
}

if(!function_exists('prepareKeywordForFulltextSearch')){
    function prepareKeywordForFulltextSearch($keyword)
    {
        $fulltext_keyword = $keyword;
        if ($fulltext_keyword) {
            $temp = explode(" ", $fulltext_keyword);
            if ((is_array($temp)) && (sizeof($temp) > 0)) {
                $fulltext_keyword = "";
                foreach ($temp as $key => $val) {
                    if (strlen($val) >= 7) {
                        $temp1 = substr($val, 0, strlen($val) - 2);
                        $fulltext_keyword .= $temp1 . "* ";
                    } else {
                        $fulltext_keyword .= $val . " ";
                    }
                }

                $fulltext_keyword = trim($fulltext_keyword);
            }
        }


        return $fulltext_keyword;
    }
}
if(!function_exists('redirect404')){
    function redirect404(){
        global $rootController;
        header('HTTP/1.0 404 Not Found');
        $path[0]['name'] = 'Strona nie istnieje';

        $rootController->assign('path', $path);
        $rootController->assign('menuBlack', 1);
        $rootController->setPageTitle(substr(TITLE, 0, 65));
        $rootController->setPageKeywords(KEYWORDS);
        $rootController->setPageDescription(substr(DESCRIPTION, 0, 320));
        $rootController->displayPage('404.html');
        die;
    }
}

?>
