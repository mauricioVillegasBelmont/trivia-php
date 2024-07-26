<?php
/**
* Base de creación para objetos estándar
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/
import('core.orm_engine.orm.orm');
import('core.orm_engine.objects.logicalconnector');
import('core.helpers.patterns');
import('core.data.datahelper');


abstract class StandardObject extends ObjectEE {

    function save() {
        @$this->__tolog();
        $id = ORM\ObjectParser()->get_id($this);
        (!$this->$id) ? $this->$id = ORM($this)->create() : ORM($this)->update();
    }

    function get() {
        @$this->__tolog();
        $data = ORM($this)->read();
        foreach($data as $property=>$value) {
            if(is_null($this->$property) && !is_null($value)) {
                $this->$property = Pattern::factory(ucwords($property), $value);
            } else {
                $this->$property = $value;
            }
        }
        $this->set_collections();
    }
    
    function destroy() {
        @$this->__tolog();
        ORM($this)->delete();
    }

    private function set_collections() {
        $properties = get_object_vars($this);

        foreach($properties as $name=>$value) {
            if(is_array($value)) {
                $cls_name = str_replace("_collection", "", ucwords($name));
                $compositor = new $cls_name();
                $parent = get_parent_class($compositor);
                if($parent == "StandardObject" || $parent == "BranchedObject") {
                    $tblrel = strtolower($cls_name . get_class($this));
                    $fields = DataHelper::get_scheme($tblrel);
                    if(count($fields) == 3) {
                        $lc = new LogicalConnector($this, $cls_name);
                        $lc->get();
                    } else {
                        unset($this->$name);
                        $mo = new MultiplierObject($this, get_class($this));
                        $mo->get($cls_name);
                    }
                } elseif($parent == "ComposerObject") {
                    $compositor->compose($this);
                }
            }
        }
    }

    /**
     *  Previene errores cuando el objeto no posee un método de agregación
     *  para una propiedad colectora
     */
    function __call($call, $arg) {
        if(strpos($call, 'add_') === 0 && $arg) {
            $cls = str_replace('add_', '', $call);
            $property = "{$cls}_collection";
            if(!property_exists($this, $property)) $this->$property = array(); 
            $this->$property = array_merge($this->$property, $arg);
        }
    }

}

?>
