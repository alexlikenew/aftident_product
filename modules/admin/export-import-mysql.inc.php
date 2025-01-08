<?php

/*
 *      Modul odpowiadajacy za export bazy danych
 *      plik:       export.inc.php
 *      autor:      Technetium [Tc]
 *                  Przemek Szalko
 *                  01 czerwiec 2007
 *      modify:     Marek Kleszyk
 *                  05 luty 2009
 *      system:     T.CMS-4.0-SEO
 */
/*
if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

require_once ROOT_PATH . '/includes/mysqlAdmin.class.php';

$oUsers->CheckPrivileges('page_config');

$oMysqlAdmin = new MysqlAdmin($root);
$config = &$root->conf;

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

$katalog = $oMysqlAdmin->sprawdzKatalog();

switch ($action) {
    case 'export':
        if (class_exists("ZipArchive")) {
            $oMysqlAdmin->_mysqlExportZip($katalog);
        } else {
            $oMysqlAdmin->_mysqlExport($katalog);
        }
        $root->redirectPage('export-import-mysql.php');
        break;

    case 'import':
        $oMysqlAdmin->_mysqlImport($root->request->post->get('name_file'));
        $root->redirectPage('export-import-mysql.php');
        break;

    case 'pobierz':
        $name = $root->request->get->get('name');
        if (!empty($name)) {
            $file = $name;
            $katalog_baza = $opcje['op_page_title'] = $config->LoadOption('katalog_baza');

            if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\.sql/i', $file)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $file . '"');
                ob_end_flush();
                readfile(ROOT_PATH . '/' . $katalog_baza . '/' . $file . '');
                die();
            } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\.zip/i', $file)) {
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . $file . "\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . filesize(ROOT_PATH . '/' . $katalog_baza . '/' . $file . ''));
                ob_end_flush();
                readfile(ROOT_PATH . '/' . $katalog_baza . '/' . $file . '');
                die();
            }
        }
        break;

    default:
        showForm($katalog);
        break;
}

function showForm($katalog) {
    global $tpl, $oMysqlAdmin;
    $file = $oMysqlAdmin->LoadPliki($katalog);
    $tpl->assign('file', $file);

    $tpl->assign('menu_selected', 'mysql');
    $tpl->setPageTitle($GLOBALS['EXPORT_BAZY_DANYCH_MYSQL']);
    $tpl->displayPage('misc/baza-mysql.html');
}
*/
?>