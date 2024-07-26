<?php

namespace MORM;


class ObjectParser {

    public function __construct($obj=NULL) {
        if(!is_null($obj)) {
            $this->table = $this->get_table_name($obj);
            $this->compuesto_id = $this->get_compuesto_id($obj);
            $this->compositor_id = $this->get_compositor_id($obj);
        }
    }

    public function get_compuesto_id($obj) {
        $compuesto = $this->get_name($obj->compuesto);
        $idname = "{$compuesto}_id";
        return $obj->compuesto->$idname;
    }

    public function get_compositor_id($obj) {
        $compositor = $this->get_name($obj->compositor);
        $idname = "{$compositor}_id";
        return $obj->compositor->$idname;
    }

    public function get_table_name($obj) {
        $compuesto = $this->get_name($obj->compuesto);
        $compositor = strtolower(get_class($obj->compositor));
        return "{$compositor}{$compuesto}";
    }

    private function get_name($obj) {
        return strtolower(get_class($obj));
    }

}



function ObjectParser($obj=NULL) {
    return new ObjectParser($obj);
}

?>
