<?php
/**
* Mapeador relacional de conectores lógicos relacionales
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/

import('core.orm_engine.corm.object_parser');
import('core.orm_engine.corm.helper');
import('core.orm_engine.dblayer');


class CORM {

    public function __construct($compuesto=NULL, $compositor='') {
        $this->parser = new CORM\ObjectParser($compuesto, $compositor);
    }

    # Guardar datos
    public function create() {
        $sql = CORMHelper::get_insert_query($this->parser);
        return DBLayer::execute($sql, $this->parser->insert_values);
    }

    # Leer datos
    public function read() {
        $sql = CORMHelper::get_select_query($this->parser);
        return DBLayer::execute($sql, array($this->parser->compuesto_id));
    }

    # Eliminar datos
    public function delete() {
        $sql = CORMHelper::get_delete_query($this->parser);
        return DBLayer::execute($sql, array($this->parser->compuesto_id));
    }
}


function CORM($obj, $compositor='') {
    return new CORM($obj, $compositor);
}

?>
