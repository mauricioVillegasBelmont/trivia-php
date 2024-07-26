<?php
import('core.orm_engine.morm.object_parser');
import('core.orm_engine.morm.helper');


class MORM {

    public function __construct($obj) {
        $this->object = $obj;
        $this->parser = new MORM\ObjectParser($obj);
    }

    # Guardar datos
    public function create() {
        $sql = MORMHelper::get_insert_query($this->parser);
        $data = array(
            $this->parser->compositor_id,
            $this->parser->compuesto_id,
            $this->object->rel
        );
        return DBLayer::execute($sql, $data);
    }

    # Leer datos
    public function read() {
        $sql = MORMHelper::get_select_query($this->parser);
        $data = array($this->parser->compuesto_id);
        $results = DBLayer::execute($sql, $data);
        return (count($results) == 1) ? $results[0] : $results;
    }

    # Eliminar datos
    public function delete() {
        $sql = MORMHelper::get_delete_query($this->parser);
        return DBLayer::execute($sql, array($this->parser->compuesto_id));
    }
}


function MORM($obj) {
    return new MORM($obj);
}

?>
