<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');
use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\GalleryController;
use Controllers\BlocksController;
use Controllers\SliderController;
use Controllers\BlogController;
use Controllers\ProductController;
use Controllers\InspirationsController;
use Controllers\ModuleController;
use Controllers\FeaturesController;
use Controllers\RoomsController;
use Controllers\NewsController;
use Controllers\FaqController;

$oFiles = new FilesController();
$pagesController = new PagesController();
$blockController = new BlocksController();
$galleryController = new GalleryController();
$sliderController = new SliderController();
$blogController = new BlogController();
$productController = new ProductController();
$inspirationController = new InspirationsController();
$moduleController = new ModuleController();
$featuresController = new FeaturesController();
$roomsController = new RoomsController();
$newsController = new NewsController();
$faqController = new FaqController();

$strPageUrl = '';
$product = null;
if (is_array($rootController->getParams()) && !empty($rootController->getParams()) && count($rootController->getParams()) > 0) {
    $strPageUrl = end($rootController->params);
    $product = $productController->getItem($strPageUrl);
}

$print = 0;
if ($rootController->hasParameter('print')) {
    $print = $rootController->getParameter('print');
}


if ( empty($strPageUrl)) {
    $path[0]['name'] = 'Strona główna';
    $path[0]['url'] = BASE_URL;
    $rootController->assign('path', $path);


    $slider = $sliderController->getItem('main');
    $slider_grants = $sliderController->getItem('Dotacje');

    $rootController->assign('slider', $slider);
    $rootController->assign('slider_grants', $slider_grants);

    $news = $newsController->getRandomItems(3);
    $rootController->assign('news', $news);

    $faqData = $faqController->getMainItems(4);
    $blogData= $blogController->getRandomItems(4);
    $blogLast = $blogController->getLastItems(10);

    $rootController->assign('blog_last', $blogLast);

    //$rooms = $roomsController->getRandomItems(3);
    //$allRooms = $roomsController->getAll();
    //$rootController->assign('all_rooms', $allRooms);
    //$rootController->assign('rooms', $rooms);
    $rootController->assign('homepage', true);
    //$slider = $sliderController->loadArticle('Main');
    //$slider_mob = $sliderController->loadArticle('Main_mob');
    $rootController->assign('slider', $slider);
    $rootController->assign('slider_mob', $slider_mob);
    $mainPage = $pagesController->getMainPage();
   //dd($mainPage);
    $rootController->assign('faq_items', $faqData);
    $rootController->assign('blog_items', $blogData);
    $rootController->assign('main_page', $mainPage);
    //$rootController->assign('main_page', $configController->getOptionLang('main_page'));
    $randomCategories = $productController->getCategoryController()->getRandomItems(100);
    $rootController->assign('randomCategories', $randomCategories);
    $inspirations = $inspirationController->getRandomItems(9);
    $rootController->assign('inspirations', $inspirations);
    $rootController->setPageTitle(TITLE);
    $rootController->setPageKeywords(KEYWORDS);
    $rootController->setPageDescription(DESCRIPTION);
    $rootController->displayPage('main.html');
}elseif($product){

    if ($product['comments'] == 1) {
        $comments_conf['group'] = CommentsController::NEWS;
        $comments_conf['parent_id'] = $product['id'];
        $comments_conf['reload_url'] = $product['url'];

        require_once ROOT_PATH . '/includes/comments.inc.php';
    }

    $rootController->assign('print', true);
    if ($print == 1) {
        $rootController->assign('article', $product);
        $rootController->display('misc/print.html');
        die;
    }

    if ($product['active'] != 1) {
        $rootController->displayError($GLOBALS['_PAGE_UPDATING']);
        die;
    }

    if (!empty($product['gallery_id'])) {
        $gallery = $galleryController->getArticleById($product['gallery_id']);
        if($gallery['active']){
            $product['gallery'] = $gallery;
            $product['gallery']['photos'] = $galleryController->getPhotos($product['gallery_id']);

        } else {
            unset($gallery);
        }
    }

    $category = $productController->getCategoryById($product['category_id']);
    if($category){
        $path = $productController->getCategoryController()->getBreadcrumbsByPath($category['path_id']);
    }
    $path[] = [
        'name'  => $product['title'],
        'url'   => $product['url'],
    ];

    $inspirations = $inspirationController->getRandomItems(2);
    $rootController->assign('inspirations', $inspirations);

    $articles = $productController->getRandomItems(5, $product['id']);
    $otherUrls = $productController->getOtherUrls($product, $LANG_ID);
    $rootController->assign('other_urls', $otherUrls);
    $rootController->assign('articles', $articles);
    $baseURL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
    $rootController->assign('category', $category);
    $rootController->assign('baseURL', $baseURL);
    //$rootController->assign('module', $moduleConfiguration);
    $rootController->assign('path', $path);
    $rootController->assign('product', $product);
    $rootController->setPageTitle($product['page_title']);
    $rootController->setPageKeywords($product['page_keywords']);
    $rootController->setPageDescription($product['page_description']);
    $rootController->displayPage( 'products/show.html');
}
elseif($category = $productController->loadCategory($strPageUrl)){
    $moduleName = 'produkty';
    $moduleConfiguration = $moduleController->getModuleConf($moduleName);

    if (!$pages = $cache->getVariable('pages')) {
        $pages = $productController->getPages(category_path: $category['path_id']);
        $cache->saveVariable($pages, "pages");
    }

    $page = 1;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    }
/*
    $path[] = [
        'name'  => $GLOBALS['_PAGE_PRODUCTS'],
        'url'   => '/produkty'
    ];
*/

    $inspirations = $inspirationController->getRandomItems(2);

    $rootController->assign('inspirations', $inspirations);
    $path = $productController->getCategoryController()->getBreadcrumbsByPath($category['path_id']);

    if (!$articles = $cache->getVariable('articles')) {
        $articles = $productController->getItems($pages, $page, category_id: $category['id'], category_path: $category['path_id']);
        $cache->saveVariable($articles, "articles");
    }

    $rootController->assign('category', $category);

    $lastAdded = $productController->getLastItems(1);
    $rootController->assign('last_added', $lastAdded);

    $otherUrls = $productController->getCategoryController()->getOtherUrls($category, $LANG_ID);

    $rootController->assign('other_urls', $otherUrls);

    $categories = $productController->getMainCategories(true, true);
    $subcategories = [];

    foreach($categories['categories'] as $cat)
        if($category['id'] == $cat['id'])
            $subcategories = $cat['subcategories'];

    $rootController->assign('categories', $categories);
    $rootController->assign('subcategories', $subcategories);

    $rootController->assign('articles', $articles);
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $page);
    $rootController->assign('module', $moduleConfiguration);
    $rootController->assign('path', $path);
    $rootController->setPageTitle(TITLE_PREFIX . $category['page_title'] . TITLE_SUFFIX);
    $rootController->setPageKeywords($category['page_keywords']);
    $rootController->setPageDescription($category['page_description']);

    $rootController->displayPage('products/category.html');

}
elseif ($page = $pagesController->loadArticle($strPageUrl)) {
    
// dd($page);
    if (($page['auth'] == 1) and !LOGGED) {
        $rootController->displayError($GLOBALS['_PAGE_AUTH']);
        die;
    }

    if ($page['active'] != 1) {
        $rootController->displayError($GLOBALS['_PAGE_UPDATING']);
        die;
    }

    if ($page['comments'] == 1) {
        require_once ROOT_PATH . '/includes/comments.class.php';

        $comments_conf['group'] = Comments::PAGES;
        $comments_conf['parent_id'] = $page['id'];
        $comments_conf['reload_url'] = $page['url'];

        require_once ROOT_PATH . '/includes/comments.inc.php';
    }

    $rootController->assign('print', true);
    if ($print == 1) {
        $rootController->assign('article', $page);
        $rootController->display('misc/print.html');
        die;
    }

    if (!empty($page['gallery_id'])) {
        $galleryController = new GalleryController();

        if (!$gallery = $cache->getVariable('gallery')) {
            $gallery = $galleryController->getGalleryById($page['gallery_id']);
            $cache->saveVariable($gallery, "gallery");
        }

        if ($gallery['active'] == 1) {
            if (!$photos = $cache->getVariable('photos')) {
                $photos = $galleryController->getPhotos($page['gallery_id'], $SEOCONF['page_keywords']);
                $cache->saveVariable($photos, "photos");
            }
            $gallery['photos'] = $photos;
            $page['gallery'] = $gallery;
        } else {
            unset($gallery);
        }
    }


    $path[0]['name'] = $page['title'];
    $path[0]['url'] = $page['url'];

    if ($page['id'] == 55) {
        $packages = $productController->getAll();
        $features = $featuresController->getAll();
        //dd($packages);
        $rootController->assign('packages', $packages);
        $rootController->assign('features', $features);
    }

    $articles = $newsController->getAll(0, 0);
    shuffle($articles);
    $rootController->assign('articles', array_slice($articles,0, 3));

    $otherUrls = $pagesController->getOtherUrls($page, $LANG_ID);

    $rootController->assign('other_urls', $otherUrls);
    $allRooms = $roomsController->getAll();
    $rootController->assign('all_rooms', $allRooms);

    $rootController->assign('path', $path);
    $rootController->assign('page', $page);
    $rootController->setPageTitle($page['page_title'] ?? '');
    $rootController->setPageKeywords($page['page_keywords'] ?? '');
    $rootController->setPageDescription($page['page_description'] ?? '');

    $baseURL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
    $rootController->assign('baseURL', $baseURL);

    if ($page['template']) {
        if($page['template'] == 'dane-kontaktowe'){
            $contact = json_decode(base64_decode($configController->getOption('contact')), true);
            $rootController->assign('contact', $contact);

            $departmentController = new DepartmentController();
            $departments = $departmentController->getAll(true);
            $rootController->assign('departments', $departments);

        }

        if($page['template'] == 'strona-tekstowa') {
            $rootController->assign('menuBlack', 1);
        }

        $rootController->displayPage('pages/' . $page['template'] . '.html');
    } else {
        $rootController->displayPage('strona.html');
    }

}else {

    redirect404();
}

?>
