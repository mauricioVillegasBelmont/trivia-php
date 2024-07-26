<?php
/**
* Mapeador relacional de objetos de objetos
*
* Provee de métodos CRUD para cualquier objeto estándar
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/

import('core.orm_engine.orm.object_parser');
import('core.orm_engine.orm.helper');
import('core.orm_engine.dblayer');


class ORM {
    public $object;
    public $parser;

    public function __construct($obj) {
        $this->object = $obj;
        $this->parser = new ORM\ObjectParser($obj);
    }

    private function get_id2array_data() {
        $idname = $this->parser->property_id;
        return array($this->object->$idname);
    }

    # Guardar datos
    public function create() {
        $sql = ORMHelper::get_insert_query($this->parser);
        return DBLayer::execute($sql, $this->parser->values);
    }

    # Leer datos
    public function read() {
        $sql = ORMHelper::get_select_query($this->parser);
        $results = DBLayer::execute($sql, $this->get_id2array_data());
        return (count($results) == 1) ? $results[0] : $results;
    }

    # Actualizar datos
    public function update() {
        $sql = ORMHelper::get_update_query($this->parser);
        $data = array_merge($this->parser->values, $this->get_id2array_data());
        return DBLayer::execute($sql, $data);
    }

    # Eliminar datos
    public function delete() {
        $sql = ORMHelper::get_delete_query($this->parser);
        return DBLayer::execute($sql, $this->get_id2array_data());
    }
}


function ORM($obj) {
    return new ORM($obj);
}

?>
