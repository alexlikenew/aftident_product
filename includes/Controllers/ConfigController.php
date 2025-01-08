<?php

namespace Controllers;
use Interfaces\ControllerInterface;
use Models\Model;
use Models\Config;
use Traits\DbConnection;



class ConfigController implements ControllerInterface{


    public $data;
    private $model;


    public function __construct($table = 'config'){
        $this->model = new Config($table);

    }

    public function load(): ?array
    {
        $result = $this->model->load();

        foreach($result as $config){
            $this->data[$config['name']] = $config['value'];
        }

        return $this->data;
    }

    /*
    public function getOptionExtra(string $options){
        return $this->model->getOptionsExtra($options);
    }
*/
    public function getLang($all = false){
        return $this->model->getLang($all);

    }

    public function setLangActive($id, $active){
        return $this->model->setLangActive($id, $active);
    }

    public function getLangMain(){
        return $this->model->getLangMain();
    }

    public function getLangActive($id){
        return $this->model->getLangActive($id);
    }

    public function getOptionLang($name, $all = false){
        return $this->model->getOptionLang($name, $all);
    }

    public function getOptionLangAdmin($name){

        $result = $this->getOptionLang($name, true);

        if ($result) {
            $data = $this->model->getOptionLangDescription($result);

            return $data;
        }

        return false;
    }

    public function getOption(string $name): ?string{
        if($this->data[$name])
            return $this->data[$name];

        return $this->model->getOption($name);
    }

    public static function getAllLangs(){
        $config = new Config('config');
        return $config->getLang();
    }

    public static function getLangByName($name){
        $config = new Config('config');
        return $config->getLangByName($name);
    }

    public static function getOptionExtra($option){
        $config = new Config('config');
        return $config->getOptions($option);
    }

    public static function getOptionStatic($option, $table = null){
        if($table)
            $config = new Config($table);
        else
            $config = new Config('config');
        return $config->getOption($option);
    }



    /**
     * @inheritDoc
     */
    public function create(array $data): bool
    {
        $data['conf_name'] = make_label($data['conf_name']);
        $data['conf_description'] = prepareString($data['conf_description'], true);
        $id = $this->model->create($data);

        return $id ? true : false;
    }

    public function createLangOption(array $data): bool
    {
        $data['conf_name'] = make_label($data['conf_name']);
        //$data['conf_description'] = prepareString($data['conf_description'], true);
        $id = $this->model->createLangOption($data['conf_name']);

        if($id){
            foreach($data['conf_value'] as $langId => $value){
                $data['conf_description'][$langId] = prepareString($data['conf_description'][$langId], true);
                $value = prepareString($value, true);
                $this->model->createOptionLangDescription($id, $langId, $value, $data['conf_description'][$langId]);
            }
            return true;
        }
        return false;

    }

    public function read(): Model
    {
        // TODO: Implement read() method.
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }

    public function updateLang(array $data){
        return $this->model->updateLang($data);
    }

    public function createLang(array $data){
        return $this->model->createLang($data);
    }

    public function deleteLang($id){
        return $this->model->deleteLang($id);
    }

    public function updateOptionLang(array $data): bool{

        return $this->model->updateOptionLang($data);
    }

    public function delete(int $id = null): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): Model
    {
        // TODO: Implement get() method.
    }

    public static function loadLangById($id){
        $model = new Config('config');
        return $model->getLangById($id);
    }

    public function getLangById(int $id){
        return $this->model->getLangById($id);
    }

    public static function makeUniqueUrl($title, $table, $field, $lang, $id = 0, $isCategorized = false){

        $model = new Config('config');
        return $model->make_unique_url( url: $title, table: $table, field: $field, language_id: $lang, parent_id: $id, isCategorized: $isCategorized);
    }

    public function loadToEdit(){

        $result = $this->model->getAll();
        $config = array();
        foreach ($result as $row) {
            if (!preg_match('/^page_/', $row['name']) && !preg_match('/^op_page_/', $row['name'])) {
                $config[] = $row;
            }
        }

        return $config;
    }

    public function updateAll($data){
        if (is_array($data)) {
            foreach ($data['conf_name'] as $i => $name) {
                $this->model->update([
                    'name'  => $name,
                    'value' => $data['conf_value'][$i]
                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    public function loadLangConfig(){
        $options = $this->model->getLangConfig();

        foreach($options As $k=>$opt){
            $descs = $this->model->getOptionDescriptions($opt['id']);

            foreach($descs as $desc){
                $options[$k]['value'][$desc['language_id']] = $desc['value'];
                $options[$k]['description'][$desc['language_id']] = $desc['description'];

            }

        }
        return $options;
    }

    public function updateLangAll($data){
        foreach($data['conf_value'] as $optionId=>$values){
            foreach($values as $langId=>$value){
                if($this->model->checkLangDescExists($optionId, $langId))
                    $this->model->updateOptionLangDescription($optionId, $langId, $value);
                else
                    $this->model->createOptionLangDescription($optionId, $langId, $value);
            }
        }
        return true;
    }

    public function createOrUpdateContact(array $data){
        $contact = [
            'headquarters' => [
                'title'             => $data['headquarters_title'],
                'street'            => $data['headquarters_street'],
                'postcode'          => $data['headquarters_postcode'],
                'building_no'       => $data['headquarters_building_no'],
                'city'              => $data['headquarters_city'],
                'email'             => $data['headquarters_email'],
                'phone'             => $data['headquarters_phone'],
            ],
            'production' => [
                'title'             => $data['production_title'],
                'street'            => $data['production_street'],
                'postcode'          => $data['production_postcode'],
                'building_no'       => $data['production_building_no'],
                'city'              => $data['production_city'],
                'email'             => $data['production_email'],
                'phone'             => $data['production_phone'],
            ],
            'bank_data' => $data['bank_content']
        ];

        if($this->getOption('contact'))
            return $this->update([
                'name'  => 'contact',
                'value' => base64_encode(json_encode($contact))
            ]);
        else
            return $this->create([
                'conf_name'         => 'contact',
                'conf_description'  => 'Contact info',
                'conf_value'        => base64_encode(json_encode($contact))
            ]);
    }

    public function getContactInfo(){
        $data = json_decode(base64_decode($this->getOption('contact')), true);
        return $data;
    }
}