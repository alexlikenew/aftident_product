<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('komentarze_administration');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/comments.class.php';

$modul = 'komentarze-moderacja';
$oComments = new Comments($root);

$page = 1;
if ($root->request->hasParameter('page')) {
    $page = $root->request->getParameter('page');
}

$group = Comments::PAGES;
if ($root->request->hasParameter('group')) {
    $group = $root->request->getParameter('group');
}

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'edit':
        $oComments->Edit($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'delete':
        if ($root->request->get->has('id')) {
            $oComments->Delete($root->request->get->get('id'));
        }
        $root->redirectPage($modul . '.php');
        break;

    default:
        showComments();
        break;
}

function showComments() {
    global $oComments, $group, $page, $tpl;
    $comments = $oComments->LoadAll($group, $page);
    $pages = $oComments->getPagesAll($group);
    $groups = $oComments->getGroups();

    $tpl->assign('comments', $comments);
    $tpl->assign('show_comments', true);
    $tpl->assign('page', $page);
    $tpl->assign('group_name', $groups[$group]['name']);
    $tpl->assign('group', $group);
    $tpl->assign('groups', $groups);
    $tpl->assign('pages', $pages);
    $tpl->assign('query_string', '&group=' . $group);

    $tpl->setPageTitle($GLOBALS['MODERACJA_KOMENTARZY']);
    $tpl->assign('menu_selected', 'komentarze_moderacja');
    $tpl->displayPage('komentarze/moderacja.html');
}

?>