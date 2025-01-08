<?php

namespace Controllers\Inputs;

class FileInputController extends FormInputController
{
    public $type = FormInputController::TYPE_FILE;
    protected $config = [
        'multiple'      => false,
        'required'      => false,
    ];
    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }
}