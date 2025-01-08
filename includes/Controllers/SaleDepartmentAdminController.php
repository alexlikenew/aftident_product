<?php

namespace Controllers;

class SaleDepartmentAdminController extends SaleDepartmentController
{
    public function __construct($table = 'sale_department')
    {
        parent::__construct($table);
    }

    public function create(array $data, array $files = null)
    {
        $id =  $this->model->create($data);

        if($id){
            $data['id'] = $id;
            foreach ($data['workplace'] as $i => $title) {

                $data['workplace'][$i] = prepareString($title, false);

                $this->model->createDescription($data, $i);
            }

            if($files){
                $data['id'] = $id;
                $this->editPhoto($data, $files);
            }

            return $id;
        }
    }

    public function update(array $post): bool
    {
        $result = $this->model->update($post, $post['id']);

        if ($result) {
            foreach ($post['workplace'] as $i => $title) {

                $post['workplace'][$i] = prepareString($title, false);

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
    public function createPhotoName($article, $files, $key){

        return isset($article['name'])
            ? changeFilename(make_url($article['name']) . "-" . rand(0, 1000), '', $files[$key]['name'])
            : '';

    }
}