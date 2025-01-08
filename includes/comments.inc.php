<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

if (($CONF['comments_not_logged'] == 0) and !LOGGED) {
    $tpl->assign('can_see_comments', false);
} else {
    $tpl->assign('can_see_comments', true);
}

if (($CONF['comments_not_logged_post'] == 0) and !LOGGED) {
    $tpl->assign('can_write_comments', false);
} else {
    $tpl->assign('can_write_comments', true);
}

$oComments = new Comments($root);

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'add':
        if ($oComments->Add($root->request->post->getAll())) {
            $root->redirectPage($comments_conf['reload_url']);
        } else {
            showComments($comments_conf['parent_id'], $comments_conf['group']);
        }
        break;

    default:
        showComments($comments_conf['parent_id'], $comments_conf['group']);
        break;
}

function showComments($parent_id, $group) {
    global $oComments, $root, $tpl;
    // ladujemy komentarze dla podanego artykulu
    if ((int) $parent_id > 0) {
        $c_pages = $oComments->getPages($parent_id, $group);
        $c_page = $c_pages;
        if ($root->request->hasParameter('c_page')) {
            $c_page = $root->request->getParameter('c_page');
        }

        $comments = $oComments->Load($parent_id, $group, $c_pages, $c_page);

        $tokensmax = sizeof(file(ROOT_PATH . '/js/token/tokens.txt'));
        $tokenid = rand(0, $tokensmax - 1);
        $tpl->assign('tokenid', $tokenid);

        $tpl->assign('comments', $comments);
        $tpl->assign('show_comments', true);
        $tpl->assign('parent_id', $parent_id);
        $tpl->assign('group', $group);
        $tpl->assign('c_pages', $c_pages);
        $tpl->assign('c_page', $c_page);
    }
}

?>