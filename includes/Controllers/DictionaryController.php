<?php

namespace Controllers;

use Models\Dictionary;

class DictionaryController extends Controller{
    protected $model;

    public function __construct($table = 'slownik', $limit = 60)
    {
        $this->model = new Dictionary($table, $limit);
        parent::__construct($this->model, $table);
    }

    public function load(int $id, array $data){
        $response =  $this->model->load($id, $data);
        $data = [];
        foreach($response as $key => $value){
            $value['lang'] = str_replace('BASE_URL', BASE_URL, $value['lang']);
            $value['lang'] = str_replace('ADMIN_EMAIL', ADMIN_EMAIL, $value['lang']);
            if (isset($_SESSION['user']['login'])) {
                $value['lang'] = str_replace('USER_LOGIN', $_SESSION['user']['login'], $value['lang']);
            }
            $data[$value['label']] = $value['lang'];
        }

        return $data;
    }

    public function getItems($pages = 100, $page = 1, $onlyActive=true, $isAdmin = false, $category_id = false, $withModule = true, $category_path = false){
        $response = $this->model->getItems(100, $page);
        $articles = [];
        foreach ($response as $row) {
            $row['opis'] = $this->getDescriptionById($row['id']);
            $articles[] = $row;
        }

        return $articles;
    }


}
