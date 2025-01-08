<?php

namespace Models;

use Controllers\DbStatementController;

class Csr extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='csr'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }


}