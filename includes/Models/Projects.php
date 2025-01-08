<?php

namespace Models;

use Controllers\DbStatementController;

class Projects extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='projects'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }


}