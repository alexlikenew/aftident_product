<?php

namespace Controllers\Inputs;

class SelectInputController extends FormInputController
{
    public $type = FormInputController::TYPE_SELECT;

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}