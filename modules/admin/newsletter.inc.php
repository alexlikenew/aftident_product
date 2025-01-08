<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\NewsletterAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('biuletyn_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'newsletter';
$module = new NewsletterAdminController();

switch ($action) {
    case 'edit_rules':
        $rootController->assign('newsletter_rules', $module->getRulesAdmin());
        $rootController->assign('menu_selected', 'newsletter_rules');
        $rootController->setPageTitle($GLOBALS['REGULAMIN_BIULETYNU']);
        $rootController->displayPage($moduleName . '/regulamin.html');
        break;

    case 'update_rules':
        if ($module->UpdateRules($rootController->post->get('newsletter_rules'))) {
            $rootController->assign('info', $GLOBALS['_BIULETYN_INFO_ZMIANY_W_REGULAMINIE']);
        } else {
            $rootController->assign('error', $GLOBALS['_BIULETYN_ERROR_REGULAMIN']);
        }
        $rootController->redirectPage('module?moduleName='.$moduleName . '&action=edit_rules');
        break;

    case 'edit_info':
        $rootController->assign('newsletter_info', $module->getInfoAdmin());
        $rootController->assign('menu_selected', 'newsletter_info');
        $rootController->setPageTitle($GLOBALS['INFORMACJA_O_BIULETYNIE']);
        $rootController->displayPage($moduleName . '/informacja.html');
        break;

    case 'update_info':
        if ($module->updateInfo($rootController->post->get('newsletter_info'))) {
            $rootController->assign('info', $GLOBALS['_BIULETYN_INFO_ZMIANY_W_INFORMACJI']);
        } else {
            $rootController->assign('error', $GLOBALS['_BIULETYN_ERROR_INFORMACJA']);
        }
        $rootController->redirectPage('module?moduleName='. $moduleName . '&action=edit_info');
        break;

    case 'load_template':
        $rootController->assign('template', $module->loadTemplate($rootController->post->get('template')));
        $rootController->assign('stats', $module->getStats());
        $rootController->assign('menu_selected', 'newsletter_mailing');
        $rootController->setPageTitle($GLOBALS['WYSYLANIE_BIULETYNU']);
        $rootController->displayPage( $moduleName . '/mailing.html');
        break;

    case 'mailing':
        $template = $module->loadTemplateAll();

        $rootController->assign('mail_tpl', $template);
        $rootController->assign('stats', $module->getStats($rootController->post->getAll()));
        $rootController->assign('menu_selected', 'newsletter_mailing');
        $rootController->setPageTitle($GLOBALS['WYSYLANIE_BIULETYNU']);
        $rootController->displayPage($moduleName . '/mailing.html');
        die;

    case 'send':
        $user->CheckPrivileges('biuletyn_administration');
        $content = stripslashes($rootController->post->get('mailing_content'));

        $rootController->assign('stats', $module->getStats());
        $rootController->assign('template', $content);
        $rootController->assign('menu_selected', 'newsletter_mailing');
        $rootController->setPageTitle($GLOBALS['WYSYLANIE_NEWSLETTERA']);
        $rootController->displayPage($moduleName . '/potwierdzenie.html');
        break;

    case 'send_mailing':
        $user->CheckPrivileges('biuletyn_administration');
        $count = $module->sendNewsletter($rootController->post->getAll());
        if ($count > 0) {
            $module->_countSend($rootController->post->get('id'), $count);
            $rootController->assign('info', $GLOBALS['BIULETYN_WYSLANO_DO'] . '<b>' . $count . '</b> ' . $GLOBALS['ODBIORCOW'] . '.');
        } else {
            $rootController->assign('error', $GLOBALS['_BIULETYN_ERROR_SEND']);
        }
        $rootController->display('index.tpl');
        break;

    case 'user':
        showUsers();
        break;

    case 'edit_user':
        showUser($rootController->get->get('id'));
        break;

    case 'save_user':
        if ($module->update($rootController->post->getAll())) {
            showUsers();
        } else {
            showUser($rootController->post->get('id'));
        }
        break;

    case 'delete_user':
        $module->delete($rootController->post->get('id'));
        showUsers();
        break;

    case 'add_email':
        $module->addByAdmin($rootController->post->get('add_email_value'), $rootController->post->get('add_name_value'));
        $rootController->redirectPage('module?moduleName='.$moduleName);
        break;
    default:
        showUsers();
        die;
}

function showUsers() {
    global $module, $rootController, $moduleName;

    $limit = 50;
    if ($rootController->hasParameter('limit')) {
        $limit = $rootController->getRequestParameter('limit');
    }

    $page = 0;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    }

    $order_type = '';
    if ($rootController->get->has('order_type')) {
        $order_type = $rootController->get->get('order_type');
    }
    if (empty($order_type)) {
        $rootController->get->set('order_type', 'ASC');
    }

    $order_field = '';
    if ($rootController->get->has('order_field')) {
        $order_field = $rootController->get->get('order_field');
    }
    if (empty($order_field)) {
        $rootController->get->set('order_field', 'first_name');
    }

    $active = 0;
    if ($rootController->get->has('active')) {
        $active = $rootController->get->get('active');
    }
    if ($active < 0) {
        $rootController->get->set('active', '%');
    }
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('pages', $module->getPages($rootController->get->getAll(), $limit));
    $rootController->assign('page', $page);
    $rootController->assign('interval', $limit * $page);
    $rootController->assign('users', $module->loadUsers($rootController->get->getAll(), $page, $limit));
    $rootController->assign('qs', $_SERVER['QUERY_STRING']);
    $rootController->assign('menu_selected', 'newsletter_user');
    $rootController->setPageTitle($GLOBALS['ZARZADZANIE_UZYTKOWNIKAMI_BIULETYNU']);
    $rootController->displayPage($moduleName . '/users.html');
}

function showUser($id) {
    global $oNewsletter, $root, $tpl, $modul;

    if ($user = $oNewsletter->LoadUserById($id)) {
        $post = $root->request->post->getAll();
        if (!empty($post)) {
            $user = $post;
        }

        $tpl->assign('u', $user);
        $tpl->assign('menu_selected', 'newsletter_user');
        $tpl->setPageTitle($GLOBALS['EDYCJA_UZYTKOWNIKA_BIULETYNU']);
        $tpl->displayPage($modul . '/edytuj.html');
    } else {
        showUsers();
    }
}

?>