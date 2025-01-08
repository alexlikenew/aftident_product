<?php


namespace Controllers;

use Models\Category;

class CategoryController extends Controller
{
    protected $model;
    protected $module;
    protected int $scale_width;
    protected int $scale_height;
    protected int $list_height;
    protected int $list_width;
    protected int $detail_width;
    protected int $detail_height;
    protected int $main_width;
    protected int $main_height;
    protected array $all_categories;


    public function __construct($table = 'categories', $parent = null, $module_url = null, $test = false){
        $this->model = new Category($table);
        $this->module = 'kategorie';//$table;
        $this->parent = $parent;
        parent::__construct($this->model, $table);
        $this->module_url = $module_url;

    }

    public function getByPid($pid = 0, $withModule = true)
    {
        $data = $this->model->getByPid($pid);

        $categories = [];
        foreach($data as $cat) {

            $cat['title'] = stripslashes($cat['title']);
            $cat['photo'] = $this->getPhotoUrl($cat['photo'], $cat['id']);
            $cat['photo_list'] = $this->getPhotoUrl($cat['photo_list'], $cat['id']);
            $cat['url'] = $this->getUrlByPath($cat['path_id'], $withModule);
            $categories[] = $cat;
        }
        return $categories;
    }

    public function getSubtree($path_id = null){
        $tree = [];
        $data = $this->model->getSubtree($path_id);

        foreach($data as $row) {
            $tree['item'][$row['id']] = $row;
            $tree['depth'][$row['depth']][] = $row['id'];
        }
        $this->subtree = $tree;

        return $tree;
    }

    public function getSubcategoryByUrl($url){
        $items = [];
        $data = $this->model->getSubcategoryByUrl($url);

        foreach ($data as $row) {
            if (!empty($row['id'])) {
                $items[] = mstripslashes($row);
            }
        }
        return $items;

    }

    public function loadMap(){
        $cats = $this->getSameParent(0);
        return $cats;
    }

    public function getSameParent($parent)
    {
        $data = $this->model->getSameParent($parent);
        $categories = [];
        foreach($data as $row) {
            $row['title'] = stripslashes($row['title']);
            $row['name'] = $row['title'];
            $row['photo'] = $this->getPhotoUrl($row['photo'], $row['id']);
            $row['photo_list'] = $this->getPhotoUrl($row['photo_list'], $row['id']);
            if ($parent == 0) {
                $row['url'] = BASE_URL . '/' . $this->module . '/' . $row['title_url'];
            } else {
                $row['url'] = BASE_URL . '/' . $this->module . '/' . $row['title_url'];
            }

            $categories[] = $row;
        }

        if ((isset($categories)) && (is_array($categories))) {
            foreach ($categories as $key => $val) {
                $categories[$key]['url'] = $this->getUrlByPath($val['path_id']);
            }
        }

        return $categories;
    }


    public function getUrlByPath($path_id, $withModule = true)
    {
        if (!isset($this->all_categories)) {
            $this->getAllCategories();
        }

        $link = [];
        if($this->module_url && $withModule)
            $link[] = $this->module_url;

        $path = explode('.', $path_id);
        $path = array_filter($path);

        if (is_array($path) && !empty($path)) {

            foreach ($path as $k => $v) {
                $link[] = isset($this->all_categories[$v]) ? $this->all_categories[$v]['title_url'] : 'undefined';
            }
        }

        $link = implode('/', $link);

        if(!$this->module_url)
            $link = '/'.$link;

        return $link;
    }

    public function getAllCategories()
    {

        $cats = $this->getSubtree(null);

        $this->all_categories = $cats['item'] ?? [];
    }

    public function loadArticle($str){
        $data = parent::loadArticle($str);
        $data['url'] = $this->getUrlByPath($data['path_id']);
        return $data;
    }

    public function getBreadcrumbsByPath($path_id, $first_element = '')
    {
        if (!isset($this->all_categories)) {
            $this->getAllCategories();
        }
        $breadcrumbs = array();
        $temp = array();
        $path = explode('.', $path_id);
        $path = array_filter($path);
        $count = count($path);
        $i = 0;
        $j = 0;
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j <= $i; $j++) {
                $temp[$i][] = $path[$j];
            }
        }
        foreach ($temp as $k => $v) {
            $path = implode('.', $v);
            $breadcrumbs[$k]['name'] = $this->all_categories[end($v)]['title'];
            $breadcrumbs[$k]['url'] = $this->getUrlByPath($path);
        }
        if (!empty($first_element)) {
            array_unshift($breadcrumbs, $first_element);
        }
        return $breadcrumbs;
    }

    public function getCategories()
    {
        if (!isset($this->all_categories)) {
            $this->getAllCategories();
        }
        return $this->all_categories;
    }

    public function getFirstSubcategory($pid = 0)
    {
        $data = $this->model->getFirstSubcategory($pid);

        if ($data) {
            $data['title'] = stripslashes($data['title']);
            $data['url'] = $this->getUrlByPath($data['path_id']);
            return $data;
        }

        return false;
    }

    public function getPathByPid($pid)
    {
        return $this->getPath($this->getPathById($pid));
    }

    public function getPathById($id)
    {
        return $this->model->getPathById($id);
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
                $row['url'] = BASE_URL . "/" . $this->module . '/' . $row['title_url'];
            } else {
                $row['url'] = BASE_URL . "/" . $this->module . '/' . $path[$i - 1]['title_url'] . "/" . $row['title_url'];
            }
            $row['name'] = $row['title'];
            $path[$i++] = $row;
        }
        return $path;
    }

    public function getCategoryByTitleUrl($title_url = '', $parent_id = '')
    {

        $data = $this->model->getCategoryByTitleUrl($title_url, $parent_id);

        if ($data) {
            $data['title'] = stripslashes($data['title']);
            $data['content'] = stripslashes($data['content']);
            $data['content'] = str_replace('class="fancybox"', 'class="fancybox" rel="fancybox"', $data['content']);

            $data['page_title'] = stripslashes($data['page_title']);
            $data['page_keywords'] = stripslashes($data['page_keywords']);
            $data['page_description'] = stripslashes($data['page_description']);

            if ($data['op_page_title'] == '1') {
                $data['page_title'] = TITLE;
            } elseif ($data['op_page_title'] == '2') {
                $data['page_title'] = $data['title'];
            } elseif ($data['op_page_title'] == '3') {
                $data['page_title'] = $data['title'] . ' - ' . TITLE;
            } elseif ($data['op_page_title'] == '4') {
                $data['page_title'] = $data['page_title'];
            } elseif ($data['op_page_title'] == '5') {
                $data['page_title'] = $data['page_title'] . ' - ' . TITLE;
            }
            if ($data['op_page_keywords'] == '1') {
                $data['page_keywords'] = KEYWORDS;
            } elseif ($data['op_page_keywords'] == '2') {
                $data['page_keywords'] = $data['title'];
            } elseif ($data['op_page_keywords'] == '3') {
                $data['page_keywords'] = $data['title'] . ', ' . KEYWORDS;
            } elseif ($data['op_page_keywords'] == '4') {
                $data['page_keywords'] = $data['page_keywords'];
            } elseif ($data['op_page_keywords'] == '5') {
                $data['page_keywords'] = $data['page_keywords'] . ', ' . KEYWORDS;
            }
            if ($data['op_page_description'] == '1') {
                $data['page_description'] = DESCRIPTION;
            } elseif ($data['op_page_description'] == '2') {
                $data['page_description'] = $data['title'];
            } elseif ($data['op_page_description'] == '3') {
                $data['page_description'] = $data['title'] . ' - ' . DESCRIPTION;
            } elseif ($data['op_page_description'] == '4') {
                $data['page_description'] = substr(strip_tags($data['content_short']), 0, 156);
            } elseif ($data['op_page_description'] == '5') {
                $data['page_description'] = substr(strip_tags($data['content_short']), 0, 156) . ' - ' . DESCRIPTION;
            } elseif ($data['op_page_description'] == '6') {
                $data['page_description'] = $data['page_description'];
            } elseif ($data['op_page_description'] == '7') {
                $data['page_description'] = $data['page_description'] . ' - ' . DESCRIPTION;
            }

            $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);

            $data['url'] = $this->getUrlByPath($data['path_id']);
            return $data;
        } else {
            $this->setError('Brak kategorii o podanej nazwie!');
            return false;
        }
    }

    /* funkcja laduje kategorie o podanej nazwie */

    public function getCategory($title_url = '', $parent_id = '')
    {
        return $this->getCategoryByTitleUrl($title_url, $parent_id);
    }

    public function getArticleById($id, $withModule = true)
    {
        $data = $this->model->getArticleById($id);

        if ($data) {
            $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            $data['url'] = $this->getUrlByPath($data['path_id'], $withModule);
        }

        return $data;
    }

    function getCategoryByTargetId($id)
    {
        $data = $this->model->getCategoryByTargetId($id);
        if ($data) {
            $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            $data['url'] = $this->getUrlByPath($data['path_id']);
        }
        return $data;
    }

    function loadByDepth($depth = 1)
    {
        $q = "SELECT d.*, a.* FROM " . $this->table . " a ";
        $q .= "LEFT JOIN $this->tableDescription d ON a.id=d.parent_id ";
        $q .= "WHERE a.depth='" . (int)$depth . "' ";
        $q .= "AND d.language_id='" . _ID . "' ";
        $q .= "ORDER BY a.`order` ASC ";
        $statement = $this->db->query($q);
        while ($row = $statement->fetch_assoc()) {
            $row['title'] = stripslashes($row['title']);

            $categories[] = $row;
        }

        return $categories;
    }

    function loadByIds($ids)
    {
        if (!is_array($ids)) {
            return false;
        }

        $data = $this->model->loadByIds($ids);

        foreach($data as $row) {

            $row['title'] = stripslashes($row['title']);
            $row['photo'] = $this->getPhotoUrl($row['photo'], $row['id']);

            $categories[] = $row;
        }

        if (is_array($categories)) {
            foreach ($categories as $key => $val) {
                $categories[$key]['url'] = $this->getUrlByPath($val['path_id']);
            }
        }

        return $categories;
    }

    /* funkcja wczytuje podkategorie wg podanego title_url */

    function loadSubcategoriesByTitleUrl($title_url)
    {
        $q = "SELECT B.* FROM ";
        $q .= "(SELECT category_id AS id FROM " . $this->tableDescription . " WHERE title_url='" . addslashes($title_url) . "') AS A LEFT JOIN ";
        $q .= "(SELECT a.*, d.* FROM " . $this->table . " a LEFT JOIN $this->tableDescription d ON a.id=d.parent_id WHERE d.language_id='" . _ID . "' ORDER BY d.title ASC) AS B ON A.id=B.parent_id";

        $statement = $this->db->query($q);
        while ($row = $statement->fetch_assoc()) {
            if (!empty($row['id'])) {
                $items[] = mstripslashes($row);
            }
        }
        return $items;
    }

    function LoadForSubmenu(&$submenu, &$parent_selected, $menu_selected = '')
    {
        $submenu = $this->loadSameParent(0);
        if (is_array($submenu)) {
            foreach ($submenu as $key => $val) {
                $sub = $this->loadSameParent($val['id']);
                if ($sub) {
                    $submenu[$key]['submenu'] = $sub;
                }

            }
        }
    }

    public function search($keyword)
    {
        $keyword = strip_tags($keyword);

        $data = $this->model->search($keyword);

        $result = [];
        $aIds = [];
        foreach($data as $row) {
            $aIds[] = $row['id'];
        }

        if ((is_array($aIds)) && (sizeof($aIds) > 0)) {
            $result = $this->loadByIds($aIds);
        }

        return $result;

    }

    /* funkcja tworzy pole SELECT o podanej nazwie z wszystkimi kategoriami */

    public function createHtmlSelect($title, $type = 'id', $selected = '', $onChange = '', $path_id = '')
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

        $html = '<select class="form-select" name="' . $title . '" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; ' . $onChange . '">' . "\n";
        $html .= $this->createOption($subTree, $type, $selected);
        $html .= '</select>';

        return $html;
    }

    /* funkcja rekurencyjnie tworzy opcje <option> do pola <select> */

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
                    if ($selected == $item[$type])
                        $html .= ' selected="true"';
                    $html .= '>' . $nbsp;
                    $html .= '&nbsp;' . $item['title'] . '</option>' . "\n";
                    $html .= $this->createOption($tree, $type, $selected, $item['id'], $depth);
                }
            }
        }

        return $html;
    }

    public function getSitemapXml()
    {

        $lastmod = date('Y-m-d') . 'T' . date('H:i:s') . '+00:00';
        $changefreq = 'daily';
        $cats = $this->getMenu();

        $article = "";
        if (is_array($cats)) {
            foreach ($cats as $key => $val) {
                $article .= '<url>' . PHP_EOL;
                $article .= '	<loc>'. $val['url'] . '</loc>' . PHP_EOL;
                $article .= '	<lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
                $article .= '	<changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
                $article .= '	<priority>0.9</priority>' . PHP_EOL;
                $article .= '</url>' . PHP_EOL;
                if (isset($val['items']) && is_array($val['items'])) {
                    foreach ($val['items'] as $key1 => $val1) {
                        $article .= '<url>' . PHP_EOL;
                        $article .= '	<loc>' . $val1['url'] . '</loc>' . PHP_EOL;
                        $article .= '	<lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
                        $article .= '	<changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
                        $article .= '	<priority>0.9</priority>' . PHP_EOL;
                        $article .= '</url>' . PHP_EOL;
                    }
                }
            }
        }

        return $article;
    }

    public function getParentIdByPid($pid)
    {
        return $this->model->getParentById($pid);
    }

    public function getMainCategories($limit = null, $withSubcategories = false){
        $data = $this->model->getMainCategories($limit);

        $ids = [];
        foreach($data as $id)
            $ids[] = $id['id'];

        $categories = $this->loadByIds($ids);

        foreach($categories as $k=>$cat){
            $categories[$k]['subcategories'] = $this->getSubcategories($cat['id'], $cat['depth']);
            if(!empty($categories[$k]['subcategories']) && $withSubcategories){
                foreach($categories[$k]['subcategories'] as $skey=>$sub){
                    $categories[$k]['subcategories'][$skey]['subcategories'] = $this->getSubcategories($sub['id'], $sub['depth']);
                }
            }

        }

        return $categories;
    }
    public function getSubcategories($id, $depth, $limit = null){
            $data = $this->model->getSubcategories($id, $depth, $limit);

            $ids = [];
            foreach($data as $id)
                $ids[] = $id['id'];

            if($ids)
                return $this->loadByIds($ids);
            else
                return $ids;
    }

    public function getSubcategoriesIds($id){
        $data = $this->model->getSubcategoriesIds($id);
        $ids = [];
        foreach($data as $id){
            $ids[] = $id['id'];
        }

        return $ids;
    }

    public function getMenu($path_id = '', $onlyActive = false)
    {
        $subTree = $this->getSubtree($path_id);

        if (empty($path_id)) {
            $this->all_categories = $subTree['item'];
        }

        $items = $subTree['item'];
        $depth = $subTree['depth'];
        reset($depth);
        $first_key = key($depth);
        end($depth);
        $last_key = key($depth);
        $i = 1;
        for ($i = $last_key; $i >= $first_key; $i--) {
            foreach ($depth[$i] as $v) {
                if($onlyActive && $items[$v]['active'] == 0){
                    unset($items['[$v]']);
                    continue;
                }
                if (!empty($items[$v]['photo'])) {
                    $items[$v]['photo'] = $this->getPhotoUrl($items[$v]['photo'] ,$items[$v]['id']);
                }
                $items[$v]['url'] = $this->getUrlByPath($items[$v]['path_id']);
                $item = $items[$v];
                if ($i >= $first_key + 1) {
                    $items[$item['parent_id']]['items'][$item['id']] = $item;
                    unset($items[$item['id']]);
                }
            }
        }

        foreach($items as $k=>$val)
            if(!$val['active'])
                unset($items[$k]);
        return $items;
    }

    public function getCategoriesByDepth($depth){
        $data = $this->getSubtree(0);
        $ctaegories = [];
        foreach($data['item'] as $cat){
            if($cat['depth'] == $depth){
                $cat['photo'] = $this->getPhotoUrl($cat['photo'], $cat['id']);
                $cat['url'] = $this->getUrlByPath($cat['path_id']);
                $categories[] = $cat;
            }
        }
        return $categories;
    }

    protected function sitemapItemUrlExtract(array $item): string
    {
        $path = $this->getPath($item['path_id']);

        return $path['url'] ?? '/';
    }

    public function getCategoryTitle($id){
        return $this->model->getTitleById($id);
    }

    public function prepare($article){
        $article['content'] = str_replace('class="fancybox"', 'class="fancybox" rel="fancybox"', $article['content']);
        $article['url'] = BASE_URL . '/' . $this->module_url . '/' . $article['title_url'] ;
        $article['date_add_org'] = $article['date_add'];
        $article['date_add'] = date("j", strtotime($article['date_add_org'])) . " " . miesiac2(date("n", strtotime($article['date_add_org']))) . " " . date("Y", strtotime($article['date_add_org'])) . "r";

        switch ($article['op_page_title']) {
            case 1:
                $article['page_title'] = TITLE_PREFIX . TITLE . TITLE_SUFFIX;
                break;
            case 2:
                $article['page_title'] = TITLE_PREFIX . $article['title'] . TITLE_SUFFIX;
                break;
            case 3:
                $article['page_title'] = TITLE_PREFIX . $article['title'] . ' - ' . TITLE . TITLE_SUFFIX;
                break;
            case 4:
                $article['page_title'] = TITLE_PREFIX . $article['page_title'] . TITLE_SUFFIX;
                break;
            case 5:
                $article['page_title'] = TITLE_PREFIX . $article['page_title'] . ' - ' . TITLE . TITLE_SUFFIX;
                break;
        }

        switch ($article['op_page_keywords']) {
            case 1:
                $article['page_keywords'] = KEYWORDS;
                break;
            case 2:
                $article['page_keywords'] = $article['title'];
                break;
            case 3:
                $article['page_keywords'] = $article['title'] . ', ' . KEYWORDS;
                break;
            case 4:
                break;
            case 5:
                $article['page_keywords'] = $article['page_keywords'] . ', ' . KEYWORDS;
                break;
        }

        switch ($article['op_page_description']) {
            case 1:
                $article['page_description'] = DESCRIPTION;
                break;
            case 2:
                $article['page_description'] = $article['title'];
                break;
            case 3:
                $article['page_description'] = $article['title'] . ' - ' . DESCRIPTION;
                break;
            case 4:
                $article['page_description'] = substr(strip_tags($article['content_short']), 0, 156);
                break;
            case 5:
                $article['page_description'] = substr(strip_tags($article['content_short']), 0, 156) . ' - ' . DESCRIPTION;
                break;
            case 6:
                break;
            case 7:
                $article['page_description'] = $article['page_description'] . ' - ' . DESCRIPTION;
                break;
        }

        if (!empty($article['tagi'])) {
            $article['tagi_url'] = explode('|', str_replace(' ', '-', $article['tagi']));
            $article['tagi'] = explode('|', $article['tagi']);
        }

        return $article;
    }

    public function searchItems(string $keywords): array{
        $result = $this->model->searchItems($keywords);

        $items = [];
        foreach($result as $item){
            $item['photo'] = $this->getPhotoUrl($item['photo'], $item['id']);

            $item['url'] = $this->getUrlByPath($item['path_id']);

            $items[] = $item;
        }
        return $items;
    }

    public function getOtherUrls($item, $languages, $withModule = false){

        $langs= [];
       /* foreach($languages as $lang){
            if($lang['active'] && $lang['id'] != _ID)
                $langs[] = $lang['id'];
        }
        */
        $response = $this->model->getOtherUrls($item['path_id'], $languages);

        if($withModule){
            $url = str_replace('/', '', $this->module_url);
            $moduleUrls = ModuleController::getModuleUrls($url, $languages);
        }

        $data = [];
        foreach($response as $other){
            $data[$other['language_id']][] =  $other['title_url'];
        }
        $result = [];
        foreach($data as $langId=>$parts){
            if($moduleUrls[$langId])
                $result[$langId] = $moduleUrls[$langId].'/'.implode('/', $parts);
            else
                $result[$langId] = '/'.implode('/', $parts);
        }

        return $result;
    }


}