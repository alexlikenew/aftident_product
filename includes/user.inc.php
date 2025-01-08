<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Classes\Cache;
use Controllers\UsersController;
use Controllers\RedirectsController;
use Controllers\MenuController;
use Controllers\SliderController;
use Controllers\ModuleController;
use Controllers\CategoryController;
use Controllers\BlocksController;
use Models\Menu;
use Controllers\CaseStudyController;
use Controllers\BlogController;

$cache = new Cache($configController->getOption('cache_on') ?? false, $configController->getOption('cache_logged_on'), $configController->getOption('cache_lifetime'));
$user = new UsersController();
$categoryController = new CategoryController();
$moduleController = new ModuleController();
$blockController = new BlocksController();
$blogController = new BlogController();
if (!$dst_url = $cache->getVariable('dst_url')) {
    if(empty($redirectsController))
        $redirectsController = new RedirectsController();
    $dst_url = $redirectsController->checkRedirect($_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].(isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:""));
    $cache->saveVariable($dst_url, "dst_url");

}
if($dst_url)
{
    redirect301($dst_url);
}
$menuController = new MenuController('menu');
$menu_types = $menuController->getTypes();
$rootController->assign('menu_types', $menu_types);

$menu_selected = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);

if (!$menu_t = $cache->getVariable('user_inc_menu_t', false, true)) {
    $menu_t = $menuController->load(0, Menu::GROUP_TOP, $menu_selected);
    $cache->saveVariable($menu_t, "user_inc_menu_t", false, true);
}

if (!$menu_l = $cache->getVariable('user_inc_menu_l', false, true)) {
    $menu_l = $menuController->load(0, Menu::GROUP_LEFT, $menu_selected);
    $cache->saveVariable($menu_l, "user_inc_menu_l", false, true);
}

if (!$menu_b = $cache->getVariable('user_inc_menu_b', false, true)) {
    $menu_b = $menuController->load(0, Menu::GROUP_BOTTOM, $menu_selected);
    $cache->saveVariable($menu_b, "user_inc_menu_b", false, true);
}


$rootController->assign('darkMode', $_SESSION['darkMode']);
$mainBlogs = $blogController->showOnMain(4);


$rootController->assign('blogs', $mainBlogs);

//$blocks = $blockController->getUniversalBlocks();

//$rootController->assign('blocks', $blocks);

$host = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];
$scheme = $_SERVER['REQUEST_SCHEME'];

if ($self && !empty($self) && $self != BASE_URL) {
    //$temp_name = explode('.', $self);

    if (preg_match('/.html/', $self)) {
        $self = str_replace('.html', '' , $self);
        $query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
        $newUrl = !empty($query) ? $scheme."://" . $host . $self . "?" . $query : $scheme."://" . $host . $self;
        redirect301($newUrl);
    }
}

$rootController->assign('mt', $menu_t);
$rootController->assign('mb', $menu_b);
$rootController->assign('ml', $menu_l);

$contactInfo = $configController->getContactInfo();
$rootController->assign('contact_info', $contactInfo);

if (!$aZmieniarka = $cache->getVariable('slider')) {
    $sliderController= new SliderController();
    $slider = $sliderController->getSliders();
    $cache->saveVariable($slider, "slider");
}
$rootController->assign('slider', $slider);

//if($_SERVER['REMOTE_ADDR'] == '91.223.64.111'){
    foreach($slider as $sItem){
        if($sItem['id'] == 4)
            $brandsSlider = $sItem;
    }
    $rootController->assign('brands_slider', $brandsSlider);
//}

if (user_is_logged('user')) {
    $rootController->assign('logged', 1);
}

if (DEBUG_MODE == 1) {
    echo '<div class="center error">DEBUG_MODE ON</div>';
}

$rootController->assign('BRAK_ZDJECIA', BASE_URL . '/upload/brak-zdjecia_s.jpg');

// sprawdzamy czy uzytkownik jest zalogowany
if ($user->isLogged()) {
    $rootController->assign('logged', 1);
    $rootController->assign('user', $_SESSION['user']);
    define('LOGGED', 1);
} else {
    define('LOGGED', 0);
}
$rootController->assign('csrf_token', getCSRFToken());
function check_login() {
    global $rootController;
    if (LOGGED == 0) {
        $rootController->displayError();
        die;
    }
}


/*
$other = [];
dd($menuCategories);
foreach($menuCategories as $mainCat){
    if(is_array($mainCat['items']))
        $other = array_merge($other, $mainCat['items']);
}

foreach($other as $key=>$cat)
    if($cat['active'] == 0)
        unset($other[$key]);
*/


//$rootController->assign('other', $other);

?>