<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

global $oModules;

$modul_conf = $oModules->LoadModuleConf('przypomnienie-hasla');

$path[0]['name'] = $GLOBALS['_PAGE_REMINDER'];
$path[0]['url'] = BASE_URL . '/przypomnienie-hasla.html';
$tpl->assign('logowanie', true);
$tpl->assign('path', $path);
$tpl->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_REMINDER'] . ' - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
$tpl->setPageKeywords($modul_conf['page_keywords']);
$tpl->setPageDescription($modul_conf['page_description']);

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'remind_pass':
        if ($oUser->remindPass($root->request->post->get('login'))) {
            $root->redirectPage(BASE_URL . '/przypomnienie-hasla.html');
        } else {
            showPassForm();
        }
        break;

    default:
        showPassForm();
        break;
}

function showPassForm() {
    global $root, $tpl;

    $info = $root->messages->getInfo();
    if (!empty($info)) {
        $tpl->displayInfo();
    } else {
        $tpl->displayPage('uzytkownik/przypomnienie-hasla.html');
    }
}

?>