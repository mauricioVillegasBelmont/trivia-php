<?php

abstract class BranchedObject extends StandardObject {

    public function __construct() {
        extract($this->set_names());
        $this->$id_name = 0;
        $this->denomination = '';
        $this->state = 0;
        $this->$collection_name = array();
    }

    public function get_tree($id=1) {
        extract($this->set_names());
        $this->$id_name = $id;
        $this->get();
        $this->ramify();
    }

    protected function ramify($txt="", $ul="<li>") {
        extract($this->set_names());
        $obj = new $cls_name();
        $obj->$id_name = $this->$id_name;

        if(!isset($GLOBALS['bullet_tree'])) $GLOBALS['bullet_tree'] = '';
        if(!isset($GLOBALS['plain_tree'])) $GLOBALS['plain_tree'] = '';
        if(empty($GLOBALS['bullet_tree']))$GLOBALS['bullet_tree'] = "<ul>";

        $obj->denomination = "{$txt}{$this->denomination}";
        $GLOBALS['plain_tree'][] = $obj;
        $txt = str_replace(array('│', '─'), array('│', '&nbsp;'), $txt) . "│─ ";

        $link = "<a href='{link}/{$this->$id_name}'>{$this->denomination}";
        $GLOBALS['bullet_tree'] .= "{$ul}&#xf114; {$link}</a>" . chr(10);
        $ul = "<ul><li>";

        foreach($this->$collection_name as $branch) {
            $branch->ramify($txt, $ul);
        }
        $GLOBALS['bullet_tree'] .= "</ul>" . chr(10);
    }

    private function set_names() {
        $cls_name = get_class($this);
        $lower_name = strtolower(get_class($this));
        $id_name = "{$lower_name}_id";
        $collection_name = "{$lower_name}_collection";
        return get_defined_vars();
    }

}

?>
