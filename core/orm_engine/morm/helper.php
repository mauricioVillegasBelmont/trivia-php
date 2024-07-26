<?php

class MORMHelper {

    public static function get_insert_query($p) {
        $data = array($p->table);
        $str = "INSERT INTO %s (compositor, compuesto, rel) VALUES (?, ?, ?)";
        return vsprintf($str, $data);
    }

    public static function get_select_query($p) {
        $data = array($p->table);
        $format = "SELECT rel_id, compositor, rel FROM %s WHERE compuesto = ?";
        return vsprintf($format, $data);
    }

    public static function get_delete_query($p) {
        $data = array($p->table);
        return vsprintf("DELETE FROM %s WHERE compuesto = ?", $data);
    }

}

?>
