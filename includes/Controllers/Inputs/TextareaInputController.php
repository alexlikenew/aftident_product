<?php

namespace Controllers\Inputs;

class TextareaInputController extends FormInputController
{
    public $type = FormInputController::TYPE_TEXTAREA;

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}