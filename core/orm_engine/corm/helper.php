<?php
/**
* Ayudante del Mapeador Relacional de conectores lógicos relacionales
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/

class CORMHelper {

    public static function get_insert_query($p) {
        $data = array($p->table, $p->insert_string);
        $string = "INSERT INTO %s (compuesto, compositor) VALUES %s";
        return vsprintf($string, $data);
    }

    public static function get_select_query($p) {
        $data = array($p->table);
        return vsprintf("SELECT compositor FROM %s WHERE compuesto = ?", $data);
    }

    public static function get_delete_query($p) {
        $data = array($p->table);
        return vsprintf("DELETE FROM %s WHERE compuesto = ?", $data);
    }

}

?>
