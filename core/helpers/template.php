<?php
/**
* Clase que permite realizar sustituciones estáticas y dinámicas (iterativas)
*
* @package        PymEngine
* @subpackage     core.helpers
* @license        http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author         LinK <contacto@pymeweb.mx>
* @link           https://pymeweb.mx
* @contributors   Jimmy Daniel Barranco, Xabi Pico
*/
 
class Template {
    public $str;
    public $filename;
    public $dict;
 
    public function __construct($str='', $custom_file='') {
        $this->str = $str;
        $default_tmpl = APP_DIR . "core/templates/basetemplate.html";
        $file = ($custom_file) ? $custom_file : (
            (CUSTOM_TEMPLATE) ? CUSTOM_TEMPLATE : $default_tmpl);
        $this->filename = $file;
        if(!empty($custom_file)) $this->str = file_get_contents($file);
    }

    public function render($dict=array()) {
        settype($dict, 'array');
        if(empty($dict)) $dict = $GLOBALS['DICT'];
        $this->set_dict($dict);
        return str_replace(array_keys($this->dict), array_values($this->dict),
            $this->str);
    }

    public function render_safe($dict=array()) {
        if(empty($dict)) $dict = $GLOBALS['DICT'];
        $new_dict = array();
        foreach($dict as $key=>$value) {
            $identificador = "<!--$key-->";
            if(!$value) {
                $ini = strpos($this->str, $identificador);
                if($ini !== False) {
                    $search = $this->get_substr($key, False);
                    $this->str = str_replace($search, "", $this->str);
                }
                $this->str = str_replace("{{$key}}", "", $this->str);
            } else {
                $new_dict[$key] = $value;
            }
            $this->str = str_replace($identificador, "", $this->str);
        }
        return $this->render($new_dict);
    }

    function get_regex($key, $remove_keys=True) {
        if(USE_PCRE) {
            $actual_recursion_limit = ini_set("pcre.recursion_limit", 10000);
            $regex = "/<!--$key-->(.|\n){1,}<!--$key-->/";
            preg_match($regex, $this->str, $matches);
            $no_keys = str_replace("<!--$key-->", "", $matches[0]);
            ini_set("pcre.recursion_limit", $actual_recursion_limit);
            if(PREG_RECURSION_LIMIT_ERROR === preg_last_error()) {
                return $this->get_substr($key, $remove_keys);
            } else {
                return ($remove_keys) ? $no_keys : $matches[0];
            }

        } else {
            return $this->get_substr($key, $remove_keys);
        }
    }
 
    function get_substr($key, $remove_keys=True) {
        $needle = "<!--$key-->";
        $first = strpos($this->str, $needle);
        $last = strrpos($this->str, $needle);
        if($first==False || $last==False) return "";
        $long = ($last - $first) + strlen($needle);
        $str = substr($this->str, $first, $long);
        $no_keys = str_replace($needle, "", $str);
        return ($remove_keys) ? $no_keys : $str;
    }

    function delete($key) {
        $needle_l = "<!--$key-->";
        $needle_r = "<!--/$key-->";
        $l = strpos($this->str, $needle_l);
        $r = strpos($this->str, $needle_r);
        $long = ($l AND $r AND $l!==$r) ? ($r - $l) + strlen($needle_r) : false;
        $str = $this->str;
        while( $long ) {
            $str = substr_replace( $str, "", $l, $long );
            $l = strpos($str, $needle_l);
            $r = strpos($str, $needle_r);
            $long = ($l AND $r AND $l!==$r) ? ($r - $l) + strlen($needle_r) : false;
        }
        return $str;
    }

    function render_regex($key='REGEX', $stack=array(), $use_pcre=USE_PCRE) {
        $originalstr = $this->str;
        $func = ($use_pcre) ? "get_regex" : "get_substr";
        $match = $this->$func($key, False);
        $this->str = $this->$func($key);
        $render = '';
        foreach($stack as $dict) $render .= $this->render($dict);
        return str_replace($match, $render, $originalstr);
    }

    function render_substr($key='REGEX', $stack=array()) {
        return $this->render_regex($key, $stack, False);
    }

    function render_menu() {
        if(!isset($GLOBALS['menu'])) $GLOBALS['menu'] = array();
        $this->str = $this->render_regex('MENU-MOBILE-ITEM', $GLOBALS['menu']);
        $this->str = $this->render_regex('MENU-ITEM', $GLOBALS['menu']);
    }

    function render_navbar() {
        $key = (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) ? 'nologin' : 'login';
        $user = (empty($_SESSION['username'])) ? '' : $_SESSION['username'];
        $level = (isset($_SESSION['level']) && $_SESSION['level'] == 1) ? " (admin)" : "";
        $uid = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
        $dict = array("USERNAME"=>$user, "LEVEL"=>$level, "UID"=> $uid);
        $this->str = Template($this->delete($key))->render($dict);
    }

    protected function set_dict($dict=array()) {
        $this->sanitize($dict);
        $keys = array_keys($dict);
        $values = array_values($dict);
        foreach($keys as &$key) {
            $key = "{{$key}}";
        }
        $this->dict = array_combine($keys, $values);
    }
   
    private function sanitize(&$dict) {
        foreach($dict as $key=>&$value) {
            if(is_array($value) or is_object($value)) {
                $value = print_r($value, True);
                if(strlen($value) > 100) {
                    $value = substr($value, 0, 100) . chr(10) . "(...)";
                    $value = nl2br($value);
                }
            }
        }
    }
   
    public function show($contenido='') {
        $default_tmpl = APP_DIR . "core/templates/basetemplate.html";
        if( isset($this->filename) && $this->filename!==$default_tmpl ){
            $title = "";
        }else{
            $title = $this->str;    
        }        
        $this->str = file_get_contents($this->filename);
        $this->render_menu();
        $this->render_navbar();
        $dict = array(
            "TITLE"=>($title) ? $title : DEFAULT_TITLE, 
            "CONTENIDO"=>$contenido,
        );
        $dict = array_merge($dict, $GLOBALS['DICT']);
        $this->str = Template($this->str)->render($dict);
        return $this->str;
    }
}
 
 
# Función agregada para compatibilidad con PHP 5.3
function Template($str='', $file='') {
    return new Template($str, $file);
}
 
 
# Alias para estilo por procedimientos
function template_render($str='', $dict=array()) {
    return Template($str)->render($dict);
}
 
function template_render_regex($str='', $key='REGEX', $dict=array()) {
    return Template($str)->render_regex($key, $dict);
}
?>
