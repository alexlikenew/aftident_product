<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

$config = &$root->conf;
$modul = 'szablony';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        if ($config->Update('default_template', $root->request->post->get('template'))) {
            $tpl->assign('info', $GLOBALS['ZMIANY_ZOSTALY_ZAPISANE']);
        } else {
            $tpl->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_ZAPISYWANIA_ZMIAN']);
        }
        $root->redirectPage($modul . '.php');
        break;

    default:
        showTemplates();
        break;
}

function showTemplates() {
    global $config, $tpl;
    $tpl->assign('currTpl', $config->LoadOption('default_template'));
    $tpl->assign('templates', loadTemplates());

    $tpl->setPageTitle($GLOBALS['WYBOR_DOMYSLNEGO_SZABLONU']);
    $tpl->assign('menu_selected', 'szablony');
    $tpl->displayPage('misc/szablony.html');
}

function loadTemplates() {
    $tplDir = ROOT_PATH . '/templates';
    if ($dp = opendir($tplDir)) {
        $i = 0;
        while ($item = readdir($dp)) {
            if ($item != '..' and $item != '.' and $item != 'admin' and is_dir($tplDir . '/' . $item) and !preg_match('/^_/i', $item)) {
                $templates[$i]['name'] = $item;
                $templates[$i]['compile'] = checkDir($tplDir . '/_compile/' . $item);
                $templates[$i]['cache'] = checkDir($tplDir . '/_cache/' . $item);

                if (($templates[$i]['compile'] == true) and ($templates[$i]['cache'] == true)) {
                    $templates[$i]['ready'] = true;
                }
                else
                    $templates[$i]['ready'] = false;
                $i++;
            }
        }
        closedir($dp);
        return $templates;
    }else {
        return false;
    }
}

// end function

function checkDir($dir) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            return true;
        }
        if (@chmod($dir, 0777)) {
            return true;
        }
    } else {
        if (@mkdir($dir)) {
            return true;
        }
    }
    return false;
}

?>