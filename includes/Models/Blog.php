<?php

namespace Models;

use Controllers\DbStatementController;

class Blog extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table = 'blog')
    {
        $this->table = DB_PREFIX.$table;
        $this->tableDescription = $this->table.'_description';

        parent::__construct($table);
    }

    public function getLastItems($limit, int $toSkip = 0, ?int $from = null)
    {
        $params = [];

        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM ".$this->table." p ";
        $q .= "LEFT JOIN ".$this->tableDescription." d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $params[] = [DbStatementController::INTEGER, _ID];
        $q .= "AND p.active=1 AND d.active = 1 ";

        if ($toSkip > 0) {
            $q .= "AND p.id != ? ";
            $params[] = [DbStatementController::INTEGER, $toSkip];
        }

        $q .= "ORDER BY p.date_add DESC, p.id DESC ";

        if (null !== $from) {
            $q .= "LIMIT ?,? ";
            $params[] = [DbStatementController::INTEGER, $from];
        } else {
            $q .= "LIMIT ? ";
        }
        $params[] = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);

        return $statement->get_all_rows();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false)
    {
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.active, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";
        if($category_path)
            $q .= "AND p.category_id IN (SELECT id FROM ".$this->tableCategory." WHERE path_id LIKE CONCAT(?, '%')) ";
        elseif($category_id)
            $q .= "AND p.category_id = ? ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];

        if ($category_path) {
            $params[] = [
                DbStatementController::STRING,
                $category_path,
            ];
        } elseif ($category_id) {
            $params[] = [DbStatementController::INTEGER, $category_id];
        }

        $params[] = [DbStatementController::INTEGER, $start];
        $params[]  = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);

        return $statement->get_all_rows();
    }

}
