<?php
import('core.orm_engine.dblayer');


class DataHelper {

    public static function get_scheme($table) {
        $sql = "SELECT  COLUMN_NAME
                FROM    COLUMNS
                WHERE   TABLE_SCHEMA = ?
                AND     TABLE_NAME = ?
        ";
        $data = array(DB_NAME, "$table");
        return DBLayer::execute($sql, $data, 'INFORMATION_SCHEMA');
    }

    public static function explode_scheme($table) {
        $scheme = self::get_scheme($table);
        $columns = array();
        foreach($scheme as $col) $columns[] = $col['COLUMN_NAME'];
        $campos = join(', ', $columns);
        return $campos;
    }

    public static function set_query($table, $where=NULL, $type='LATEST') {
        $str_campos = self::explode_scheme($table);
        $sql = "SELECT $str_campos FROM $table ";
        if(!is_null($where)) $sql .= "WHERE $where ";
        if($type == 'LATEST') $sql .= "ORDER BY {$table}_id DESC LIMIT ?";
        return $sql;
    }

    public static function set_query_like($table, $field, $kword) {
        $str_campos = self::explode_scheme($table);
        $sql = "SELECT $str_campos FROM $table WHERE $field LIKE ?";
        return $sql;
    }

    public static function set_query_between($table, $field) {
        $str_campos = self::explode_scheme($table);
        $sql = "SELECT $str_campos FROM $table WHERE $field BETWEEN ? AND ?";
        return $sql;
    }

    public static function explode_condition($condition, $filter) {
        list($field, $value) = explode($filter, $condition);
        return array(trim($field), trim($value));
    }
}

?>
