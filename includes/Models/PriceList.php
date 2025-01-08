<?php

namespace Models;

use Controllers\DbStatementController;

class PriceList extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='price_list'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function create($data):int{
        $q = "INSERT INTO ".$this->table." SET ";
        $q .= "type_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $data['type_id']]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();

    }
}