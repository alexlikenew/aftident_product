<?php

namespace Controllers;

class PriceStepsAdminController extends PriceStepsController
{
    public function __construct($table = 'price_steps')
    {
        parent::__construct($table);
    }

    public function create(array $data, array $files = null)
    {
        $id = $this->model->create($data);

        if($id){
            $data['id'] = $id;
            foreach($data['lang_active'] as $langId){
                $this->model->createDescription($data, $langId);
            }
            return $id;
        }
    }

    public function update(array $post):bool{
        $this->model->update($post, $post['id']);
        foreach($post['lang_active'] as $langId){
            $this->model->updateDescription($post, $langId);
        }
        return true;
    }

}