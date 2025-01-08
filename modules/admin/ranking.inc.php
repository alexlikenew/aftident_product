<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/rankingAdmin.class.php';

$modul = 'ranking';
$oRankingAdmin = new RankingAdmin($root);

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'add_anchor':
        $oRankingAdmin->addAnchor($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'edit_anchor':
        $oRankingAdmin->editAnchor($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'del_anchor':
        if ($root->request->get->has('id')) {
            $oRankingAdmin->delAnchor($root->request->get->get('id'));
        }
        $root->redirectPage($modul . '.php');
        break;

    case 'pokaz':
        if ($root->request->get->has('id')) {
            showPozycje($root->request->get->get('id'));
        } else {
            $root->redirectPage($modul . '.php');
        }
        break;

    default:
        showArticles();
        break;
}

function showArticles() {
    global $oRankingAdmin, $tpl;

    $anchory = $oRankingAdmin->LoadAnchor();

    $tpl->assign('anchory', $anchory);
    $tpl->assign('liczba', $oRankingAdmin->countAnchor());
    $tpl->assign('menu_selected', 'ranking');
    $tpl->setPageTitle($GLOBALS['RANKING_W_GOOGLE']);
    $tpl->displayPage('misc/ranking.html');
}

function showPozycje($id) {
    global $oRankingAdmin, $tpl;

    $pozycje = $oRankingAdmin->LoadPozycje($id);
    $anchor = $oRankingAdmin->LoadName($id);

    $tpl->assign('pozycje', $pozycje);
    $tpl->assign('anchor', $anchor);
    $tpl->assign('menu_selected', 'ranking');
    $tpl->setPageTitle($GLOBALS['RANKING_W_GOOGLE']);
    $tpl->displayPage('misc/ranking-pokaz.html');
}

?>