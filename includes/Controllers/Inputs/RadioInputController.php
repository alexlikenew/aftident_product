<?php

namespace Controllers\Inputs;

class RadioInputController extends FormInputController
{
    public $type = FormInputController::TYPE_RADIO;

    protected $withContent = true;

    protected $config = [
        'required'      => [
            'by_lang'       => false,
            'is_bool'       => true,
        ],
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}