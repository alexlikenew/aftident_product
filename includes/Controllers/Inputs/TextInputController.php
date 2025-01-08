<?php

namespace Controllers\Inputs;

class TextInputController extends FormInputController
{
    public $type = FormInputController::TYPE_TEXT;

    protected $config = [
        'size'          => [
            'by_lang'       => false,
            'is_bool'       => false,
        ],
        'pattern'       => [
            'by_lang'       => true,
            'is_bool'       => false,
        ],
        'placeholder'   => [
            'by_lang'       => true,
            'is_bool'       => false,
        ],
        'required'      => [
            'by_lang'       => false,
            'is_bool'       => true,
        ],
        'autocomplete'  => [
            'by_lang'       => false,
            'is_bool'       => true,
        ],
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}