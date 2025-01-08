<?php

namespace Controllers;
use Models\Register;

class RegisterAdminController extends RegisterController
{
    protected $model;
        public function __construct($table = 'rejestr'){
            $this->model = new Register($table);
            parent::__construct($table);
        }

        public function getPagesAdmin()
        {
            return parent::getPagesAdmin();
        }

        public function loadArticlesAdmin($pages, $page){
            $start = ($pages - $page) * $this->limit_page;
            $response = $this->model->loadArticles($start, $this->limit_page, false);
            $data = [];
            foreach($response as $event){
                $event['data'] = json_decode($event['content'], true);
                $data[] = $event;
            }
            return $data;
        }

        public function delete(int $id = null): bool{
            return $this->model->delete($id);
        }
}