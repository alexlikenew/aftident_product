<?php

namespace Controllers\Inputs;

class ColorInputController extends FormInputController
{
    public $type = FormInputController::TYPE_COLOR;

    protected $config = [
        'autocomplete'   => false,
        ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}