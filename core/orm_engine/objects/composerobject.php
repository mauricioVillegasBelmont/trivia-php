<?php
import('core.orm_engine.dblayer');
import('core.orm_engine.orm.helper');
import('core.orm_engine.objects.standardobject');
import('core.helpers.patterns');


abstract class ComposerObject extends StandardObject {

    function compose($compuesto) {
        extract($this->_parser_this($compuesto));
        $op = new ORM\ObjectParser($this);
        $sql = ORMHelper::get_composer_query($op, $compuesto_name);
        $results = DBLayer::execute($sql, array($compuesto_idvalue));
        foreach($results as $row) {
            $compuesto->$aggregation_function(
                Pattern::factory(get_class($this), $row[$this_idname])
            );
        }
    }

    protected function _parser_this($compuesto) {
        $compuesto_name = strtolower(get_class($compuesto));
        $compuesto_idname = "{$compuesto_name}_id";
        $compuesto_idvalue = $compuesto->$compuesto_idname;
        $compositor_name = strtolower(get_class($this));
        $this_idname = "{$compositor_name}_id";
        $aggregation_function = "add_{$compositor_name}";
        return get_defined_vars();
    }

}

?>
