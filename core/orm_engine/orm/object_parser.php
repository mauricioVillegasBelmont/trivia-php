<?php
namespace ORM;
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
    public $properties;
    public $table;
    public $fields;
    public $values;
    public $property_id;
    public $string_fields;
    public $substitution_string;
    public $update_string;
    public $insert_string;

    public function __construct($obj=NULL) {
        if(!is_null($obj)) {
            $this->properties = $this->get_clean_properties($obj);
            $this->table = $this->get_table_name($obj);
            $this->fields = $this->get_fields();
            $this->values = $this->get_values();
            $this->property_id = $this->get_id($obj);
            $this->string_fields = $this->get_string_fields();
            $this->substitution_string = $this->get_substitution_string();
            $this->update_string = $this->get_update_string();
            $this->insert_string = $this->get_insert_string();
        }
    }

    public function get_table_name($obj) {
        return strtolower(get_class($obj));
    }

    public function get_clean_properties($obj) {
        $properties = get_object_vars($obj);
        foreach($properties as $property=>$value) {
            if(is_array($value)) unset($properties[$property]);
        }
        return $properties;
    }

    public function get_fields() {
        return array_keys($this->properties);
    }

    public function get_id($obj) {
        return strtolower(get_class($obj)) . '_id';
    }

    public function get_values() {
        $data = array_values($this->properties);
        foreach($data as &$value) {
            if((gettype($value) == 'object')) {
                $property = $this->get_id($value);
                $value = $value->$property;
            }
        }
        array_shift($data);
        return $data;
    }

    public function get_string_fields() {
        return implode(', ', array_keys($this->properties));
    }

    public function get_substitution_string() {
        return implode(', ', array_fill(0, count($this->fields) - 1, '?'));
    }

    public function get_update_string() {
        $string = implode(' = ?, ', $this->fields) . ' = ? ';
        return str_replace("{$this->property_id} = ?, ", "", $string);
    }

    public function get_insert_string() {
        $string = implode(', ', $this->fields);
        return str_replace("{$this->property_id}, ", "", $string);
    }

}



function ObjectParser($obj=NULL) {
    return new ObjectParser($obj);
}
?>
