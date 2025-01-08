<?php


namespace Models;


class Producer extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='producers'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }


}