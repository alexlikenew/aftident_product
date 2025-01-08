<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/aktualnosci.class.php';
//require_once ROOT_PATH . '/includes/faq.class.php';
//require_once ROOT_PATH . '/includes/gallery.class.php';
//require_once ROOT_PATH . '/includes/pages.class.php';

$oAktualnosci = new Aktualnosci($root);
$oFaq = new Faq($root);
$oGallery = new Gallery($root);
$oPages = new Pages($root);

$aRss = array();
$aRss[] = array('name' => $oAktualnosci->getClassName(), 'rss' => $oAktualnosci->LoadRss());
$aRss[] = array('name' => $oFaq->getClassName(), 'rss' => $oFaq->LoadRss());
$aRss[] = array('name' => $oGallery->getClassName(), 'rss' => $oGallery->LoadRss());
$aRss[] = array('name' => $oPages->getClassName(), 'rss' => $oPages->LoadRss());

$tpl->assign('aRss', $aRss);
$tpl->display('rss/artykuly.xml');
die;
?>