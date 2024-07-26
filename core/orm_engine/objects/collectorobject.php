<?php
/**
* Objeto Colector
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/
import('core.helpers.patterns');


class CollectorObject {

    private static $instance;
    public $collection;

    private function __construct() {
        $this->collection = array();
    }

    private function add_object($obj) {
        $this->collection[] = json_decode(json_encode($obj));
    }

    public static function get($class_name='Anonymous') {
        extract(CollectorHelper::get_names($class_name));
        $cls = "{$class_name}Collection";

        if(!isset(self::$instance[$cls])) {
            eval("class $cls extends CollectorObject {}");
            self::$instance[$cls] = new $cls();
        }

        $sql = CollectorHelper::set_query($class_name);
        $results = DBLayer::execute($sql);

        self::$instance[$cls]->collection = array();

        foreach($results as $r) {
            self::$instance[$cls]->add_object(
                Pattern::factory($class_name, $r[$idname]));
        }

        return self::$instance[$cls];
    }

}


class CollectorHelper {

    public static function set_query($cls='') {
        extract(self::get_names($cls));
        return "SELECT $idname FROM $tbl";
    }

    public static function get_names($cls) {
        $tbl = strtolower($cls);
        $idname = "{$tbl}_id";
        return get_defined_vars();
    }
}

?>
