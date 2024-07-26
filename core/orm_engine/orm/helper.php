<?php
/**
* Ayudante del Mapeador Relacional de Objetos
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/

class ORMHelper {

    public static function get_insert_query($p) {
        $data = array($p->table, $p->insert_string, $p->substitution_string);
        return vsprintf("INSERT INTO %s (%s) VALUES (%s)", $data);
    }

    public static function get_update_query($p) {
        $data = array($p->table, $p->update_string, $p->property_id);
        return vsprintf("UPDATE %s SET %s WHERE %s = ?", $data);
    }

    public static function get_select_query($p) {
        $data = array($p->string_fields, $p->table, $p->property_id);
        return vsprintf("SELECT %s FROM %s WHERE %s = ?", $data);
    }

    public static function get_delete_query($p) {
        $data = array($p->table, $p->property_id);
        return vsprintf("DELETE FROM %s WHERE %s = ?", $data);
    }

    public static function get_composer_query($p, $compuesto) {
        $data = array($p->property_id, $p->table, $compuesto);
        return vsprintf("SELECT %s FROM %s WHERE %s = ?", $data);
    }
}

?>
