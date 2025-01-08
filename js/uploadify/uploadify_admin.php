<?php
//echo "<pre>";
//print_r($_FILES);
//echo "</pre>";
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

/*
  Uploadify
  Copyright (c) 2012 Reactive Apps, Ronnie Garcia
  Released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

// Define a destination
$subfolder = $_GET['folder'];
$subfolder = str_replace(".", "", $subfolder);
$targetFolder = dirname(__FILE__)."/../../upload/".$subfolder; // Relative to the root
$allowed_ext = explode(",", "pdf,doc,docx,txt,rtf,mpg,avi,zip,rar,jpg,png,gif,bmp");


$multi = false;
if(is_array($_FILES['file']['tmp_name']))
{
    $multi = true;
}

if (!empty($_FILES)) {
    $tempFile = ($multi?$_FILES['file']['tmp_name'][0]:$_FILES['file']['tmp_name']);
//    $_FILES['Filedata']['name'] = make_url($_FILES['Filedata']['name']) . rand(0, 9999) . "." . end(explode(".", strtolower(strtolower($_FILES['Filedata']['name']))));
    $new_name = ($multi?$_FILES['file']['name'][0]:$_FILES['file']['name']);
    $lastDot = strrpos($new_name, '.');
    $ext = substr($new_name, $lastDot, strlen($new_name));
    $new_name = str_replace(' ', '_', strip_pl($new_name));
    $new_name = strtolower($new_name);
    
    if(($new_name)&&(file_exists(str_replace('//', '/', $targetFolder . '/') . '/'.$new_name)))
    {
        $new_name = rand(0,99999).$new_name;
    }
    
    if (preg_match('/WINDOWS/i', $_SERVER['SystemRoot'])) {
//        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder . '/';
        $targetPath = $targetFolder . '/';
        $targetFile = str_replace('//', '/', $targetPath) . $new_name;
    } else {
        $targetPath = $targetFolder . '/';
        $targetFile = str_replace('//', '/', $targetPath) . $new_name;
    }
    
    
    $ext = end(explode(".", strtolower(strtolower($new_name))));
    if(in_array($ext, $allowed_ext))
    {
        move_uploaded_file($tempFile, $targetFile);
        echo 1; 
    }
    else
    {
        echo 0;
    }


}

// funkcja zwraca adres URL ze zmiennej
function make_url($str) {
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
    $search[5] = '&';
    $replace[5] = '-and-';
    $search[6] = '"';
    $replace[6] = '';
    $search[7] = 'quot;';
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
    $replace[12] = '.';
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
    $search[21] = '@';
    $replace[21] = '';
    $search[22] = '$';
    $replace[22] = '';
    $search[23] = '^';
    $replace[23] = '';
    $search[24] = '^';
    $replace[24] = '';
    $search[25] = '<';
    $replace[25] = '';
    $search[26] = '>';
    $replace[26] = '';
    $search[27] = '*';
    $replace[27] = '';
    $search[28] = '_';
    $replace[28] = '';
    $search[29] = '=';
    $replace[29] = '';
    $search[30] = '|';
    $replace[30] = '';
    $search[31] = '}';
    $replace[31] = '';
    $search[32] = '[';
    $replace[32] = '';
    $search[33] = ']';
    $replace[33] = '';
    $search[34] = '{';
    $replace[34] = '';
    $search[35] = '~';
    $replace[35] = '';
    $search[36] = '`';
    $replace[36] = '';
    $search[37] = '.';
    $replace[36] = '-';

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

?>