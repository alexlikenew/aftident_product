<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

$config = & $root->conf;
$modul = 'komentarze';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        $return1 = $config->Update('comments_not_logged', $root->request->post->get('comments_not_logged'));
        $return2 = $config->Update('comments_not_logged_post', $root->request->post->get('comments_not_logged_post'));

        if ($return1 and $return2) {
            $tpl->assign('info', '$LANG.ZMIANY_W_KONFIGURACJI_ZOSTALY_ZAPISANE');
        } else {
            $tpl->assign('error', '$LANG.WYSTAPIL_BLAD_PODCZAS_ZSPISYWANIA_ZMIAN_W_KONFIGURACJI');
        }
        $root->redirectPage($modul . '.php');
        break;
}

$tpl->assign('comments_not_logged', $config->LoadOption('comments_not_logged'));
$tpl->assign('comments_not_logged_post', $config->LoadOption('comments_not_logged_post'));

$tpl->setPageTitle($GLOBALS['KONFIGURACJA_KOMENTARZY']);
$tpl->assign('menu_selected', 'komentarze');
$tpl->displayPage('komentarze/konfiguracja.html');
?>