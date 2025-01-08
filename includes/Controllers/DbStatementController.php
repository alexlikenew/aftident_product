<?php

namespace Controllers;
use Traits\Template;
use Traits\Request;


class DbStatementController{
    use Template,Request;

    private $link;
    private $result;
    private $result_index;
    private $result_meta;
    private $stmt;
    private $query;
    private $types;
    private $params;
    private $stop;
    private $success;
    private $affected_rows;
    private $num_rows;
    private $prepared;
    private $logTable = 'database_error_log';

    const INTEGER = 'i';
    const DOUBLE = 'd';
    const STRING = 's';
    const BLOB = 'b';

    /**
     *
     * @param string $query
     * @param array $params
     * @param mysqli $link
     * @param type $stop
     */
    public function __construct($query, $params, $link, $stop)
    {
        $this->link = $link;
        $this->result = false;
        $this->result_index = 0;
        $this->stmt = null;
        $this->query = $query;
        $this->stop = $stop;
        $this->success = false;
        $this->prepared = false;
        $this->affected_rows = 0;
        if (is_array($params)) {
            $this->types = '';
            $this->params = array();
            foreach ($params as $param) {
                $this->types .= $param[0];
                $this->params[] = $param[1];
            }
        } else {
            $this->types = false;
            $this->params = false;
        }
    }

    public function getLink(){
        return $this->link;
    }

    public function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    public function createResult() {
        $result = array();
        $row = array();
        $params = array();
        $this->result_meta = $this->stmt->result_metadata();
        if ($this->result_meta) {
            while ($field = $this->result_meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }

            call_user_func_array(array($this->stmt, 'bind_result'), $params);

            while ($this->stmt->fetch()) {
                foreach ($row as $key => $val) {
                    $c[$key] = $val;
                }
                $result[] = $c;
            }
        }
        return $result;
    }

    public function execute() {
        if ($this->params) {
            try{
                $this->stmt = $this->link->prepare($this->query);
            }
            catch(\Exception $e){
                $this->errorLog($e->getCode(), $e->getMessage());
                $this->setError('Something went wrong, please contact administrator', true);
                die();
            }

            if ($this->stmt) {
                call_user_func_array(array(&$this->stmt, 'bind_param'), $this->refValues(array_merge(array($this->types), $this->params)));
                try{
                    $this->success = $this->stmt->execute();
                }
                catch(\Exception $e){
                    $this->errorLog($e->getCode(), $e->getMessage());
                    $this->setError('Something went wrong, please contact administrator', true);
                    die();
                }


                $this->affected_rows = $this->getLink()->affected_rows;
                if ($this->stop) {
                    dump($this->stmt);
                }
                if (!$this->success) {
                    $this->errorLog($this->stmt->errno, $this->stmt->error);
                    $this->setError('Something went wrong, please contact administrator', true);
                    die();
                }
                $this->prepared = true;
                $this->stmt->store_result();
                $this->result = $this->createResult();
            } else {
                $this->errorLog($this->link->errno, $this->link->error);
                $this->setError('Something went wrong, please contact administrator', true);
                die();
            }
        } else {
            try{
                $this->result = $this->link->query($this->query);
                $this->affected_rows = $this->getLink()->affected_rows;
            }
            catch(\Exception $e){
                $this->errorLog($e->getCode(), $e->getMessage());
                $this->setError('Something went wrong, please contact administrator', true);
                die();
            }

            if (!$this->result) {
                $this->errorLog($this->link->errno, $this->link->error);
                $this->setError('Something went wrong, please contact administrator', true);
                die();
            } else {
                $this->success = true;
            }
        }
        return $this;
    }

    public function errorLog($errorId, $error){
        $q = "INSERT INTO ". $this->logTable ." ";
        $q .= "SET query=?, ";
        $q .= "error_id=?, ";
        $q .= "error=?";

        $params=[
            [self::STRING, $this->query],
            [self::INTEGER, $errorId],
            [self::STRING, $error]
        ];

        $this->setParams($params);


        $this->stmt = $this->link->prepare($q);

        if ($this->stmt) {
            call_user_func_array(array(&$this->stmt, 'bind_param'), $this->refValues(array_merge(array($this->types), $this->params)));
            $this->success = $this->stmt->execute();
        }
    }

    private function setParams(array $params)
    {
        if (is_array($params)) {
            $this->types = '';
            $this->params = array();
            foreach ($params as $param) {
                $this->types .= $param[0];
                $this->params[] = $param[1];
            }
        } else {
            $this->types = false;
            $this->params = false;
        }
    }

    public function is_success() {
        return $this->success;
    }

    public function fetch_assoc() {
        if ($this->prepared) {
            if (isset($this->result[$this->result_index])) {
                $result = $this->result[$this->result_index];
                $this->result_index++;
                return $result;
            } else {
                return false;
            }
        } else {
            if ($this->result) {
                return $this->result->fetch_assoc();
            }
            return false;
        }
    }

    public function fetch_row() {
        if ($this->prepared) {
            if (isset($this->result[$this->result_index])) {
                $this->result_index++;
                $fields = $this->result[$this->result_index];
                $i = 0;
                $row = array();
                foreach ($fields as $field) {
                    $row[$i] = $field;
                    $i++;
                }
                return $row;
            } else {
                return false;
            }
        } else {
            if ($this->result) {
                return $this->result->fetch_row();
            }
            return false;
        }
    }

    public function get_all_rows() {
        if ($this->prepared) {
            if (count($this->result) > 0) {
                $items = array();
                while ($row = $this->fetch_assoc()) {
                    $items[] = $row;
                }
                return $items;
            }
            return false;
        } else {
            if ($this->result) {
                $items = array();
                while ($row = $this->result->fetch_assoc()) {
                    $items[] = $row;
                }
                return $items;
            }
            return false;
        }
    }

    public function get_result($r = 0, $c = 0) {
        if ($this->prepared) {
            if (count($this->result) > 0 && $r < count($this->result)) {
                $row = $this->result[$r];
                $value = false;
                $i = 0;
                foreach ($row as $field) {
                    if ($i == $c) {
                        $value = $field;
                    }
                    $i++;
                }
                return $value;
            }
            return false;
        } else {
            if ($this->result) {
                if ($this->result->num_rows == 0) {
                    return false;
                }
                $this->result->data_seek($r);
                $row = $this->result->fetch_array();
                $value = false;
                if (isset($row[$c])) {
                    $value = $row[$c];
                }
                return $value;
            }
            return false;
        }
    }

    public function insert_id() {
        return $this->link->insert_id;
    }

    public function affected_rows() {
        return $this->affected_rows;
    }

    public function num_rows() {
        if ($this->prepared) {
            return count($this->result);
        } else {
            return $this->result->num_rows;
        }
    }

    public function num_fields() {
        return $this->link->field_count;
    }

    public function fetch_field($number) {
        if ($this->prepared) {
            return $this->result_meta->fetch_field_direct($number);
        } else {
            return $this->result->fetch_field_direct($number);
        }
    }

}

