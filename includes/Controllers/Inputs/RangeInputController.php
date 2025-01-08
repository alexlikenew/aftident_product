<?php

namespace Controllers\Inputs;

class RangeInputController extends FormInputController
{
    public $type = FormInputController::TYPE_RANGE;

    protected $config = [
        'min'           => false,
        'max'           => false,
        'step'          => false,
        'autocomplete'  => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}