<?php

namespace Controllers;
use Controllers\Inputs\FormInputController;
use Interfaces\FormInputInterface;

class FormAdminController extends FormController
{
    public function __construct($table = 'form')
    {
        parent::__construct($table);
    }

    public function getInputsInfo($id){
        return $this->model->getInputsInfo($id) ?? [];
    }

    public function getInputsTypes(){
        return FormInputController::getAllInputTypes();
    }

    public function saveInput($data){

        $input = FormInputController::getByType($data['type_id']);
        $input->setFormId($data['parent_id']);
        $input->setTitles($data['title']);
        $input->setActiveLangs($data['lang_active']);
        $input->setName($data['name']);
        return $input->create([]);
    }

    public function updateInput($data)
    {

        $input = FormInputController::getByType($data['type_id']);
        $input->updateConfig($data);
        if($input->hasContent())
            $input->updateContent($data['options']);
        $input->setDbId($data['id']);
        $input->setFormId($data['parent_id']);
        $input->setTitles($data['title']);
        $input->setActiveLangs($data['lang_active']);
        $input->setName($data['name']);

        $input->update([], 1);
    }

    public function deleteInput(int $id, $type)
    {
        $input = FormInputController::getByType($type);
        $input->setDbId($id);
        return $input->delete($id);

    }
}