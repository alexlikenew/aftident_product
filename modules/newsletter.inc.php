<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/newsletter.class.php';

global $oModules;   

$oNewsletter = new Newsletter($root);

$modul_conf = $oModules->LoadModuleConf('newsletter');

$path[0]['name'] = $GLOBALS['_PAGE_NEWSLETTER'];
$path[0]['url'] = BASE_URL . '/newsletter.html';
$tpl->assign('path', $path);

$tpl->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_NEWSLETTER'] . ' - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
$tpl->setPageKeywords($modul_conf['page_keywords']);
$tpl->setPageDescription($modul_conf['page_description']);
$tpl->assign('menu_selected', BASE_URL . '/newsletter.html');

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'show_rules':
        $tpl->assign('rules', $oNewsletter->LoadRules());
        $tpl->display('newsletter/regulamin.html');
        break;

    case 'show_remove':
        $tpl->displayPage('newsletter/usun.html');
        break;

    case 'add_email':
        if ($oNewsletter->addUser($root->request->post->getAll())) {
            $tpl->assign('info', $GLOBALS['_NEWSLETTER_INFO']);
            $tpl->displayPage('misc/info.html');
        } else {
            $tpl->assign('informacja', $oNewsletter->LoadInfo());
            $tpl->assign('newsletter_error', $root->messages->getError());
            $tpl->displayPage('newsletter/zapisz.html');
        }
        break;

    case 'activate':
        if ($root->request->get->has('id')) {
            if ($oNewsletter->activateUser($root->request->get->get('id'))) {
                $tpl->assign('info', $GLOBALS['_NEWSLETTER_REGISTER']);
                $tpl->displayPage('misc/info.html');
            } else {
                $root->displayError();
            }
        }
        break;

    case 'remove':
        if ($root->request->get->has('id')) {
            if ($oNewsletter->removeUser($root->request->get->get('id'))) {
                $tpl->assign('info', $GLOBALS['_NEWSLETTER_DEL_EMAIL']);
                $tpl->displayPage('misc/info.html');
            } else {
                $root->displayError();
            }
        }
        break;

    case 'remove_email':
        if ($oNewsletter->sendConfirmRemove($root->request->post->get('email'))) {
            $tpl->assign('info', $GLOBALS['_NEWSLETTER_INFO2']);
            $tpl->displayPage('misc/info.html');
        } else {
            $root->displayError();
        }
        break;

    default:
        $tpl->assign('informacja', $oNewsletter->LoadInfo());
        $tpl->displayPage('newsletter/zapisz.html');
        break;
}
?>