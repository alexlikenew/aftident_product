<?php

namespace Models;

use Controllers\DbStatementController;

class Menu extends Model
{
    protected $table;
    protected $tableDescription;
    protected $modules;
    protected $pages;

    const TYPE_URL = 0;
    const TYPE_PAGE = 1;
    const TYPE_MODULE = 2;
    const TYPE_NO_LINK = 3;
    const TYPE_CATEGORY = 4;
    const TYPE_CATALOG_CATEGORY = 6;
    const TYPE_OFFER = 5;
    const GROUP_TOP = 0;
    const GROUP_LEFT = 1;
    const GROUP_BOTTOM = 2;

    public function __construct($table = 'menu'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription  = $this->table.  '_description';
        $this->modules = new Module();
        $this->pages = new Pages();
        parent::__construct($table);
    }

    public function getClassName() {
        return "Menu";
    }

    public function getTableDescription() {
        return $this->tableDescription;
    }

   public  function createMenuItem($item) {

        $item['order'] = $this->getMaxOrder($item['pid'], $item['group']) + 1;

        $blank = isset($item['blank']) ? $item['blank'] : 0;
        $nofollow = isset($item['nofollow']) ? $item['nofollow'] : 0;
        $has_submenu = isset($item['has_submenu']) ? $item['has_submenu'] : 0;
        $submenu_type = isset($item['submenu_type']) ? $item['submenu_type'] : 0;

        if($item['submenu_type'] == 0) {
            $item['submenu_source'] = 0;
        }

        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "parent_id=?, ";
        $q .= "type=?, ";
        $q .= "`group`=?, ";
        $q .= "blank=?, ";
        $q .= "nofollow=?, ";
        $q .= "has_submenu=?, ";
        $q .= "submenu_type=?, ";
        $q .= "submenu_source=?, ";
        $q .= "`order`=? ";
        $params = [
            [DbStatementController::INTEGER, $item['pid']],
            [DbStatementController::INTEGER, $item['type']],
            [DbStatementController::INTEGER, $item['group']],
            [DbStatementController::INTEGER, $blank],
            [DbStatementController::INTEGER, $nofollow],
            [DbStatementController::INTEGER, $has_submenu],
            [DbStatementController::INTEGER, $submenu_type],
            [DbStatementController::INTEGER, $item['submenu_source']],
            [DbStatementController::INTEGER, $item['order']]
        ];

        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            $this->article_id = $statement->insert_id();

            foreach ($item['name'] as $i => $name) {
                $item['name'][$i] = prepareString($name, true);
                $lang_active = isset($item['lang_active'][$i]) ? 1 : 0;

                $q = "INSERT INTO " . $this->tableDescription . " SET ";
                $q .= "name=?, ";
                $q .= "url=?, ";
                $q .= "active=?, ";
                $q .= "parent_id=?, ";
                $q .= "language_id=?, ";
                $q .= "target_id=? ";
                $params = [
                    [DbStatementController::STRING, $item['name'][$i]],
                    [DbStatementController::STRING, $item['url'][$i]],
                    [DbStatementController::INTEGER, $lang_active],
                    [DbStatementController::INTEGER, $this->article_id],
                    [DbStatementController::INTEGER, $i],
                ];

                if($item['type'] == self::TYPE_CATEGORY)
                    $params[] = [DbStatementController::INTEGER, $item['category_id']];
                else
                    $params[] = [DbStatementController::INTEGER, $item['target_id'][$i]];

                $this->query($q, $params);
            }
            $this->saveEventInfo(json_encode($item), '', 0, __METHOD__, $this->table);
            return true;
        } else {

            return false;
        }
    }

    function update(array $item, int $id = null):bool {

        $blank = isset($item['blank']) ? $item['blank'] : 0;
        $nofollow = isset($item['nofollow']) ? $item['nofollow'] : 0;
        $has_submenu = isset($item['has_submenu']) ? $item['has_submenu'] : 0;
        if($item['submenu_type'] == 0) {
            $item['submenu_source'] = 0;
        }

        $q = "UPDATE " . $this->table . " SET ";
        $q .= "parent_id=?, ";
        $q .= "type=?, ";
        $q .= "`group`=?, ";
        $q .= "blank=?, ";
        $q .= "nofollow=?, ";
        $q .= "has_submenu=?, ";
        $q .= "submenu_type=?, ";
        $q .= "submenu_source=? ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $item['pid']],
            [DbStatementController::INTEGER, $item['type']],
            [DbStatementController::INTEGER, $item['group']],
            [DbStatementController::INTEGER, $blank],
            [DbStatementController::INTEGER, $nofollow],
            [DbStatementController::INTEGER, $has_submenu],
            [DbStatementController::INTEGER, $item['submenu_type']],
            [DbStatementController::INTEGER, $item['submenu_source']],
            [DbStatementController::INTEGER, $item['id']]
        ];
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            foreach ($item['name'] as $i => $name) {
                $item['name'][$i] = prepareString($name, true);
                $lang_active = (isset($item['lang_active'][$i]) && $item['lang_active'][$i]) ? 1 : 0;

                $q = "SELECT * FROM " . $this->tableDescription . " ";
                $q.= "WHERE parent_id=? AND language_id=? ";
                $params = [
                    [DbStatementController::INTEGER, $item['id']],
                    [DbStatementController::INTEGER, $i]
                ];
                $statement = $this->query($q, $params);
                if ($statement->num_rows() > 0) {
                    $q = "UPDATE " . $this->tableDescription . " SET ";
                    $q .= "name=?, ";
                    $q .= "url=?, ";
                    $q .= "active=?, ";
                    $q .= "target_id=? ";
                    $q .= "WHERE parent_id=? ";
                    $q .= "AND language_id=? ";
                    $params = [
                        [DbStatementController::STRING, $item['name'][$i]],
                        [DbStatementController::STRING, $item['url'][$i]],
                        [DbStatementController::INTEGER, $lang_active],
                        ];
                    if($item['type'] == self::TYPE_CATEGORY)
                        $params[] = [DbStatementController::INTEGER, $item['category_id']];
                    else
                        $params[] = [DbStatementController::INTEGER, $item['target_id'][$i]];

                    $params[] = [DbStatementController::INTEGER, $item['id']];
                    $params[] = [DbStatementController::INTEGER, $i];

                    $this->query($q, $params);
                } else {
                    $q = "INSERT INTO " . $this->tableDescription . " SET ";
                    $q .= "name=?, ";
                    $q .= "url=?, ";
                    $q .= "active=?, ";
                    $q .= "parent_id=?, ";
                    $q .= "language_id=?, ";
                    $q .= "target_id=? ";
                    $params = [
                        [DbStatementController::STRING, $item['name'][$i]],
                        [DbStatementController::STRING, $item['url'][$i]],
                        [DbStatementController::INTEGER, $lang_active],
                        [DbStatementController::INTEGER, $item['id']],
                        [DbStatementController::INTEGER, $i]
                    ];
                    if($item['type'] == self::TYPE_CATEGORY)
                        $params[] = [DbStatementController::INTEGER, $item['category_id']];
                    else
                        $params[] = [DbStatementController::INTEGER, $item['target_id'][$i]];

                    $this->query($q, $params);
                }
            }
            $this->saveEventInfo(json_encode($item), '', 1, __METHOD__, $this->table);
            return true;
        } else {
            return false;
        }
    }

    public function getById($id) {
        $q = "SELECT p.*, d.name FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.id=? ";
        $q .= "AND d.language_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];
        $statement = $this->query($q, $params);
        return $statement->fetch_assoc();
    }

    public function getItemDescription($id) {
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q .= "WHERE parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
        ];
        $statement = $this->query($q, $params);
        $opis = array();
        while ($row = $statement->fetch_assoc()) {
            $opis[$row['language_id']] = $row;
        }

        return $opis;
    }

    public function getPath($pID) {
        $path = '/';
        while ($pID != 0) {
            $q = "SELECT p.parent_id, d.name FROM " . $this->table . " p ";
            $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
            $q .= "WHERE p.id=? ";
            $q .= "AND d.language_id=? ";
            $params = [
                [DbStatementController::INTEGER, $pID],
                [DbStatementController::INTEGER, _ID]
            ];
            $statement = $this->query($q, $params);
            $res = $statement->fetch_assoc();
            $path = '/' . $res['name'] . $path;
            $pID = $res['parent_id'];
        }
        return $path;
    }

    public function getPathTitle($pID) {
        $path = array();
        while ($pID != 0) {
            $q = "SELECT p.parent_id, d.name FROM " . $this->table . " p ";
            $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
            $q .= "WHERE p.id=? ";
            $q .= "AND d.language_id=? ";
            $params = [
                [DbStatementController::INTEGER, $pID],
                [DbStatementController::INTEGER, _ID]
            ];
            $statement = $this->query($q, $params);
            $res = $statement->fetch_assoc();
            $path[] = $res['name'];
            $pID = $res['parent_id'];
        }

        $reversed = array_reverse($path);
        return $reversed;
    }

    public function loadMenu($pid, $group = -1, $menu_selected = '', $onlyActive = true) {
        $params = [];
        $params[] = [DbStatementController::INTEGER, $pid];
        $q = "SELECT p.*, d.name, d.url, d.target_id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.parent_id=? ";
        if ($group > -1) {
            $q .= "AND p.`group`=? ";
            $params[] = [DbStatementController::INTEGER, $group];
        }
        $q .= "AND d.language_id=? ";
        if($onlyActive)
            $q .= "AND d.active=1 ";
        $q .= "ORDER BY p.`group` ASC, p.`order` ASC";
        $params[] = [DbStatementController::INTEGER, _ID];
        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function _getPIDbyID($id) {
        $q = "SELECT parent_id FROM " . $this->table . " ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result();
    }

    /* funkcja sprawdza czy istnieje submenu dla danej pozycji */

    public function HasSubmenu($parent_id, $group) {
        $q = "SELECT COUNT(id) FROM " . $this->table . " ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND parent_id>0 ";
        $q .= "AND `group`=? ";

        $params = [
            [DbStatementController::INTEGER, $parent_id],
            [DbStatementController::INTEGER, $group]
        ];
        $statement = $this->db->query($q, $params);
        $count = $statement->get_result();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /* funkcja laduje submenu dla danej pozycji */

    public function getSubmenu($parent_id, $group, $parent_selected, $menu_selected = '') {
        $parent_selected = false;
        $q = "SELECT p.*, d.name, d.url, d.target_id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.parent_id=? ";
        $q .= "AND p.parent_id>0 ";
        $q .= "AND p.`group`=? ";
        $q .= "AND d.language_id=? ";
        $q .= "AND d.active=1 ";
        $q .= "ORDER BY p.`order` ASC";
        $params = [
            [DbStatementController::INTEGER, $parent_id],
            [DbStatementController::INTEGER, $group],
            [DbStatementController::INTEGER, _ID]
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    function LoadXml($pid, $group = -1) {
        $aMenu = $this->_loadMenu($pid, $group);

        $xml = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>';
        $xml .= '<links>';

        if (is_array($aMenu)) {
            foreach ($aMenu as $val) {
                $xml.= '<link name="' . $val['name'] . '" ref="' . $val['url'] . '" />';
            }
        }

        $xml .= '</links>';

        return $xml;
    }

    function getMaxOrder($parent, $group) {
        $q = "SELECT MAX(`order`) FROM " . $this->table . " ";
        $q .= "WHERE `group`=? ";
        $q .= "AND parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $group],
            [DbStatementController::INTEGER, $parent]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result();
    }

    function deleteItem($id) {
        $item = $this->getById($id);

        /* usuwamy element menu */
        $q = "DELETE FROM " . $this->table . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {

            $q = "DELETE FROM " . $this->tableDescription . " WHERE parent_id=? ";
            $params = [
                [DbStatementController::INTEGER, $id]
            ];
            $this->query($q, $params);

            /* aktualizujemy kolejność */
            $q = "UPDATE " . $this->table . " SET ";
            $q.= "`order`=`order`-1 ";
            $q.= "WHERE `order`>? ";
            $q.= "AND `group`=? ";
            $q.= "AND parent_id=? ";
            $params = [
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $item['group']],
                [DbStatementController::INTEGER, $item['parent_id']]
            ];
            $this->query($q, $params);

            /* usuwamy wszystkie elementy potomne elementu menu */
            $q = "SELECT * FROM " . $this->table . " WHERE parent_id=? ";
            $params = [
                [DbStatementController::INTEGER, $id]
            ];

            $statement2 = $this->query($q, $params);
            while ($row = $statement2->fetch_assoc()) {
                $this->deleteItem($row['id']);
            }
            $this->saveEventInfo(json_encode($item), '', 2, __METHOD__, $this->table);
            return true;
        }

        return false;
    }

    // funkcja laduje menu w celu grupowania podstron
    function LoadMenuStrony($parent_id) {
        $q = "SELECT p.id, d.name FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON d.parent_id=p.id ";
        $q .= "WHERE p.parent_id=? ";
        $q .= "AND d.language_id=? ";
        $q .= "ORDER BY p.`order` ASC, d.name ASC";

        $params = [
            [DbStatementController::INTEGER, $parent_id],
            [DbStatementController::INTEGER, _ID]
        ];
        $statement = $this->db->query($q, $params);
        $items = array();
        while ($row = $statement->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function getImageName($id){
        $q = "SELECT image FROM ". $this->table ." WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function deletePhoto($id, $background = false){
        $q = "UPDATE " . $this->table . " SET has_image=0, photo='' WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id'=>$id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updatePhoto($id, $filename, $background = false){
        $q = "UPDATE ". $this->table . " SET has_image = 1,  photo=? WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $filename],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id'=>$id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateItemsOrder($item, $order_new){

        if ($item['order'] < $order_new) {
            $q = "UPDATE " . $this->table . " SET `order`=`order`-1 ";
            $q .= "WHERE `group`=? ";
            $q .= "AND `order`>? ";
            $q .= "AND `order`<=? ";
            $q .= "AND parent_id =  ? ";
            $params = [
                [DbStatementController::INTEGER, $item['group']],
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['parent_id']]
            ];
            $this->query($q, $params);

        } else {
            $q = "UPDATE " . $this->table . " SET `order`=`order`+1 ";
            $q .= "WHERE `group`=? ";
            $q .= "AND `order`>=? ";
            $q .= "AND `order`<? ";
            $q .= "AND parent_id = ? ";
            $params = [
                [DbStatementController::INTEGER, $item['group']],
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $item['parent_id']]
            ];
            $this->query($q, $params);

        }
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "`order`=? ";
        $q .= "WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $order_new],
            [DbStatementController::INTEGER, $item['id']]
        ];
        $statement = $this->query($q, $params);
        $this->saveEventInfo('items order', '', 1, __METHOD__, $this->table);
        if ($statement->is_success()) {
            return true;
        }
        return false;
    }

    public function getSitemapData(): ?array{
        $q = "SELECT p.type, d.target_id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->table . "_description d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND p.type=? AND d.active=1 ";
        $q .= "ORDER BY d.name ASC";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, Menu::TYPE_MODULE]
        ];

        $result = $this->query($q, $params)->get_all_rows();

        if ($result)
            return $result;

        return [];
    }

}
