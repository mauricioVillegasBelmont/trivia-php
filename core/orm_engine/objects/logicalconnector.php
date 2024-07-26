<?php
/**
* Conector Lógico Relacional
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/
import('core.helpers.patterns');
import('core.orm_engine.corm.helper');
import('core.orm_engine.corm.corm');


class LogicalConnector {

    protected static $composername;

    public function __construct($obj=NULL, $compositor='') {
        self::$composername = strtolower($compositor);
        $this->connector_id = 0;
        $this->compuesto = $obj;
        $f = strtolower($compositor) . "_collection";
        $this->collection = $obj->$f;
    }

    public function destroy() {
        CORM($this->compuesto, self::$composername)->delete();
    }

    public function save() {
        $this->destroy();
        if(count($this->collection) > 0) CORM($this->compuesto, 
            self::$composername)->create();
    }
    
    public function get() {
        $results = CORM($this->compuesto, self::$composername)->read();
        foreach($results as $field) {
            $c = Pattern::factory(self::$composername, $field['compositor']);
            $method = "add_" . self::$composername;
            $this->compuesto->$method($c);
        }
    }
}


function LogicalConnector($obj_compuesto=NULL, $cls_compositor='') {
    return new LogicalConnector($obj_compuesto, $cls_compositor);
}

?>
