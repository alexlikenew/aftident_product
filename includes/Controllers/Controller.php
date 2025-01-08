<?php
namespace Controllers;

use Classes\Sitemap;
use Interfaces\ControllerInterface;
use Models\Model;
use Models\Config;
use Classes\Upload\UploadManager;
use Classes\Upload\UploadException;

use Traits\Mailer;
use Traits\Template;
use Traits\Request;
use Traits\Image;
use Traits\Block;
use Traits\File;
//use Classes\Image;


abstract class Controller implements ControllerInterface {
    use Template,Request, Mailer, Image, Block, File;

    protected $limit_page = 20;
    protected $limit_admin = 60;

    public $params;
    protected $model;
    protected $dir;
    protected $dirPhoto;
    protected $url;
    protected $tableDescription;
    protected $table;
    protected $module_id = 0;
    protected $dirFiles;
    protected $urlFiles;
    protected $module_url;

    public function __construct(Model $model = null, $table = null)
    {
        if (isset($_SESSION['error'])) {
            $this->setError($_SESSION['error']);
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['info'])) {
            $this->setInfo($_SESSION['info']);
            unset($_SESSION['info']);
        }
        // konfiguracja
        $this->conf = new ConfigController();
        //$this->conf->load();
        $this->setGet($_GET);
        $this->setPost($_POST);
        $this->setFiles($_FILES);
        $this->params = [];
        $this->model = $model;

        $this->list_width = 170;
        $this->list_height = 100;
        $this->detail_width = 213;
        $this->detail_height = 166;
        $this->main_width = 90;
        $this->main_height = 90;
        $this->scale_width = 1920;
        $this->scale_height = 1080;

        if($table){
            $this->table = DB_PREFIX . $table;
            $this->tableDescription = $this->table.'_description';
            $this->dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $table;
            $this->dirFiles = ROOT_PATH . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'files';
            $this->dirBlocks = ROOT_PATH . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'blocks';
            $this->urlBlocks = BASE_URL.'/upload/blocks/';
            $this->urlFiles = BASE_URL.'/upload/files/';
            $this->url = BASE_URL.'/upload/'.$table;
        }

        if(isset($this->module) && $this->module){
            $this->module_id = ModuleController::getFileType($this->module);
            $this->module_url = ModuleController::getModuleUrl($this->module_id);
        }


    }

    public function prepare($article){
        $article['content'] = str_replace('class="fancybox"', 'class="fancybox" rel="fancybox"', $article['content']);

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

    public function create(array $data, array $files = null)
    {
        $id =  $this->model->create($data);

        if($id){
            $data['id'] = $id;
            foreach ($data['title'] as $i => $title) {

                $data['title'][$i] = prepareString($title, false);
                //$data['content_short'][$i] = prepareString($data['content_short'][$i], true);
                $data['page_title'][$i] = prepareString($data['page_title'][$i]);
                $data['page_keywords'][$i] = prepareString($data['page_keywords'][$i]);
                $data['page_description'][$i] = prepareString($data['page_description'][$i]);

                $lang = ConfigController::loadLangById($i);

                if ($lang['gen_title']) {
                    $data['title_url'][$i] = ConfigController::makeUniqueUrl(make_url($data['title'][$i]), $this->tableDescription, "title_url", $i,  $data['id'], $this->checkCategorized());
                } else {
                    $data['title_url'][$i] = $data['title_url'][LANG_MAIN] . '-' . $lang['code'];
                }
                $this->model->createDescription($data, $i);
            }

            if($files){
                $data['id'] = $id;
                $this->editPhoto($data, $files);
            }

            return $id;
        }
    }

    public function read()
    {
        // TODO: Implement read() method.
    }

    public function update(array $post): bool
    {
        $auth = isset($post['auth']) ? $post['auth'] : 0;
        $show_title = isset($post['show_title']) ? $post['show_title'] : 0;

        $comments = isset($post['comments']) ? $post['comments'] : 0;

        $post = array_merge($post, [
            'auth'          => $auth,
            'show_article'  => $show_title,
            'comments'      => $comments,
        ]);

        $result = $this->model->update($post, $post['id']);

        if ($result) {
            foreach ($post['title'] as $i => $title) {

                $post['title'][$i] = prepareString($title, false);
                $post['page_title'][$i] = prepareString($post['page_title'][$i]);
                $post['page_keywords'][$i] = prepareString($post['page_keywords'][$i]);
                $post['page_description'][$i] = prepareString($post['page_description'][$i]);


                $lang = ConfigController::loadLangById($i);
                if ($lang['gen_title']) {
                    $post['title_url'][$i] = $this->conf->makeUniqueUrl(make_url($post['title'][$i]), $this->tableDescription, "title_url", $i,  $post['id'], $this->checkCategorized());
                } else {
                    $post['title_url'][$i] = $post['title_url'][LANG_MAIN] . '-' . $lang['code'];
                }

                $desc = $this->model->loadDescriptionById($post['id'], $i);

                if ($desc) {
                    $this->model->updateDescription($post, $i);

                } else {
                    $this->model->createDescription($post, $i);
                }
            }

            $this->setInfo($GLOBALS['_ADMIN_UPDATE_SUCCESS']);
            return true;
        } else {
            $this->setError($GLOBALS['_ADMIN_UPDATE_ERROR']);
            return false;
        }
    }

    public function delete(int $id = null)
    {
        $this->deletePhoto($id);
        return $this->model->delete($id);
    }

    function displayError($errorMsg = '') {
        return $this->displayErrorMessage($errorMsg);
    }

    function displayInfo($infoMsg = '') {
        return $this->displayInfo($infoMsg);
    }

    function redirectPage($url) {
        unset($_SESSION['error']);
        unset($_SESSION['info']);
        if ($this->getError()) {
            $_SESSION['error'] = $this->getError();
        }
        if ($this->getInfo()) {
            $_SESSION['info'] = $this->getInfo();
        }
        header("location: " . $url);
    }

    public function loadConfigController($table = 'config'): ConfigController{
        return new ConfigController($table);

    }

    public function setActive($id, $active){
        if(!$active)
            $this->model->setDescriptionInactive($id);
        return $this->model->setActive($id, $active);
    }

    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = false, $category_id = false, $withModule = true, $category_path = false){

        if($forAdmin){
            $start = ($page -1) * $this->limit_admin;

            $articles = $this->model->loadArticles($start, $this->limit_admin, $onlyActive, $category_id);
        }
        else {
            $start = ($page -1) * $this->limit_page;

            $articles = $this->model->loadArticles($start, $this->limit_page, $onlyActive, $category_id, $category_path);

        }
        if(is_array($articles))
        foreach($articles as $key=>$article) {
            //$articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

           $articles[$key]['url'] =  $this->getItemUrl($articles[$key]);
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
            if ($articles[$key]['video'])
                $articles[$key]['video'] = $this->getVideoUrl($articles[$key]['video'], $articles[$key]['id'], $articles[$key]['video_title']);
            if($articles[$key]['date_add'])
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));

        }
        return $articles;
    }

    public function getLastItems($limit){
        $articles = $this->model->getLastItems($limit);

        foreach($articles as $key=>$article) {
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $articles[$key]['title_url'];
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
            if ($articles[$key]['video'])
                $articles[$key]['video'] = $this->getVideoUrl($articles[$key]['video'], $articles[$key]['id'], $articles[$key]['video_title']);
            if($articles[$key]['date_add'])
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));

        }

        return $articles;
    }

    public function getRandomItems($limit, $toSkip = null){
        $articles = $this->model->getRandomItems($limit, $toSkip);

        foreach($articles as $key=>$article) {
            if($article['category_id'] && method_exists($this, 'getCategoryController'))
                $articles[$key]['category_title'] = $this->getCategoryController()->getCategoryTitle($article['category_id']);
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $articles[$key]['title_url'];
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
            if ($articles[$key]['video'])
                $articles[$key]['video'] = $this->getVideoUrl($articles[$key]['video'], $articles[$key]['id'], $articles[$key]['video_title']);
            if($articles[$key]['date_add'])
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));

        }

        return $articles;
    }

    public function loadArticlesAdmin($pages, $page){

        return $this->getItems($pages, $page, false, true);
    }
/*
    public function loadBlocksAdmin($pages, $page){
        return $this->model->getBlocks($pages, $page, false);
    }
*/
    public function getPagesAdmin(){

        return $this->getPages(false, true);
    }

    public function getPhotoUrl(string $filename = null, int $id = null, $isBlock = false): ?array
    {

        if($isBlock){

            if (!empty($filename) && file_exists($this->dirBlocks. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {
                $types = Image::getOtherAppends();

                $row = [];
                $row['name'] = $filename;
                $row['source']['photo'] = $this->urlBlocks  . $id . '/' . $filename;
                $row['params'] = $this->getImageParams($this->dirBlocks . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);

                foreach ($types as $type) {
                    $name = nameThumb($filename, $type['append'], $type['ext']);

                    if (file_exists($this->dirBlocks . '/' . $id . '/' . $name))
                        $row['source'][$type['type']] = $this->urlBlocks . $id . '/' . $name;
                }

                return $row;
            }
                else{
                   return null;
               }
        }
        else if (!empty($filename) && file_exists($this->dir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {
            $types = Image::getOtherAppends();

            $row = [];
            $row['name'] = $filename;
            $row['source']['photo'] =  $this->url .'/' . $id . '/' . $filename;
            $row['params'] = $this->getImageParams($this->dir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);

            foreach($types as $type){
                $name = nameThumb($filename, $type['append'], $type['ext']);

                if(file_exists($this->dir . '/' . $id . '/' . $name))
                    $row['source'][$type['type']] =  $this->url . '/' . $id . '/' . $name;
            }

            return $row;
        } else {
            return null;
        }
    }

    public function getVideoUrl(string $filename = null, int $id = null, $title = null, $isBlock = false): ?array
    {
        if($isBlock){
            if (!empty($filename) && file_exists($this->dirBlocks. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {

                $row = [];
                $row['name'] = $filename;
                $row['title'] = $title;
                $row['source'] =  $this->urlBlocks .'/' . $id . '/' . $filename;

                return $row;
            } else {
                return null;
            }
        }
        else if (!empty($filename) && file_exists($this->dir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {

            $row = [];
            $row['name'] = $filename;
            $row['title'] = $title;
            $row['source'] =  $this->url .'/' . $id . '/' . $filename;

            return $row;
        } else {
            return null;
        }
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data) {
            if($data['photo'])
                $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if($data['video'])
                $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
            $data['url'] = BASE_URL . '/' . $this->module . '/' . $data['title_url'];
            return $data;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    public function loadDescriptionById($id){
        $data = $this->model->loadDescriptionById($id);
        $descriptions = [];
        foreach($data as $desc) {
            $desc['title'] = str_replace('"', '&quot;', $desc['title']);
            $desc['content'] = trim(preg_replace('/\s\s+/', ' ', $desc['content']));
            $descriptions[$desc['language_id']] = $desc;
        }

        return $descriptions;
    }

    public function getPages($onlyActive = false, $forAdmin = false, $category_str = null, $category_id = null){
        $count = $this->model->getPages($onlyActive, $category_str, $category_id);

        if ($count < 1) {
            $count = 1;
        }

        if($forAdmin)
            return ceil($count / $this->limit_admin);
        else
            return ceil($count / $this->limit_page);
    }

    public function getPageLimit(){
        return $this->limit_page;
    }

    public function loadArticle($str){

        $article = $this->model->getArticle($str);

        if ($article) {
            $article = $this->prepare($article);
            $article['url'] = $this->getItemUrl($article);

            $article['photo'] = $this->getPhotoUrl($article['photo'], $article['id']);
            $article['files'] = $this->getItemFiles($article['id']);

            if($this->module_id){
                $article['blocks'] = $this->getBlocksById($article['id'], $this->module_id);
            }

            //$article['other_urls'] = [];// $this->getOtherUrls($article['id']);

            return $article;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    public function getItem($slug, $isActive = true) {
        $article = $this->model->getArticle($slug, $isActive);
        if($article){
            $article['url'] = $this->getItemUrl($article);
            $article['photo'] = $this->getPhotoUrl($article['photo'], $article['id']);
            $article['blocks'] = $this->getBlocksById($article['id'], $this->module_id);

        }
        return $article;
    }

    function getForSubmenu(&$submenu, &$parent_selected, $menu_selected = '', $url = 'news') {
        $result = $this->model->getForSubmenu();
        if($result){
            foreach ($result as $row) {
                $item['name'] = $row['title'];
                $item['url'] = BASE_URL . '/'.$url.'/' . $row['title_url'];
                if ($item['url'] == $menu_selected) {
                    $item['selected'] = true;
                    if (!$parent_selected) {
                        $parent_selected = true;
                    }
                } else {
                    $item['selected'] = false;
                }
                $item['blank'] = false;
                $item['nofollow'] = false;
                $submenu[] = $item;
            }
        }

    }

    public function getTable(){
        return $this->table;
    }

    public function getModel(){
        return $this->model;
    }


    /* funkcja dodaje wpis do rejestru */

    function saveEventInfo($title, $url, $action, $method, $type) {
        return $this->model->saveEventInfo($title, $url, $action, $method, $type);
    }

    /**
     * end Register methods
     */

    public function searchItems(string $keywords): array{
        $result = $this->model->searchItems($keywords);

        $items = [];
        foreach($result as $item){
            $item['photo'] = $this->getPhotoUrl($item['photo'], $item['id']);

            $item['url'] = $this->getItemUrl($item);

            $items[] = $item;
        }

        if($this->isCategorized){
            $categoryResult = $this->getCategoryController()->searchItems($keywords);
            if($categoryResult)
                $items = array_merge($items, $categoryResult);
        }

        return $items;
    }

    public function getItemUrl($item, $languageId = _ID){
        $url = '';

        if(isset($this->isCategorized)){

            $url .= $this->getUrlByPid($item['category_id']).'/'.$item['title_url'];
        }
        else{
            if($languageId != _ID){
                $baseUrl = str_replace('/', '', $this->module_url);
                $moduleUrls = ModuleController::getModuleUrls($baseUrl, [$languageId]);

                $url .= $moduleUrls[$languageId].'/'.$item['title_url'];

            }
            else
                $url .= $this->module_url.'/'.$item['title_url'];
        }


        return $url;
    }

    public function getOtherUrls($item, $languages){

        $data = $this->model->getOtherUrls($item['id'], $languages);

        $result = [];
        foreach($data as $other){
            $item['title_url'] = $other['title_url'];
            $result[$other['language_id']] = $this->getItemUrl($item, $other['language_id']);
        }

        return $result;
    }

    public function validateLink($url)
    {
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
        return preg_match("/^$regex$/", $url);
    }

    public function getMaxOrder($type = null, $id = null){
        return $this->model->getMaxOrder($type, $id);
    }

    public function getAll($onlyActive = true, $toSkip = false)
    {
        $data = $this->model->getAll($onlyActive, $toSkip);
        $result = [];

        foreach ($data as $item) {
            $item['photo'] = $this->getPhotoUrl($item['photo'], $item['id']);
            $item['url'] = $this->getItemUrl($item);
            $result[] = $item;
        }

        return $result;
    }

    public function sendContact($data, $files = null) {

        $data = mstrip_tags($data);
        $title = 'Kontakt - ' . FIRM_NAME;
        $tresc = '<p><strong>Imię i Nazwisko: </strong>' . $data['1'] . ' </p><br />' . PHP_EOL;
        $tresc .= '<p><strong>EMAIL: </strong>' . $data['2'] . '</p><br />' . PHP_EOL;
        $tresc .= '<p><strong>Telefon: </strong>' . $data['3'] . '</p><br />' . PHP_EOL;
        $tresc .= '<p><strong>Treść wiadmości: </strong>' . nl2br($data['message']) . '</p>';
        if($files['file'])
            $this->addAttachment([
                'path'  => $files['file']['tmp_name'],
                'name'  => $files['file']['name']
            ]);
        $this->setSubject($title);
        $this->setBody($tresc);

        if ($this->sendHTML(BIURO_EMAIL)) {

            $this->clearRecipients();

            $tresc = $GLOBALS['FORMULARZ_MAIL_TXT'].PHP_EOL.PHP_EOL.$tresc;
            $this->setBody($tresc);
            $this->sendHTML($data['2']);

            $this->clearRecipients();

            $this->setInfo($GLOBALS['_PAGE_SEND']);
            return [
                'status'    => 'success'
            ];
        } else {
            $this->setError('error_contact', $GLOBALS['_PAGE_NO_SEND']);
            return [
                'status'    => 'error'
            ];
        }
    }

    public function sendReservation($data, $files = null) {

        $url = str_replace('/ax_reservation', '', $_SERVER['SCRIPT_URI']);
        $data = mstrip_tags($data);
        $title = 'Kontakt - ' . FIRM_NAME;
        $tresc = '<p><strong>Imię i Nazwisko: </strong>' . $data['1'] . ' ' .$data['2'].' </p><br />' . PHP_EOL;
        $tresc .= '<p><strong>EMAIL: </strong>' . $data['4'] . '</p><br />' . PHP_EOL;
        $tresc .= '<p><strong>Telefon: </strong>' . $data['3'] . '</p><br />' . PHP_EOL;
        $tresc .= '<p><strong>Pokój: </strong><a href="'.$url.$data['room']['url'].'">'.$data['room']['title'].'</a></p><br />';
        $tresc .= '<p><strong>Od: </strong>'.$data['dateFrom'].'</p><br />';
        $tresc .= '<p><strong>Do: </strong>'.$data['dateTo'].'</p><br />';
        $tresc .= '<p><strong>Treść wiadmości: </strong>' . nl2br($data['message']) . '</p>';
        if($files['file'])
            $this->addAttachment([
                'path'  => $files['file']['tmp_name'],
                'name'  => $files['file']['name']
            ]);
        $this->setSubject($title);
        $this->setBody($tresc);

        if ($this->sendHTML(BIURO_EMAIL)) {

            $this->clearRecipients();

            $tresc = $GLOBALS['FORMULARZ_MAIL_TXT'].PHP_EOL.PHP_EOL.$tresc;
            $this->setBody($tresc);
            $this->sendHTML($data['4']);

            $this->clearRecipients();

            $this->setInfo($GLOBALS['_PAGE_SEND']);
            return [
                'status'    => 'success'
            ];
        } else {
            $this->setError('error_contact', $GLOBALS['_PAGE_NO_SEND']);
            return [
                'status'    => 'error'
            ];
        }
    }

    public function checkIds(array $data): array{
        foreach($data as $k=>$v){
            if(is_numeric($v))
                $data[$k] = (int)$v;
            else
                unset($data[$k]);
        }
        return $data;
    }

    public function updateSearch($keyword, $quantity){

        if($this->model->keywordExists($keyword))
            $this->model->updateSearchResult($keyword, $quantity);
        else
            $this->model->createSearchResult($keyword, $quantity);
    }

    public function convertSearchResultToAutocomplete($products, $type_model=false)
    {
        if (empty($products)) {
            return [];
        }

        $jsonArray = [];

        foreach ($products as $product) {

            $jsonProduct = [];
            $jsonProduct['title'] = isset($product['title']) ? $product['title'] : '';
            $jsonProduct['subtitle'] = isset($product['subtitle']) ? $product['subtitle'] : '';
            $jsonProduct['url'] = isset($product['url']) ? $product['url'] : '';
            $jsonProduct['photo'] = $product['photo'];

            $jsonArray[] = $jsonProduct;
        }

        return $jsonArray;
    }

    public function getAllPageTemplates($template, $directory){
        $path  = ROOT_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $directory;

        $files = scandir($path);
        unset($files[0]);
        unset($files[1]);
        $templates = [];
        foreach($files as $file){
            $fileArray = explode('.', $file);
            $extension = array_pop($fileArray);

            if($extension != 'html' || preg_match('/index/', $file))
                continue;

            $fileName = implode('.', $fileArray);

            $templates[$fileName] = str_replace(['_', '-'], ' ', $fileName);
        }
        return $templates;
    }

    public function editVideo($data, $files){
        $article = $this->getArticleById($data['id']);

        if (!empty($files['video']['name'])) {

            $extArray = explode('.', $files['video']['name']);
            $extension= end($extArray);
            $oFile = uploadManager::get('video');

            if (!$oFile->isOk())
                throw new uploadException($oFile->getErrorAsString());

            $this->createDir($article['id']);

            $file_size = $this->conf->getOption('files_max_size');

            if (!$oFile->isValidSize($file_size . " KB")) {
                $this->setError($GLOBALS['_FILE_TO_BIG']);
                return false;
            }

            $name = changeFilename($data['id'], time(), $files['video']['name']);
            move_uploaded_file($files['video']['tmp_name'], $this->dir . DIRECTORY_SEPARATOR . $article['id'] . DIRECTORY_SEPARATOR .  $name);
            return $this->model->updateVideo($name, $data);
        }
    }

    public function deleteVideo($id){
        $article = $this->getArticleById($id);
        if($article['video']){
            if(file_exists($this->dir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $article['video']['name']))
                unlink($this->dir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $article['video']['name']);
            return $this->model->deleteVideo($id);
        }
    }

    public function move(int $id, int $order_new):bool{
        $item = $this->getArticleById($id);

        if($item){
            return $this->model->updateItemsOrder($item, $order_new);
        }
        return false;
    }

    public function showOnMain($limit){
        $data = $this->model->showOnMain($limit);

        $items = [];
        foreach($data as $item){
            if ($item['photo'])
                $item['photo'] = $this->getPhotoUrl($item['photo'], $item['id']);
            if ($item['video'])
                $item['video'] = $this->getVideoUrl($item['video'], $item['id'], $item['video_title']);
            if($item['date_add'])
                $item['date_add'] = date('d.m.Y', strtotime($item['date_add']));
            if($item['title_url'])
                $item['url'] = $this->getItemUrl($item);
            $items[] = $item;
        }

        return $items;
    }

    public function getSitemap($priority = '0.8'): ?string
    {
        $data = $this->model->getSitemapData();
        $sitemap = new Sitemap(priority:  $priority);

        $result = '';

        foreach ($data as $item) {
            if (isset($item['no_searchable']) && $item['no_searchable'] == 1) continue;

            $url = $this->sitemapItemUrlExtract($item);
            $result .= $sitemap->parse($url);
            $result .= $this->sitemapTagsParser($item);
        }

        return $result;
    }

    /**
     * Nadpisać jeśli url jest wyciągany przez inną metodę.
     *
     * @param array $item
     * @return string
     */
    protected function sitemapItemUrlExtract(array $item): string
    {
        return $this->getItemUrl($item);
    }

    protected function sitemapTagsParser(array $item)
    {
        $result = '';

        $sitemapTags = new Sitemap(priority: '0.6');

        $tags = explode('|', str_replace(' ', '-', $item['tagi']));

        if (count($tags) > 1) {
            foreach ($tags as $tag) {
                $result .= $sitemapTags->parse("/szukaj/{$tag}");
            }
        }

        return $result;
    }

    public function getSitemapData($title, $moduleLink = false){
        $result['title'] = $title;
        if($moduleLink)
            $result['url'] = BASE_URL.'/'.$this->module_url;

        if($this->isCategorized){

            $result['has_categories'] = true;
            $categories = $this->getCategoryController()->getCategories();
            foreach($categories as $category){

                $category['url'] = $this->getCategoryController()->getUrlByPath($category['path_id']);
                $category['items'] = $this->getItemsByCategory($category['id']);
                $result['items'][] = $category;
            }

        }
        else{
            $result['has_categories'] = false;
            $data = $this->getAll();

            foreach ($data as $item){
                $item['url'] = $this->getItemUrl($item);
                $result['items'][] = $item;
            }
        }

        return $result;
    }

    public function duplicate($data, $files, $blocks, $type){
        $oldArticle = $this->getArticleById($data['id']);


        $id = $this->create($data);

        if($blocks)
            foreach($blocks as $block)
                $this->assignBlockToItem($id, $block, $type);

        if($oldArticle['photo'])
            $this->duplicatePhotos($id, $oldArticle['photo']);
        if($files)
            $this->duplicateItemFiles($id, $type, $files);


        return $id;

    }

    public function duplicateItemFiles($id, $type, $files){
        $filesController = new FilesAdminController();
        $filesController->duplicateItemFiles($id, $type, $files);
    }

    public function assignBlockToItem($id, $block, $type){
        return $this->model->assignBlock($block['id'], $id, $type, $block['sequence']);
    }

    public function massDelete($data){
        foreach($data['list_items'] as $id){
            $this->delete($id);
        }
        return true;
    }

    public function checkCategorized(){
        if(isset($this->isCategorized) || isset($this->parent))
            return true;
        return false;
    }

    public function getMainItems($limit, $toSkip = false){
        $articles = $this->model->getMainItems($limit, $toSkip);

        foreach($articles as $key=>$article) {
            if($article['category_id'] && method_exists($this, 'getCategoryController'))
                $articles[$key]['category_title'] = $this->getCategoryController()->getCategoryTitle($article['category_id']);
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $articles[$key]['title_url'];
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
            if ($articles[$key]['video'])
                $articles[$key]['video'] = $this->getVideoUrl($articles[$key]['video'], $articles[$key]['id'], $articles[$key]['video_title']);
            if($articles[$key]['date_add'])
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));

        }

        return $articles;
    }

//    public function

}
