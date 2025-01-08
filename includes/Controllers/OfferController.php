<?php

namespace Controllers;
use Classes\Image;
use Models\Offer;

class OfferController extends Controller
{
    protected $model;
    protected $module;
    protected $scale_width;
    protected $scale_height;
    protected $list_height;
    protected $list_width;
    protected $detail_width;
    protected $detail_height;
    protected $main_width;
    protected $main_height;


    public function __construct($table = 'offers'){
        $this->model = new Offer($table);
        $this->module = 'oferta';
        parent::__construct($this->model, $table);
    }


    public function getScaleSize(){
        return [
            'width' => $this->scale_width,
            'height' => $this->scale_height,
        ];
    }

    public function loadOffer($str){

        $article = $this->model->getOffer($str);

        if ($article) {
            $article = $this->prepare($article, $this->module);

            $article['photo'] = $this->getPhotoUrl($article['photo'], $article['id']);

            return $article;
        } else {
             $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    function getByPid($pid = 0)
    {

        $data = $this->model->getByPid($pid);

        $categories = [];
        foreach($data as $cat) {

            $cat['title'] = stripslashes($cat['title']);
            $cat['photo'] = $this->getPhotoUrl($cat['photo'], $cat['id']);
            $cat['photo_list'] = $this->getPhotoUrl($cat['photo_list'], $cat['id']);
            $categories[] = $cat;
        }

        return $categories;
    }

    public function getMainOffers(){
        $articles = $this->getByPid(0);

        $result = [];
        foreach($articles as $article){
            $article['url'] = '/'.$article['title_url'];
            $article['realisations'] = $this->getRealisationsById($article['id']);
            $result[] = $article;
        }

        return $result;
    }

    public function getRealisationsById($id){
        $controller = new RealisationsController();
        return $controller->getRealisationsByOfferId($id);
    }

    public function getUrlByPath($path_id)
    {
        if (!isset($this->all_categories)) {
            $this->getAllCategories();
        }

        $link = [];
        $link[] = BASE_URL;
        //$link[] = 'oferta';
        $path = explode('.', $path_id);

        $path = array_filter($path);

        if (is_array($path) && !empty($path)) {

            foreach ($path as $k => $v) {
                $link[] = isset($this->all_categories[$v]) ? $this->all_categories[$v]['title_url'] : 'undefined';
            }
        }

        $link = implode('/', $link);
        return $link;
    }

    public function getSubtree($path_id = null){
        $tree = [];
        $data = $this->model->getSubtree($path_id);

        foreach($data as $row) {
                $depthArray = explode('.', $row['path_id']);
                $tree['item'][$row['id']] = $row;
                array_pop($depthArray);

                $tree['depth'][count($depthArray)][] = $row['id'];
        }

        $this->subtree = $tree;

        return $tree;
    }

    public function getAllCategories()
    {
        $cats = $this->getSubtree(null);

        $this->all_categories = $cats['item'] ?? [];
    }

    public function createHtmlSelect($title, $type = 'id', $selected = '', $onChange = '', $path_id = '', $multiple = false)
    {
        switch ($type) {
            case 'id';
                break;
            case 'title_url';
                break;
            default:
                $type = 'id';
        }

        $subTree = $this->getSubtree($path_id);

        $html = '<select class="form-select ';
        if($multiple)
            $html .= ' select-multiple ';
        $html .= '" name="' . $title;
        if($multiple)
            $html .= '[]';
        $html .='" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; ' . $onChange.' " ';
        if($multiple)
            $html .= " multiple ";
        $html .= '>' . "\n";
        $html .= $this->createOption($subTree, $type, $selected);
        $html .= '</select>';

        return $html;
    }

    public function createOption(&$tree, $type, $selected, $parent = 0, $depth = 0)
    {
        $html = '';
        $item = '';
        $temp_depth = [];

        $nbsp = str_repeat('&nbsp;', $depth * 4);

        $decColor = 255;
        $hexColor = dechex($decColor > 255 ? 255 : $decColor);
        $color = '#' . $hexColor . $hexColor . $hexColor;

        $depth++;
        $temp_depth = &$tree['depth'][$depth];

        if(is_countable($temp_depth)){
            for ($i = 0; $i < count($temp_depth); $i++) {
                $item = &$tree['item'][$temp_depth[$i]];

                if ($parent == $item['parent_id']) {
                    $html .= '<option value="' . $item[$type] . '" style="background:' . $color . '"';


                    if ( (is_array($selected) && in_array($item[$type], $selected))|| $selected == $item[$type])
                        $html .= ' selected';
                    $html .= '>' . $nbsp;
                    $html .= '&nbsp;' . $item['title'] . '</option>' . "\n";
                    $html .= $this->createOption($tree, $type, $selected, $item['id'], $depth);

                }
            }
        }

        return $html;
    }

    public function getPathByPid($pid)
    {
        return $this->getPath($this->getPathById($pid));
    }

    public function getPath($path_id)
    {
        $arrPathId = explode('.', substr($path_id, 0, strlen($path_id) - 1));
        $path = [];

        $data = $this->model->getPath($arrPathId);

        $i = 0;

        foreach($data as $row) {
            $row['title'] = stripslashes($row['title']);
            if ($i == 0) {
                $row['url'] = BASE_URL . '/' . $row['title_url'];
            } else {
                $row['url'] = BASE_URL . '/' . $path[$i - 1]['title_url'] . "/" . $row['title_url'];
            }
            $row['name'] = $row['title'];
            $path[$i++] = $row;
        }
        return $path;
    }

    public function getPathById($id)
    {
        return $this->model->getPathById($id);
    }

    public function getParentIdByPid($pid)
    {
        return $this->model->getParentById($pid);
    }

    public function getItemUrl($str, $languageId = _ID){
        if(BASE_URL)
            return BASE_URL . '/' .$str;
        else
            return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'] . '/' .$str;
    }
    public function loadArticle($str){
        $data = parent::loadArticle($str);
        if($data)
            $data['realisations'] = $this->getRealisationsById($data['id']);
        //if(empty($data['realisations']))
         //   return null;
        foreach($data['realisations'] as $realisation){
            $data['clients'][$realisation['client']['id']] = $realisation['client'];
        }

        return $data;
    }

    public function getByArray($ids){
        $articles = [];
        $data = $this->model->getByIds($ids);

        if ($data) {
            foreach ($data as $article) {

                $article = $this->prepare($article, $this->module);
                if ($article['photo'])
                    $article['photo'] = $this->getPhotoUrl($article['photo'], $article['id']);
                if ($article['logo'])
                    $article['logo'] = $this->getPhotoUrl($article['logo'], $article['id']);
                $article['url'] = '/'.$article['title_url'];
                $article['realisations'] = $this->getRealisationsById($article['id']);
                $articles[] = $article;
            }
        }
        return $articles;
    }

    public function getArticleById($id){
        $data = parent::getArticleById($id);
        $data['url'] = BASE_URL.'/'.$data['title_url'];
        return $data;
    }
}