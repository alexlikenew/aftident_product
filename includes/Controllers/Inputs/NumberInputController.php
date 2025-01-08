<?php

namespace Controllers\Inputs;

class NumberInputController extends FormInputController
{
    public $type = FormInputController::TYPE_NUMBER;

    protected $config = [
        'min'          => false,
        'max'          => false,
        'required'     => false,
        'step'         => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}