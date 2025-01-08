<?php

namespace Controllers\Inputs;

class CheckboxInputController extends FormInputController
{
    public $type = FormInputController::TYPE_CHECKBOX;

    protected $config = [
        'required'      => false,
    ];
    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}