<?php
/**XXX PROBARLO COMPLETO!!!! FIXME TODO
* Objeto Relacional Multiplicador
*
* @package    PymEngine
* @subpackage ORMEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/
import('core.helpers.patterns');
import('core.orm_engine.morm.morm');



class MultiplierObject {

    public function __construct($compuesto, $type, $compositor=NULL) {
        $this->rel_id = 0;
        $this->compuesto = Pattern::composite($type, $compuesto);
        $this->compositor = $compositor;
        $this->rel = 1;
        
    }

    private function setrel() {
        $tmpvar = 0;
        while($tmpvar < $this->rel) {
            Pattern::aggregation($this->compuesto, $this->compositor);
            $tmpvar++;
        }
    }

    public function get($cls_compositor='') {
        $this->compositor = new $cls_compositor();
        $results = MORM($this)->read();
        foreach($results as $fields) {
            $this->rel = $fields['rel'];
            $this->compositor = Pattern::factory(
                $cls_compositor, $fields['compositor']);
                $this->setrel();
        }
    }
    
    public function save($destroy=True) {
        if($destroy) $this->destroy();
        $this->rel_id = MORM($this)->create();
        $this->setrel();
    }
    
    public function destroy() {
        MORM($this)->delete();
    }
}


# Compatibilidad para PHP 5.3
function MultiplierObject($compuesto, $type, $compositor=NULL) {
    return new MultiplierObject($compuesto, $type, $compositor);
}
?>
