<?php

namespace Controllers;

use Classes\Sitemap;

class RootController extends Controller{

    public $params;
    protected $dirSiteMap;

    public function __construct()
  {
      $this->dirSiteMap = ROOT_PATH;
      parent::__construct();
  }

  public function setParams($params){
        $this->params = $params;
  }

  public function prepareParams(){
      $search = '(^' . BASE_URL . '\//|'; // usuwamy /BASE_URL
      $search.= '/$|';     // usuwamy slash na koncu
      $search.= '\?.*|';    // usuwamy query-string
      $search.= '\.php|';    // usuwamy .php
      $search.= '\.html|';    // usuwamy .html
      $search.= '\.htm)';    // usuwamy .htm

      $uri = $_SERVER['REQUEST_URI'];
      $uri_clean = preg_replace($search, '', $uri);
      $params = explode('/', $uri_clean);

      if($params[0] == '')
          $params = array_values(array_filter($params));

      $this->setParams($params);
  }

  public function getParameter($name){
        return $this->params[$name] ?? null;
  }

  public function getParams(){
        return $this->params;
  }

  public function setDefaultVariables(){
        if (PHP_SELF_METHOD == 1) {
            if (isset($_SERVER['REDIRECT_URL'])) {
                $_SERVER['PHP_SELF'] = $_SERVER['REDIRECT_URL'];
            } elseif (isset($_SERVER['REDIRECT_SCRIPT_URL'])) {
                $_SERVER['PHP_SELF'] = $_SERVER['REDIRECT_SCRIPT_URL'];
            } else {
                $_SERVER['PHP_SELF'] = BASE_URL;
            }
        }
        if (PHP_SELF_METHOD == 2) {
            if (isset($_SERVER['REDIRECT_URL'])) {
                $_SERVER['PHP_SELF'] = (
                    $_SERVER['HTTP_HOST'] == 'localhost' ||
                    (strpos($_SERVER['HTTP_HOST'], "192.168.") === true)
                )
                    ? explode('?', $_SERVER['REDIRECT_URL'])[0]
                    : $_SERVER['REDIRECT_URL'];
            } elseif (isset($_SERVER['REDIRECT_SCRIPT_URL'])) {
                $_SERVER['PHP_SELF'] = $_SERVER['REDIRECT_SCRIPT_URL'];
            } else {
                $_SERVER['PHP_SELF'] = BASE_URL;
            }
        }
    }

    public function generateSitemap()
    {
        if (false === Sitemap::isTimeToUpdate()) {
            return;
        }

        $xmltext = '';
        $xmltext .= Sitemap::xmlHeader();
        $xmltext .= self::getSitemapHeader();

        $menuController = new MenuController();
        $xmltext .= $menuController->getSitemap('0.9');

        $pagesController = new PagesController();
        $xmltext .= $pagesController->getSitemap();

        $blogController = new BlogController();
        $xmltext .= $blogController->getSitemap();

        $xmltext .= Sitemap::xmlFooter();

        $sitemapfile = fopen($this->dirSiteMap . '/sitemap.xml', 'w');
        fwrite($sitemapfile, $xmltext);
        fclose($sitemapfile);

        Sitemap::updateLastCheckTime();
    }

    public static function getSitemapHeader(): string
    {
        $sitemap = new Sitemap(priority: '1.0');

        return $sitemap->parse('/');
    }

    # /mapa-strony
    public function getSitemapData($title = '', $moduleLink = false){
        $sitemapData = [];

        $menuController = new MenuController();
        $menuData = $menuController->getSitemapData();

        if($menuData)
            $sitemapData[] = $menuData;

        $pagesController = new PagesController();
        $sitemapData[] = $pagesController->getSitemapData($GLOBALS['PAGES']);

        //$roomsController = new RoomsController();
        //$sitemapData[] = $roomsController->getSitemapData('Pokoje');

        //$attractionController = new AttractionsController();
        //$sitemapData[] = $attractionController->getSitemapData('Atrakcje');

        //$productController = new ProductController();
        //$sitemapData[] = $productController->getSitemapData($GLOBALS['OFFER']);

        //$inspirationController = new InspirationsController();
        //$sitemapData[] = $inspirationController->getSitemapData($GLOBALS['INSPIRATIONS']);

        //$catalogController = new CatalogController('catalogs');

        //$sitemapData[] = $catalogController->getSitemapData($GLOBALS['CATALOGS']);

        $blogController = new BlogController();
        $sitemapData[] = $blogController->getSitemapData('Blog');

        $faqController = new FaqController();
        $sitemapData[] = $faqController->getSitemapData('FAQ');
        //$newsController = new NewsController();
        //$sitemapData[] = $newsController->getSitemapData($GLOBALS['NEWS']);

        return $sitemapData;
    }

    public function getSearchResult($keyword){
        if(!is_string($keyword) || strlen($keyword)<2)
            return [];

        $results = [];

        $pagesController = new PagesController();
        $pagesResult= $pagesController->searchItems($keyword);
        if(is_array($pagesResult))
            $results = array_merge($results, $pagesResult);

        $productController = new ProductController();
        $productResult = $productController->searchItems($keyword);
        if(is_array($productResult))
            $results = array_merge($results, $productResult);

        return $results;
    }
}
