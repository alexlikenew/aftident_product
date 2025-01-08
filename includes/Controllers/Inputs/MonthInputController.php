<?php

namespace Controllers\Inputs;

class MonthInputController extends FormInputController
{
    public $type = FormInputController::TYPE_MONTH;

    protected $config = [
        'min'          => false,
        'max'          => false,
        'step'         => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}