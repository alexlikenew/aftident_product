<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$submodule = '';
if (is_array($rootController->getParams()) && count($rootController->getParams()) > 0) {
    $submodule = $rootController->params[0];
}

switch ($submodule) {
    case 'dane':
        check_login();

        $path[0]['name'] = $GLOBALS['_PAGE_USER'];
        $path[0]['url'] = BASE_URL . '/konto/dane.html';
        $rootController->assign('path', $path);
        $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_USER'] . ' - ' . TITLE . TITLE_SUFFIX);
        $rootController->setPageKeywords(KEYWORDS);
        $rootController->setPageDescription(DESCRIPTION);

        $action = '';
        if ($rootController->hasParameter('action')) {
            $action = $rootController->getParameter('action');
        }

        if ($action == 'save_changes') {
            if ($user->update($rootController->post->getAll())) {
                $rootController->redirectPage(BASE_URL . '/konto/dane.html');
            }
        }

        $info = $rootController->getInfo();
        if (!empty($info)) {
            $rootController->displayInfo();
        } else {
            if ($user = $user->load($_SESSION['user']['id'])) {
                $post = $rootController->post->getAll();
                if (!empty($post)) {
                    $user['first_name'] = $rootController->post->get('first_name');
                    $user['last_name'] = $rootController->post->get('last_name');
                    $user['firm_name'] = $rootController->post->get('firm_name');
                    $user ['phone'] = $rootController->post->get('phone');
                    $user['address'] = $rootController->post->get('address');
                    $user['post_code'] = $rootController->post->get('post_code');
                    $user['business'] = $rootController->post->get('business');
                    $user['nip'] = $rootController->post->get('nip');
                    $user['city'] = $rootController->post->get('city');
                }
                $rootController->assign('u', $user);
                $rootController->displayPage('uzytkownik/dane.html');
            } else {
                $rootController->displayError();
            }
        }
        break;

    case 'haslo':
        check_login();

        $path[0]['name'] = $GLOBALS['_PAGE_PASS'];
        $path[0]['url'] = BASE_URL . '/konto/haslo.html';
        $rootController->assign('path', $path);
        $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_PASS'] . ' - ' . TITLE . TITLE_SUFFIX);
        $rootController->setPageKeywords(KEYWORDS);
        $rootController->setPageDescription(DESCRIPTION);

        $action = '';
        if ($rootController->hasParameter('action')) {
            $action = $rootController->getParameter('action');
        }

        if ($action == 'save_pass') {
            if ($user->changePass($rootController->post->getAll(), $_SESSION['user']['admin'])) {
                $rootController->redirectPage(BASE_URL . '/konto/haslo.html');
            }
        }

        $info = $rootController->getInfo();
        if (!empty($info)) {
            $rootController->displayInfo();
        } else {
            $rootController->displayPage('uzytkownik/haslo.html');
        }
        break;

    case 'zalogowany':
        $path[0]['name'] = $GLOBALS['_PAGE_LOGIN_USER'];
        $path[0]['url'] = BASE_URL . '/konto/zalogowany.html';
        $rootController->assign('path', $path);
        $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_LOGIN_USER'] . ' - ' . TITLE . TITLE_SUFFIX);
        $rootController->setPageKeywords(KEYWORDS);
        $rootController->setPageDescription(DESCRIPTION);

        if ($user->isLogged()) {
            $rootController->displayPage('uzytkownik/zalogowany.html');
        } else {
            $rootController->displayError();
        }
        break;

    default:
        $rootController->displayError($GLOBALS['_PAGE_NOT_EXIST']);
        break;
}
?>