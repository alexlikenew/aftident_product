<?php

namespace Models;

use Controllers\DbStatementController;

class Config extends Model
{
    protected $table;
    protected $tableLang;
    protected $tableOptionLang;
    protected $tableOptionLangDescription;
    protected $vars;


    public function __construct($table = 'config')
    {
        $this->table = DB_PREFIX . $table;
        $this->tableLang = DB_PREFIX . 'languages';
        $this->tableOptionLang = $this->table . '_lang';
        $this->tableOptionLangDescription = $this->table . '_lang_description';
        parent::__construct($table);
    }



    private function setTable($table = '') {
        if ($table == '') {
            $table = 'config';
        }
        $this->table = DB_PREFIX . $table;
        return true;
    }

    private function setDefaultTable() {
        $this->setTable();
    }

    public function getLang($all = false) {
        $q = "SELECT * FROM " . $this->tableLang . " ";
        if(!$all)
            $q .= "WHERE active='1' ORDER BY main DESC";

        return $this->query($q)->get_all_rows();
    }

    public function setLangActive($id, $active){
        $q = "UPDATE ". $this->tableLang . " SET active= ? WHERE id= ?  ";
        $params = [
            [DbStatementController::INTEGER, $active ? 1 : 0],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getLangMain() {
        $q = "SELECT * FROM " . $this->tableLang . " WHERE main='1'";
        $statement = $this->query($q);
        if ($lang = $statement->fetch_assoc()) {
            return $lang;
        }
        return false;
    }

    public function getLangActive(string $code):int
    {
        $q = "SELECT id FROM " . $this->tableLang . " WHERE code=?";
        $params = [
            [DbStatementController::STRING, $code]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result();
    }


    public function getLangById(int $id): ?array
    {
        $q = "SELECT * FROM " . $this->tableLang . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        $statement = $this->query($q, $params);
        if ($lang = $statement->fetch_assoc()) {
            return $lang;
        }
        return null;
    }

    public function getLangByName(string $name)
    {
        $q = "SELECT * FROM " . $this->tableLang . " WHERE name=? ";
        $params = [
            [DbStatementController::STRING, $name]
        ];
        $statement = $this->query($q, $params);
        if ($lang = $statement->fetch_assoc()) {
            return $lang;
        }

        return false;
    }

    public function load(): array
    {
        $q = "SELECT name, value FROM " . $this->table . " ORDER BY name ASC";

        return $this->query($q)->get_all_rows();

    }

    public function getAllDescription(string $name = '%', string  $table = ''): array
    {
        $table = empty($table) ? $table = $this->table : $table;
        $q = "SELECT name, description FROM " . $table . " WHERE name LIKE ? ORDER BY name";
        $params = array(
            array(dbStatementController::STRING, $name)
        );
        $statement = $this->query($q, $params);
        $items = array();
        while ($row = $statement->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function getAllDescriptionExtra(string $name = '%'):array
    {
        $this->setTable('config_extra');
        $descriptions = $this->LoadAllDescription($name);
        $this->setDefaultTable();
        return $descriptions;
    }

    public function getOption(string $name, string $table = ''): ?string
    {
        $table = empty($table) ? $table = $this->table : $table;
        $q = "SELECT value FROM " . $table . " WHERE name=? ";
        $params = [
            [dbStatementController::STRING, $name]
        ];

        return $this->query($q, $params)->get_result();

    }


    public function getOptionExtra(string $name): ?string
    {
        $this->setTable('config_extra');
        $option = $this->getOption($name);
        $this->setDefaultTable();
        return $option;
    }


    public function getOptions(string $options, string $table = ''): ?array
    {
        $table = empty($table) ? $table = $this->table : $table;
        $q = "SELECT name, value FROM " . $table . " ";
        $q .= "WHERE name IN (";
        $options = str_replace(' ', '', $options);
        $options = explode(',', $options);
        $params = [];
        foreach ($options as $key => $option) {
            if ($key > 0) {
                $q .= ", ";
            }
            $q .= "? ";
            $params[] = [dbStatementController::STRING, $option];
        }
        $q .= ") ";
        $statement = $this->query($q, $params);
        while ($rekord = $statement->fetch_assoc()) {
            $vars[$rekord['name']] = $rekord['value'];
        }
        return $vars;
    }

    public function getOptionsExtra(string $options): ?array
    {
        $this->setTable('config_extra');
        $option = $this->getOptions($options);

        $this->setDefaultTable();
        return $option;
    }

    public function getOptionWithDescription(string $name, string $table = ''): ?array
    {
        $table = empty($table) ? $table = $this->table : $table;
        $q = "SELECT description, value FROM " . $table . " WHERE name=? ";
        $params = [
            [dbStatementController::STRING, $name]
        ];
        $statement = $this->query($q, $params);
        $row = $statement->fetch_assoc();
        return mstripslashes($row);
    }

    public function getOptionWithDescriptionExtra(string $name): ?array
    {
        $this->setTable('config_extra');
        $option = $this->getOptionWithDescription($name);
        $this->setDefaultTable();
        return $option;
    }

    public function getToEdit(): ?array
    {
        $q = "SELECT name, value, description FROM " . $this->table . " ";
        if (!$_SESSION['user']['admin']) {
            $q .= "WHERE active=1 ";
        }
        $q .= "ORDER BY name ASC";
        $statement = $this->db->query($q);
        $config = [];
        while ($row = $statement->fetch_assoc()) {
            if (!preg_match('/^page_/', $row['name']) && !preg_match('/^op_page_/', $row['name'])) {
                $config[] = $row;
            }
        }
        return $config;
    }

    public function create(array $data):int {
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "value=?, ";
        $q .= "description=?, ";
        $q .= "active=1";

        $params = [
            [DbStatementController::STRING, $data['conf_name']],
            [DbStatementController::STRING, $data['conf_value']],
            [DbStatementController::STRING, $data['conf_description']]
        ];

        $this->saveEventInfo($data['conf_name'], '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function update(array $data, int $id = null): bool
    {
        $table = empty($table) ? $table = $this->table : $table;
        $q = "UPDATE " . $table . " SET ";
        $q.= "value=? ";
        $q.= "WHERE name=? ";

        $params = [
            [DbStatementController::STRING, $data['value']],
            [DbStatementController::STRING, $data['name']]
        ];
        $statement = $this->query($q, $params);
        $this->saveEventInfo($data['name'], '', 1, __METHOD__, $this->table);
        return $statement->is_success();
    }

    public function updateAll(array $dane): bool
    {
        if (is_array($dane)) {
            foreach ($dane['conf_name'] as $i => $name) {
                $q = "UPDATE " . $this->table . " SET value=? WHERE name=? LIMIT 1";
                $params = [
                    [dbStatementController::STRING, $dane['conf_value'][$i]],
                    [dbStatementController::STRING, $name]
                ];
                $this->query($q, $params);
            }
            $this->saveEventInfo('multiple update', '', 1, __METHOD__, $this->table);
            return true;
        } else {
            return false;
        }
    }

    public function updateExtra(string $name, string $value): bool
    {
        $this->setTable('config_extra');
        $resp = $this->update([
            'name'  => $name,
            'value' => $value
        ]);
        $this->setDefaultTable();
        return $resp;
    }

    public function updateOptionLang(array $data): bool{
        $result = $this->getOptionLang($data['name'], true);

        if ($result) {
            foreach ($data['value'] as $i => $v) {

                if ($this->checkLangDescExists($result['id'], $i)) {
                    $this->updateOptionLangDescription($result['id'], $i, $v);
                } else {
                    $this->createOptionLangDescription($result['id'], $i, $v);
                }
            }
            return true;
        }
        return false;
    }

    public function checkLangDescExists($id, $langId){
        $q = "SELECT id FROM " . $this->tableOptionLangDescription . " ";
        $q .= "WHERE parent_id=? AND language_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, $langId]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function updateOptionLangDescription($itemId, $langId, $description){
        $q = "UPDATE " . $this->tableOptionLangDescription . " SET ";
        $q .= "value=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params =[
            [DbStatementController::STRING, $description],
            [DbStatementController::INTEGER, $itemId],
            [DbStatementController::INTEGER, $langId]
        ];
        $this->saveEventInfo($description, '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function createOptionLangDescription($itemId, $langId, $description, $desc = false){
        $q = "INSERT INTO " . $this->tableOptionLangDescription . " SET ";
        $q .= "value=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $description],
            [DbStatementController::INTEGER, $itemId],
            [DbStatementController::INTEGER, $langId]
        ];

        if($desc){
            $q .= ", description = ? ";
            $params[] = [DbStatementController::STRING, $desc];
        }

        $this->saveEventInfo($description, '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function delete(int $id, $background = false):bool
    {
        $q = "DELETE FROM " . $this->table . " WHERE name=? ";
        $params = [
            [DbStatementController::STRING, $id]
        ];
        $statement = $this->query($q, $params);
        $this->saveEventInfo($id, '', 2, __METHOD__, $this->table);
        if($statement->is_success())
            return true;

        return false;
    }

    public function getOptionLang(string $name, $all = false)
    {
        $q = "SELECT a.*, d.value FROM " . $this->tableOptionLang . " a ";
        $q .= "LEFT JOIN " . $this->tableOptionLangDescription . " d ON a.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND a.name=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::STRING, $name]
        ];

        $statement = $this->query($q, $params);
        if ($row = $statement->fetch_assoc()) {
            if($all)
                return $row;

            return $row['value'];
        }

        return false;
    }

    public function getOptionLangDescription($data){

        $q = "SELECT language_id, value FROM " . $this->tableOptionLangDescription . " ";
        $q .= "WHERE parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['id']]
        ];

        $statement = $this->query($q, $params);
        $option = array();
        while ($row = $statement->fetch_assoc()) {
            $option[$row['language_id']] = $row['value'];
        }

        return $option;
    }

    public function getOptionLangAdmin(string $name): ?array
    {
        $q = "SELECT * FROM " . $this->tableOptionLang . " ";
        $q .= "WHERE name=? ";

        $params = [
            [DbStatementController::STRING, $name]
        ];
        $statement = $this->query($q, $params);
        if ($row = $statement->fetch_assoc()) {
            $q = "SELECT language_id, value FROM " . $this->tableOptionLangDescription . " ";
            $q .= "WHERE parent_id='" . $row['id'] . "' ";

            $statement = $this->query($q);
            $option = array();
            while ($row2 = $statement->fetch_assoc()) {
                $option[$row2['language_id']] = $row2['value'];
            }

            return $option;
        }

        return false;
    }

    public function make_unique_url(string $url, string $table, string $field, $parent_id=null, int $language_id = 1, bool $exclude = false, string $exclude_field = "parent_id", string $exclude_type = "i", bool $isCategorized = false): ?string
    {

        if($isCategorized){

            $isCategory = false;
            if(preg_match('/categories_description/', $table)){
                $categoryTable = $table;
                $table = str_replace('_categories_description', '_description', $table);
                $isCategory = true;
            }
            else{
                $categoryTable = str_replace('_description', '_categories_description', $table);
            }


            $q = "SELECT id FROM ". $table ." ";
            $q .= "WHERE language_id = ? ";
            $q .= "AND ".$field." = ? ";
            if(!$isCategory)
                $q .= "AND parent_id != ? ";
            $q .= " UNION SELECT id FROM ".$categoryTable." ";
            $q .= "WHERE language_id = ? ";
            $q .= "AND ".$field." = ? ";
            if($isCategory)
                $q .= "AND parent_id != ? ";

            $params = [
                [DbStatementController::INTEGER, $language_id],
                [DbStatementController::STRING, $url]
            ];
            if(!$isCategory)
                $params[] = [DbStatementController::INTEGER, $parent_id];
            $params[] = [DbStatementController::INTEGER, $language_id];
            $params[] = [DbStatementController::STRING, $url];
            if($isCategory)
                $params[] = [DbStatementController::INTEGER, $parent_id];

            /*
            $q = "SELECT count(*) as ile FROM " . $table . " ";
            $q .= "WHERE " . $field . " LIKE ? ";
            $q .= "AND language_id=? AND parent_id != ? ";
            $q .= "UNION SELECT count(*) AS cat_ile FROM " . $categoryTable." ";
            $q .= "WHERE " . $field . " LIKE ? ";
            $q .= "AND language_id=? ";


            $params = [
                [dbStatementController::STRING, $url],
                [dbStatementController::INTEGER, $language_id],
                [dbStatementController::INTEGER, $parent_id],
                [dbStatementController::STRING, $url],
                [dbStatementController::INTEGER, $language_id],
            ];
            */

            $statement = $this->query($q, $params);
            $powtorka = 0;
            $row = $statement->fetch_assoc();

            if ($row['id'] > 0 ) {
                $powtorka = 1;

                while ($powtorka == 1) {
                    $url = $url . "-".rand(1,10000);

                    $q = "SELECT id FROM ". $table ." ";
                    $q .= "WHERE language_id = ? ";
                    $q .= "AND ".$field." = ? ";
                    if(!$isCategory)
                        $q .= "AND parent_id != ? ";
                    $q .= " UNION SELECT id FROM ".$categoryTable." ";
                    $q .= "WHERE language_id = ? ";
                    $q .= "AND ".$field." = ? ";
                    if($isCategory)
                        $q .= "AND parent_id != ? ";

                    $params = [
                        [DbStatementController::INTEGER, $language_id],
                        [DbStatementController::STRING, $url]
                    ];
                    if(!$isCategory)
                        $params[] = [DbStatementController::INTEGER, $parent_id];
                    $params[] = [DbStatementController::INTEGER, $language_id];
                    $params[] = [DbStatementController::STRING, $url];
                    if($isCategory)
                        $params[] = [DbStatementController::INTEGER, $parent_id];


                    $statement = $this->query($q, $params);
                    $row = $statement->fetch_assoc();
                    if ($row['ile'] == 0 && $row['cat_ile'] == 0) {
                        $powtorka = 0;
                    }
                }
            }
        }
        else{

            $q = "SELECT count(*) as ile FROM " . $table . " ";
            $q .= "WHERE " . $field . " LIKE ? ";
            $q .= "AND language_id=? AND parent_id != ?";

            $params = [
                [dbStatementController::STRING, $url],
                [dbStatementController::INTEGER, $language_id],
                [dbStatementController::INTEGER, $parent_id],
            ];

            if ($exclude) {
                $q .= " AND " . $exclude_field . "<>? ";
                $params[] = [$exclude_type, $exclude];
            }

            $statement = $this->query($q, $params);
            $powtorka = 0;
            $row = $statement->fetch_assoc();
            if ($row['ile'] > 0) {
                $powtorka = 1;

                while ($powtorka == 1) {
#                $url = $url . "-";
                    $url = $url . "-".rand(1,10000);
                    $q = "SELECT count(*) as ile FROM " . $table . " ";
                    $q .= "WHERE " . $field . " LIKE ? ";
                    $q .= "AND language_id=? ";
                    $params = [
                        [dbStatementController::STRING, $url],
                        [dbStatementController::INTEGER, $language_id]
                    ];
                    if ($exclude) {
                        $q.= " AND " . $exclude_field . "<>? ";
                        $params[] = [$exclude_type, $exclude];
                    }
                    $statement = $this->query($q, $params);
                    $row = $statement->fetch_assoc();
                    if ($row['ile'] == 0) {
                        $powtorka = 0;
                    }
                }
            }
        }

        return $url;
    }

    public function getAll($onlyActive = false, $toSkip = false){
        $q = "SELECT name, value, description FROM " . $this->table . " ";
        if (!$_SESSION['user']['admin']) {
            $q .= "WHERE active=1 ";
        }
        $q .= "ORDER BY name ASC";

        return $this->query($q)->get_all_rows();
    }

    public function createLang($data){
        $q = "INSERT INTO ".$this->tableLang. " SET ";
        $q .= "name=?, ";
        $q .= "code=?, ";
        $q .= "directory=?, ";
        $q .= "domain=?, ";
        $q .= "main=?, ";
        $q .= "active=?, ";
        $q .= "gen_title=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['code']],
            [DbStatementController::STRING, $data['code']],
            [DbStatementController::STRING, $data['domain']],
            [DbStatementController::INTEGER, $data['main'] ?? 0],
            [DbStatementController::INTEGER, $data['active'] ?? 0],
            [DbStatementController::INTEGER, $data['gen_title'] ?? 0],

        ];

        return $this->query($q, $params)->insert_id();
    }

    public function deleteLang($id){
        $q = "DELETE FROM ".$this->tableLang." WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateLang($data){
        $q = "UPDATE ".$this->tableLang. " SET ";
        $q .= "name=?, ";
        $q .= "code=?, ";
        $q .= "domain=?, ";
        $q .= "main=?, ";
        $q .= "active=?, ";
        $q .= "gen_title=? ";
        $q .= "WHERE id=?";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['code']],
            [DbStatementController::STRING, $data['domain']],
            [DbStatementController::INTEGER, $data['main'] ?? 0],
            [DbStatementController::INTEGER, $data['active'] ?? 0],
            [DbStatementController::INTEGER, $data['gen_title'] ?? 0],
            [DbStatementController::INTEGER, $data['id']]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getLangConfig(){
        $q = "SELECT * FROM ".$this->tableOptionLang;
        return $this->query($q)->get_all_rows();
    }

    public function getOptionDescriptions($id, $langId = null){
        $q = "SELECt * FROM ". $this->tableOptionLangDescription . " WHERE parent_id =? ";
        if($langId)
            $q .= "AND language_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        if($langId)
            $params[] = [DbStatementController::INTEGER, $langId];

        return $this->query($q, $params)->get_all_rows();
    }

    public function createLangOption($name){
        $q = "INSERT INTO ".$this->tableOptionLang." SET name = ? ";
        $params = [
            [DbStatementController::STRING, $name]
        ];

        return $this->query($q, $params)->insert_id();
    }

}
