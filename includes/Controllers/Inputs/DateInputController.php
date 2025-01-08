<?php

namespace Controllers\Inputs;

class DateInputController
{
    public $type = FormInputController::TYPE_DATE;

    protected $config = [
        'min'          => false,
        'max'          => false,
        'pattern'       => false,
        'step'         => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}