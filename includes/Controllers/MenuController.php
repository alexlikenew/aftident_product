<?php

namespace Controllers;
use Classes\Sitemap;
use Models\Model;
use Models\Menu;
use Models\Pages;
use Symfony\Component\ErrorHandler\Debug;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Controllers\ProductAdminController;
use Controllers\CatalogAdminController;

class MenuController extends Controller{
    protected $model;
    protected $modules;
    protected $pages;

    public function __construct($table = 'menu'){
        $this->model = new Menu($table);
        $this->modules = new ModuleController();
        $this->pages = new PagesAdminController();
        $this->offer = new OfferController();
        $this->productCategory = (new ProductAdminController())->getCategoryController();
        $this->catalogCategory = (new CatalogAdminController())->getCategoryController();
        parent::__construct($this->model, $table);
    }

    function getTypes() {
        $types = [];
        $types['url'] = Menu::TYPE_URL;
        $types['page'] = Menu::TYPE_PAGE;
        $types['module'] = Menu::TYPE_MODULE;
        $types['nolink'] = Menu::TYPE_NO_LINK;
        $types['category'] = Menu::TYPE_CATEGORY;
        $types['catalog_category'] = Menu::TYPE_CATALOG_CATEGORY;
        $types['offer'] = Menu::TYPE_OFFER;
        return $types;
    }

    public function load($pid = 0, $group = -1, $menu_selected = '', $onlyActive = true)
    {

        $response = $this->model->loadMenu($pid, $group, $menu_selected, $onlyActive);
        $menu = [];
        $i = 0;

        foreach($response as $key=>$data) {

            $pselected = false;
            $menu[$i] = $data;
            if ($data['type'] == Menu::TYPE_MODULE) {

                $menu[$i]['select'] = $this->modules->getArticleById($data['target_id']);
                if ($menu[$i]['select']['title_url'] != 'main') {
                    $menu[$i]['url'] = BASE_URL . '/' . $menu[$i]['select']['title_url'] ;
                } else {
                    $menu[$i]['url'] = BASE_URL . '/';
                }
            }
            if ($data['type'] == Menu::TYPE_PAGE) {
                $menu[$i]['select'] = $this->pages->getArticleById($data['target_id']);

                $menu[$i]['url'] = BASE_URL . '/' . $menu[$i]['select']['title_url'] ;
            }
            if($data['type'] == Menu::TYPE_CATEGORY){

                $menu[$i]['select'] = $this->productCategory->getArticleById($data['target_id'], false);
                $menu[$i]['url'] =  $menu[$i]['select']['url'] ;
            }
            if($data['type'] == Menu::TYPE_CATALOG_CATEGORY){

                $menu[$i]['select'] = $this->catalogCategory->getArticleById($data['target_id']);
                $menu[$i]['url'] =  $menu[$i]['select']['url'] ;
            }
            if ($menu[$i]['has_submenu']) {
                if ($menu[$i]['submenu_type'] == 0) {
                    $subData = $this->getSubmenu($data['id'], $group, $pselected, $menu_selected);
                    $menu[$i]['submenu'] = $subData['submenu'];
                    $menu[$i]['submenuIds'] = $subData['ids'];
                } else {
                    if ($data['submenu_source'] > 0) {
                        $submenu_module = $this->modules->getArticleById($data['submenu_source']);

                        $controllerName = 'Controllers\\'.$submenu_module['class_name'].'Controller';

                        $obj = new $controllerName();
                        if (method_exists($obj, 'getForSubmenu')) {
                            $menu[$i]['submenu'] = [];
                            call_user_func_array([$obj, 'getForSubmenu'], [&$menu[$i]['submenu'], &$pselected, $menu_selected, $submenu_module['title_url']]);
                        } else {
                            $message = 'Klasa ' . $submenu_module['class_name'] . ' nie posiada metody "LoadForSubmenu" przez co automatyczne generowanie podmenu nie będzie działać. Dodaj taką metodę.';
                            $this->displayError($message);
                        }
                    }
                }
            } else {
                $menu[$i]['submenu'] = false;
            }
            if ($menu[$i]['url'] == $menu_selected || $pselected) {
                $menu[$i]['selected'] = true;
            } else {
                $menu[$i]['selected'] = false;
            }
            $menu[$i]['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            $i++;
        }

        if ($i > 0) {
            return $menu;
        } else {
            return false;
        }
    }

    public function getSubmenu($parent_id, $group, $parent_selected, $menu_selected = ''){
        $parent_selected = false;
        $response = $this->model->getSubmenu($parent_id, $group, $parent_selected, $menu_selected);
        $submenu = [];
        $i = 0;
        $ids = [];
        foreach($response as $key=>$data) {
            $pselected = false;
            $submenu[$i] = $data;
            $ids[] = $data['target_id'];
            $submenu[$i]['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if ($data['type'] == Menu::TYPE_MODULE) {

                $submenu[$i]['select'] = $this->modules->getArticleById($data['target_id']);
                if ($submenu[$i]['select']['title_url'] != 'main') {
                    $submenu[$i]['url'] = BASE_URL . '/' . $submenu[$i]['select']['title_url'];
                } else {
                    $submenu[$i]['url'] = BASE_URL . '/';
                }
            }
            if ($data['type'] == Menu::TYPE_PAGE) {
                $submenu[$i]['select'] = $this->pages->getArticleById($data['target_id']);
                $submenu[$i]['url'] = BASE_URL . '/' . $submenu[$i]['select']['title_url'];
            }
            if ($data['type'] == Menu::TYPE_CATEGORY) {
                $submenu[$i]['select'] = $this->productCategory->getArticleById($data['target_id'], false);

                $submenu[$i]['url'] = $submenu[$i]['select']['url'];

            }
            if($data['type'] == Menu::TYPE_CATALOG_CATEGORY){

                $submenu[$i]['select'] = $this->catalogCategory->getArticleById($data['target_id']);
                $submenu[$i]['url'] = $submenu[$i]['select']['url'];
            }
            if ($data['type'] == Menu::TYPE_OFFER) {
                $submenu[$i]['select'] = $this->offer->getArticleById($data['target_id']);
                $submenu[$i]['url'] = BASE_URL . '/offer/' . $submenu[$i]['select']['title_url'];

            }

            if ($submenu[$i]['has_submenu']) {
                if ($submenu[$i]['submenu_type'] == 0) {
                    $subData = $this->getSubmenu($data['id'], $group, $pselected, $menu_selected);
                    $submenu[$i]['submenu'] = $subData['submenu'];
                    $submenu[$i]['submenuIds'] = $subData['ids'];
                } else {
                    if ($data['submenu_source'] > 0) {
                        $submenu_module = $this->modules->getArticleById($data['submenu_source']);
                        require_once ROOT_PATH . '/includes/' . $submenu_module['class_file'];
                        $obj = new $submenu_module['class_name']($this->root);
                        if (method_exists($obj, 'LoadForSubmenu')) {
                            $submenu[$i]['submenu'] = array();
                            call_user_func_array(array($obj, 'LoadForSubmenu'), array(&$submenu[$i]['submenu'], &$pselected, $menu_selected));
                        } else {
                            $message = 'Klasa ' . $submenu_module['class_name'] . ' nie posiada metody LoadForSubmenu przez co automatyczne generowanie podmenu nie będzie działać. Dodaj taką metodę.';
                            $this->displayError($message);
                        }
                    }
                }
            } else {
                $submenu[$i]['submenu'] = false;
            }
            if ($submenu[$i]['url'] == $menu_selected || $pselected) {
                $submenu[$i]['selected'] = true;
                if (!$parent_selected) {
                    $parent_selected = true;
                }
            } else {
                $submenu[$i]['selected'] = false;
            }
            $i++;
        }

        return [
            'submenu'   => $submenu,
            'ids'       => $ids
        ];
    }

    public function getMap() {
        $map = $this->model->_loadMenu(0);
        for ($j = 0; $j < count($map); $j++) {
            $map[$j]['submenu'] = $this->model->_loadMenu($map[$j]['id'], '%');
        }
        return $map;
    }

    public function loadTree($mm) {
        for ($i = 0; $i < count($mm); $i++) {
            $map = $this->model->_loadMenu(0, $mm[$i]['group']);
            for ($j = 0; $j < count($map); $j++) {
                $map[$j]['submenu'] = $this->model->_loadMenu($map[$j]['id']);
                $maps[$i] = $map;
            }
        }
        return $maps;
    }

    protected function sitemapItemUrlExtract(array $item): string
    {
        $module = $this->modules->getArticleById($item['target_id']);

        return '/' . $module['title_url'];
    }

    # /mapa-strony
    public function getSitemapData($title = '', $moduleLink = false)
    {
        $menu_t = $this->load(0, Menu::GROUP_TOP);
        $menu_l = $this->load(0, Menu::GROUP_LEFT);
        $menu_b = $this->load(0, Menu::GROUP_BOTTOM);

    }
}
