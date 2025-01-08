<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('stat_administration');

$type = '';
if ($root->request->hasParameter('type')) {
    $type = $root->request->getParameter('type');
}

$page = 1;
if ($root->request->hasParameter('page')) {
    $page = $root->request->getParameter('page');
}

$limit = 50;

$oPagesAdmin = new PagesAdmin($root);
$oAktualnosciAdmin = new AktualnosciAdmin($root);
$oFaqAdmin = new FaqAdmin($root);
$oGallery = new GalleryAdmin($root, 'gallery', 'gallery');

switch ($type) {
    case 'podstrony':
        $tpl->assign('odwiedz', $oPagesAdmin->LoadOdwiedziny($limit, $page));
        $tpl->assign('pages', $oPagesAdmin->GetPagesOdwiedziny($limit));
        break;

    case 'aktualnosci':
        $tpl->assign('odwiedz', $oAktualnosciAdmin->LoadOdwiedziny($limit, $page));
        $tpl->assign('pages', $oAktualnosciAdmin->GetPagesOdwiedziny($limit));
        break;

    case 'faq':
        $tpl->assign('odwiedz', $oFaqAdmin->LoadOdwiedziny($limit, $page));
        $tpl->assign('pages', $oFaqAdmin->GetPagesOdwiedziny($limit));
        break;

    case 'galeria':
        $tpl->assign('odwiedz', $oGallery->LoadOdwiedziny($limit, $page));
        $tpl->assign('pages', $oGallery->GetPagesOdwiedziny($limit));
        break;
}

$tpl->assign('page', $page);
$tpl->assign('interval', $limit * ($page - 1));

$tpl->setPageTitle($GLOBALS['ODWIEDZALNOSC']);
$tpl->assign('menu_selected', 'odwiedziny_' . $type);
$tpl->displayPage('statystyki/odwiedziny.html');
?>