<?php

namespace Models;
use Controllers\DbStatementController;

class News extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='news'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

}