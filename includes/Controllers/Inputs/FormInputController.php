<?php

namespace Controllers\Inputs;
use Interfaces\FormInputInterface;
use Models\FormInput;

abstract class FormInputController implements FormInputInterface
{
    public const TYPE_TEXT = 1;
    public const TYPE_RADIO = 2;
    public const TYPE_SELECT = 3;
    public const TYPE_TEXTAREA = 4;
    public const TYPE_FILE = 5;
    public const TYPE_CHECKBOX = 6;
    public const TYPE_COLOR = 7;
    public const TYPE_DATE = 8;
    public const TYPE_DATE_TIME = 9;
    public const TYPE_DATE_TIME_LOCAL = 10;
    public const TYPE_EMAIL = 11;
    public const TYPE_MONTH = 12;
    public const TYPE_NUMBER = 13;
    public const TYPE_PASSWORD = 14;
    public const TYPE_RANGE = 15;
    public const TYPE_SEARCH = 16;
    public const TYPE_TEL = 17;
    public const TYPE_TIME_AND_WEEK = 18;
    public const TYPE_URL = 19;
    public const TYPE_DATEPICKER = 20;
    public const TYPE_IMAGE = 21;

    protected $defaultConfig = [
        'class'     => [
            'by_lang'   => false,
            'is_bool'   => false,
        ],
        'id'        => [
            'by_lang'   => false,
            'is_bool'   => false,
        ],
        'value'     => [
            'by_lang'   => true,
            'is_bool'   => false,
        ],
        'maxlength' => [
            'by_lang'   => false,
            'is_bool'   => false,
        ],
        'readonly'  => [
            'by_lang'   => false,
            'is_bool'   => true,
        ],
        'disabled'  => [
            'by_lang'   => false,
            'is_bool'   => true,
        ],
        'autofocus' => [
            'by_lang'   => false,
            'is_bool'   => true,
        ],
    ];

    protected $type;
    protected $name;
    protected $titles;

    protected $activeLangs;

    protected $dbId;

    protected $form_id;

    protected $withContent = false;
    protected $content = [];

    public function __construct($type){
        $this->type = $type;
        $this->model = new FormInput('form_input');
    }

    public function create(array $data)
    {
        $id = $this->model->create([
            'parent_id' => $this->getFormId(),
            'type'      => $this->getType(),
            'name'      => $this->getName(),
            'order'     => $this->model->getMaxOrder($this->getFormId()) + 1
            ]);

        if($id){
            foreach($this->getTitles() as $langId=>$title){
                $this->model->createDescription([
                    'parent_id' => $id,
                    'title'     => $title,
                    'active'    => $this->getActiveLangs()[$langId] ?? 0
                ], $langId);
            }
        }
        return $id;
    }

    public function update(array $data):bool{
        $config = [];
        $descConfig = [];

        foreach($this->getConfig() as $key=>$val){
             if($val['by_lang']){
                foreach($val['value'] as $langId=>$value)
                    $descConfig[$langId][$key] = [
                        'by_lang'   => true,
                        'is_bool'   => $val['is_bool'],
                        'value'     => $value,
                    ];
             }
             else
                 $config[$key] = $val;
        }

        $result = $this->model->update([
            'name'      => $this->getName(),
            'config'    => json_encode($config),
        ], $this->getDbId());

        foreach($this->getTitles() as $langId=>$title){
            $desc = $this->model->loadDescriptionById($this->getDbId(), $langId);

            if($desc)
                $this->model->updateDescription([
                    'parent_id'        => $this->getDbId(),
                    'config'           => json_encode($descConfig[$langId]),
                    'content'          => json_encode($this->getContent()[$langId]),
                    'title'            => $title,
                    'active'           => $this->getActiveLangs()[$langId] ?? 0,
                ], $langId);
            else
                $this->model->createDescription([
                    'parent_id'        => $this->getDbId(),
                    'config'           => json_encode($descConfig[$langId]),
                    'title'            => $title,
                    'content'          => json_encode($this->getContent()[$langId]),
                    'active'           => $this->getActiveLangs()[$langId] ?? 0,
                ], $langId);
        }

        return $result;
    }

    public function getType(){
        return $this->type;
    }

    public function read()
    {
        // TODO: Implement read() method.
    }
/*
    public function update(array $data): bool
    {
        // TODO: Implement update() method.
        return true;
    }
*/
    public function delete(int $id = null)
    {
        $this->model->delete($id);
    }

    public function setFormId($id){
        $this->form_id = $id;
        return $this;
    }

    public function getFormId(){
        return $this->form_id;
    }

    public function setTitles($data){
        $this->titles = $data;
        return $this;
    }

    public function getTitles(){
        return $this->titles;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setActiveLangs($data){
        $this->activeLangs = $data;
        return $this;
    }

    public function getActiveLangs(){
        return $this->activeLangs;
    }

    public function hasContent(){
        return $this->withContent;
    }

    /**
     * @return \string[][]
     */
    public static function getAllInputTypes(){
        return [
            1 => [
                'name'  => 'Pole tekstowe',
                'image' => '/upload/blocks/types/8/zdjecie-675.jpg',
            ],
            2 => [
                'name'  => 'Radio',
                'image' => '/upload/blocks/types/8/zdjecie-675.jpg',
            ],
            3 => [
                'name'  => 'Select',
                'image' => '/upload/blocks/types/8/zdjecie-675.jpg',
            ],
        ];
    }

    public static function getByType($type){
        switch($type){
            case 1:
                return new TextInputController();
                break;
            case 2:
                return new RadioInputController();
                break;
            case 3:
                return new TextInputController();
                break;
            case 4:
                return new TextInputController();
                break;
            case 5:
                return new TextInputController();
                break;
            case 6:
                return new TextInputController();
                break;
            case 7:
                return new TextInputController();
                break;
            case 8:
                return new TextInputController();
                break;
            case 9:
                return new TextInputController();
                break;
            case 10:
                return new TextInputController();
                break;
            case 11:
                return new TextInputController();
                break;
            case 12:
                return new TextInputController();
                break;
            case 13:
                return new TextInputController();
                break;
            case 14:
                return new TextInputController();
                break;
            case 15:
                return new TextInputController();
                break;
            case 16:
                return new TextInputController();
                break;
            case 17:
                return new TextInputController();
                break;
            case 18:
                return new TextInputController();
                break;
            case 19:
                return new TextInputController();
                break;
        }
    }

    public function setDataById($id){
        $data = $this->model->getById($id);
        $this->setContent(json_decode($data['lang_content'],1));
        $this->setDbId($id);
        $this->setFormId($data['parent_id']);
        $this->setName($data['name']);
        $this->setTitles([
            _ID => $data['title']
        ]);

        $this->setConfig(json_decode($data['config'], true), json_decode($data['lang_config'], true));
    }

    public function setDbId($id){
        $this->dbId = $id;
        return $this;
    }

    public function getDbId(){
        return $this->dbId;
    }

    public function setConfig($data, $langData){

        foreach($this->config as $name=>$val){
            if($val['by_lang'] && isset($langData[$name])){
                $this->config[$name]['value'] = $langData[$name]['value'];
            }
            elseif(isset($data[$name]))
                $this->config[$name]['value'] = $data[$name]['value'];
        }
        return $this;
    }

    public function getClass(){
        if(isset($this->config['class']['value']) && $this->config['class']['value'])
            return $this->config['class']['value'];
        else
            return false;
    }

    public function getId(){
        if(isset($this->config['id']['value']) && $this->config['id']['value'] )
            return $this->config['id']['value'];
        else
            return false;
    }

    public function getDefaultValue(){
        if(isset($this->config['value']['value']) && $this->config['value']['value'])
            return $this->config['value']['value'];
        else
            return false;
    }

    public function isReadonly(){
        if(isset($this->config['readonly']['value']) && $this->config['readonly']['value'])
            return true;
        else
            return false;
    }

    public function isDisabeld(){
        if(isset($this->config['disabeld']['value']) && $this->config['disabeld']['value'])
            return true;
        else
            return false;
    }

    public function getMaxlength(){
        if(isset($this->config['maxlength']['value']) && $this->config['maxlength']['value'])
            return $this->config['maxlength']['value'];
        else
            return false;
    }

    public function isAutofocus(){
        if(isset($this->config['autofocus']['value']) && $this->config['autofocus']['value'])
            return true;
        else
            return false;
    }

    public function getSize(){
        if(isset($this->config['size']['value']) && $this->config['size']['value'])
            return $this->config['size']['value'];
        else
            return false;
    }

    public function getPattern(){
        if(isset($this->config['pattern']['value']) && $this->config['pattern']['value'])
            return $this->config['pattern']['value'];
        else
            return false;
    }

    public function getPlaceholder(){
        if(isset($this->config['placeholder']) && $this->config['placeholder']['value'])
            return $this->config['placeholder']['value'];
        else
            return false;
    }

    public function isRequired(){
        if(isset($this->config['required']['value']) && $this->config['required']['value'])
            return true;
        else
            return false;
    }

    public function isAutocomplete(){
        if(isset($this->config['autocomplete']['value']) && $this->config['autocomplete']['value'])
            return true;
        else
            return false;
    }

    public function getConfig(){
        return $this->config;
    }

    public function updateConfig($data){
        foreach($this->getConfig() as $name=>$val){
            if(isset($data[$name]))
                $this->config[$name]['value'] = $data[$name];
        }
        return $this;
    }

    public function updateContent($data){
        $options = [];
        foreach($data as $langId=>$opt){
            foreach($opt as $option)
                if($option['title'])
                    $options[$langId][] = $option;
        }

        $this->setContent($options);
    }

    public function setContent($data){
        $this->content = $data;
        return $this;
    }

    public function getContent(){
        return $this->content;
    }

    public function getTitle(){
        return $this->getTitles()[_ID];
    }
}
