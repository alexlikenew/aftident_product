<?php

namespace Controllers;

use Models\Newsletter;

class NewsletterController extends Controller
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


    public function __construct($table = 'newsletter'){
        $this->model = new Newsletter($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

    function loadGroup($id)
    {
        $groups = $this->loadGroups();
        return $groups[$id];
    }

    function loadGroups()
    {
        $tab = [];
        $tab[1] = "Zapisani";
        $tab[2] = "Grupa 2";
        $tab[3] = "Grupa 3";

        return $tab;
    }

    public function assignUser($data){
        $id = $this->model->addNewUser($data[1], $data[2]);
        if($id)
            return [
                'status' => 'success'
            ];
        else
            return [
                'status' => 'error'
            ];
    }

    function getInQueueCount($id_template=false)
    {
        $data = $this->model->getInQueueCount($id_template);
        if($data)
            return $data;

        return 0;
    }

    public function getStats($data){
        return $this->model->getStats($data);
    }


    function loadTemplate($id) {
        $data = $this->model->loadTemplate($id);


        $data['value'] = str_replace('{$NEWSLETTER_PATH}', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/newsletter', $data['value']);
        $data['value_org'] = $data['value'];
        //$row['value'] = htmlspecialchars($row['value']);
        return $data;
    }

}