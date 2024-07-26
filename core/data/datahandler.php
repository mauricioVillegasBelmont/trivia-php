<?php
import('core.data.datahelper');
import('core.helpers.patterns');


const DH_FILTER_EQ = '=';
const DH_FILTER_NOTEQ = '<>';
const DH_FILTER_LT = '<';
const DH_FILTER_GT = '>';

const DH_FORMAT_DATA = 'data';
const DH_FORMAT_OBJECT = 'object';


class DataHandler {

    public function __construct($table='', $format='') {
        $this->table = $table;
        $this->format = ($format) ? $format : DH_FORMAT_DATA;
    }

    public function get_latest($n=1) {
        $sql = DataHelper::set_query($this->table);
        $sql = str_replace('?', $n, $sql);
        $results = DBLayer::execute($sql);
        if($this->format == DH_FORMAT_OBJECT) $this->__data2object($results);
        return $results;
    }

    public function filter($condition='', $filter=DH_FILTER_EQ) {
        list($field, $value) = DataHelper::explode_condition(
            $condition, $filter);
        $where = str_replace($value, '?', $condition);
        $sql = DataHelper::set_query($this->table, $where, NULL);
        $data = array($value);
        $results = DBLayer::execute($sql, $data);
        if($this->format == DH_FORMAT_OBJECT) $this->__data2object($results);
        return $results;
    }

    public function filter_like($field, $kword) {
        $sql = DataHelper::set_query_like($this->table, $field, $kword);
        $data = array("%$kword%");
        $results = DBLayer::execute($sql, $data);
        if($this->format == DH_FORMAT_OBJECT) $this->__data2object($results);
        return $results;
    }

    public function filter_between($field, $v1, $v2) {
        $sql = DataHelper::set_query_between($this->table, $field);
        $data = array($v1, $v2);
        $results = DBLayer::execute($sql, $data);
        if($this->format == DH_FORMAT_OBJECT) $this->__data2object($results);
        return $results;
    }

    private function __data2object(&$data) {
        $cls = ucwords($this->table);
        foreach($data as &$array) {
            $id = "{$this->table}_id";
            $array = Pattern::factory($cls, $array[$id]);
        }
    }

}


function DataHandler($table='', $format=DH_FORMAT_DATA) {
    return new DataHandler($table, $format);
}
?>
