<?php

namespace Controllers;

use Interfaces\FormInputInterface;
use Interfaces\FormInterface;
use Models\Form;
use Controllers\Inputs\FormInputController;

class FormController extends Controller implements FormInterface
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

    public static $types = [
        1    => 'Pole tekstowe',
        2    => 'Pole radio',
        3    => 'Pole select',
        4    => 'Pole ',
        5    => 'Pole tekstowe',
        6    => 'Pole tekstowe',
        7    => 'Pole tekstowe',
        8    => 'Pole tekstowe',
        9    => 'Pole tekstowe',
        10   => 'Pole tekstowe',
        11   => 'Pole tekstowe',
        12   => 'Pole tekstowe',
        13   => 'Pole tekstowe',
        14   => 'Pole tekstowe',
        15   => 'Pole tekstowe',
        16   => 'Pole tekstowe',
        17   => 'Pole tekstowe',
        18   => 'Pole tekstowe',
        19   => 'Pole tekstowe',
        20   => 'Pole tekstowe',
    ];


    public function __construct($table = 'form'){
        $this->model = new Form($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

    public function createInput(FormInputInterface $input)
    {
        // TODO: Implement createInput() method.
    }

    public function updateInput(array $data)
    {
        // TODO: Implement updateInput() method.
    }

    public function deleteInput(int $id, $type)
    {
        // TODO: Implement deleteInput() method.
    }


    public function loadArticle($str){
        $data = parent::loadArticle($str);
        $inputsInfo = $this->getInputsInfo($data['id']);
        $inputs = [];
        foreach($inputsInfo as $inputInfo){
            $input = FormInputController::getByType($inputInfo['type']);
            $input->setDataById($inputInfo['id']);
            $inputs[] = $input;
        }
        $data['inputs'] = $inputs;

        return $data;
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data) {
            $inputsInfo = $this->getInputsInfo($data['id']);
            $inputs = [];
            foreach($inputsInfo as $inputInfo){
                $input = FormInputController::getByType($inputInfo['type']);
                $input->setDataById($inputInfo['id']);
                $inputs[] = $input;
            }
            $data['inputs'] = $inputs;
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

    public function getInputsInfo($id){
        return $this->model->getInputsInfo($id) ?? [];
    }
}