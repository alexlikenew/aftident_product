<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/files.class.php';
//require_once ROOT_PATH . '/includes/articles.class.php';
//require_once ROOT_PATH . '/includes/categories.class.php';
//require_once ROOT_PATH . '/includes/gallery.class.php';

global $oModules;
$modul = 'lokale'; // nazwa modulu, tabela w bazie, link w adresie
$oFiles = new Files($root);
$oAktualnosci = new Articles($root, 'articles');
$oCategories = new Categories($root);

$modul_conf = $oModules->LoadModuleConf($modul);

if (!$modul_conf['active']) {
    if (!BASE_URL)
        redirect301('/');
    else
        redirect301(BASE_URL);
    die;
}

$strCatUrl = '';
if (is_array($root->params) && count($root->params) > 0) {
    $strCatUrl = $root->params[0];
}

$strPageUrl = '';
if (is_array($root->params) && count($root->params) > 1) {
    $strPageUrl = $root->params[1];
}

$print = 0;
if ($root->request->hasParameter('print')) {
    $print = $root->request->getParameter('print');
}

$tpl->assign('menu_selected', BASE_URL . '/' . $modul . '.html');

//$path[0]['name'] = "Lokale";
//$path[0]['url'] = BASE_URL . '/' . $modul . '.html';

if (!$article = $oCache->getVariable('article')) {
    if (!empty($strPageUrl)) {
        $article = $oAktualnosci->LoadArticle($strPageUrl);
        $oCache->saveVariable($article, "article");
    }
}

if (!$article) {
    if (!$pages = $oCache->getVariable('pages')) {
        $pages = $oAktualnosci->getPages();
        $oCache->saveVariable($pages, "pages");
    }

    $page = 0;
    if ($root->request->hasParameter('page')) {
        $page = $root->request->getParameter('page');
    } else {
//        redirect301(BASE_URL . '/' . $modul . '.html');
    }

    if (!$articles = $oCache->getVariable('articles')) {
        $articles = $oAktualnosci->LoadArticles($pages, $page);
        $oCache->saveVariable($articles, "articles");
    }

    $categories = $oCategories->LoadArticles();

    if ($strCatUrl) {
        $articles = $oAktualnosci->LoadArticlesByCategory($strCatUrl);

        $tpl->assign('menu_selected', BASE_URL . '/' . $modul . '/' . $strCatUrl . '.html');
        $tpl->setPageTitle(TITLE_PREFIX . $articles[0]['category_title'] . ' - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
    } else {
        $tpl->setPageTitle(TITLE_PREFIX . 'Lokale - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
    }

    $tpl->assign('articles', $articles);
    $tpl->assign('categories', $categories);
    $tpl->assign('pages', $pages);
    $tpl->assign('page', $page);

    $tpl->assign('path', $path);

    $tpl->setPageKeywords($modul_conf['page_keywords']);
    $tpl->setPageDescription($modul_conf['page_description']);
    $tpl->displayPage($modul . '/list.html');

    die;
}

if (($article['auth'] == 1) and !LOGGED) {
    $tpl->displayError($GLOBALS['_PAGE_AUTH']);
    die;
}

if ($article['comments'] == 1) {
    require_once ROOT_PATH . '/includes/comments.class.php';

    $comments_conf['group'] = Comments::NEWS;
    $comments_conf['parent_id'] = $article['id'];
    $comments_conf['reload_url'] = $article['url'];

    require_once ROOT_PATH . '/includes/comments.inc.php';
}

$tpl->assign('print', true);
if ($print == 1) {
    $tpl->assign('article', $article);
    $tpl->display('misc/print.html');
    die;
}

if ($article['active'] != 1) {
    $tpl->displayError($GLOBALS['_PAGE_UPDATING']);
    die;
}

if (!empty($article['gallery_id'])) {
    $oGallery = new Gallery($root, 'gallery', 'gallery');
    $gallery = $oGallery->LoadGalleryById($article['gallery_id']);
    if ($gallery['active'] == 1) {
        $tpl->assign('gallery', $gallery);
        $tpl->assign('photos', $oGallery->LoadPhotos($article['gallery_id'], $SEOCONF['page_keywords']));
    } else {
        unset($gallery);
    }
}

if ($pliki = $oFiles->LoadFiles($article['id'], Files::NEWS)) {
    $tpl->assign('pliki', $pliki);
}

$oAktualnosci->_countArticle($article['id']); //zlicza odwiedziny aktualnosci
$path[0]['name'] = $article['category_title'];
$path[0]['url'] = '/lokale/' . $article['category_url'];
$path[1]['name'] = $article['title'];
$path[1]['url'] = $article['url'];

$tpl->assign('path', $path);
$tpl->assign('article', $article);
$tpl->setPageTitle($article['page_title']);
$tpl->setPageKeywords($article['page_keywords']);
$tpl->setPageDescription($article['page_description']);
$tpl->displayPage($modul . '/pokaz.html');
?>