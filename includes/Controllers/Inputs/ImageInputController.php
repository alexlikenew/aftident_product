<?php

namespace Controllers\Inputs;

class ImageInputController extends FormInputController
{
    public $type = FormInputController::TYPE_IMAGE;

    protected $config = [
        'step'   => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}