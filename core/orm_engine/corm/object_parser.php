<?php
namespace CORM;
/**
* Analizador sintáctico de objetos
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/


class ObjectParser {

    public function __construct($compuesto=NULL, $compositor='') {
        $this->compositor = strtolower($compositor);
        if(!is_null($compuesto)) {
            $this->obj_compuesto = $compuesto;
            $this->compuesto = $this->get_table_name($compuesto);
            $this->table = "{$this->compositor}{$this->compuesto}";
            $this->compuesto_id = $this->get_compuesto_id($compuesto);
            $this->coleccion = $this->get_coleccion();
            $this->insert_string = $this->get_insert_string();
            $this->insert_values = $this->get_insert_values();
        }
    }

    public function get_coleccion() {
        $name = "{$this->compositor}_collection";
        return $this->obj_compuesto->$name;
    }

    public function get_table_name($obj) {
        return strtolower(get_class($obj));
    }

    public function get_compuesto_id($compuesto) {
        $idname = "{$this->compuesto}_id";
        return $compuesto->$idname;
    }

    public function get_insert_values() {
        $values = array();
        $idname = "{$this->compositor}_id";
        foreach($this->coleccion as $compositor) {
            $values[] = $this->compuesto_id;
            $values[] = $compositor->$idname;
        }
        return $values;
    }

    public function get_id($obj) {
        return strtolower(get_class($obj)) . '_id';
    }

    public function get_insert_string() {
        $cant = count($this->coleccion);
        if($cant > 0) return implode(', ', array_fill(0, $cant, "(?, ?)"));
    }

}



function ObjectParser($obj=NULL, $compositor='') {
    return new ObjectParser($obj, $compositor);
}
?>
