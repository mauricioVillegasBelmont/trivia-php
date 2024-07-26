<?php
/**
* Capa de abstracción a bases de datos
*
* Permite ejecutar consultas a bases de datos soportadas por PDO
* Actualmente testeada con:
*        MySQL (testing exhaustivo)
*   PostgreSQL (testing básico)
*
* package    PymEngine
* subpackage ORMEngine
* license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* author     Eugenia Bahit <ebahitmember.fsf.org>
* link       https://pymeweb.mx
*/

use function PHPSTORM_META\type;

import('core.dev_tools.error_handler');
import('core.dev_tools.helper');


class DBLayer {

    public static $db;
    public static $results = True;


    public static function execute($sql, $data=array(), $db=DB_NAME) {
        self::$db = $db;
        extract(self::set_temp_connection_vars());
        try {
            $pdo = new PDO($connection_string, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            DBLayerErrorHandler()->brief($e->getCode());
        }

        $query = $pdo->prepare($sql);

        for($i=0; $i<count($data); $i++){
            $type = self::getPDOParamType($data[$i]);
            $query->bindParam($i+1, $data[$i], $type );
        };
        $query->execute();

        $errors = $query->errorInfo();
        if(!is_null($errors[1])){
            if(!PRODUCTION){echo "ERROR:<pre>"; var_dump($errors); echo "</pre>";}
            DBLayerErrorHandler()->handle(EH_BOTH, $errors);
        }

        $is_insert = (strpos(strtoupper($sql), 'INSERT') === 0);
        $is_select = (strpos(strtoupper($sql), 'SELECT') === 0);
        if($is_insert) {
            self::$results = $pdo->lastInsertId();
        } elseif($is_select) {
            self::$results = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return self::$results;
    }

    private static function getPDOParamType($value) {
        if (is_int($value))  return PDO::PARAM_INT;
        if (is_bool($value)) return PDO::PARAM_BOOL;
        if (is_null($value)) return PDO::PARAM_NULL;
        return PDO::PARAM_STR;
    }

    private static function verify_constatnts() {
        if(!defined('DB_DRIVER')) define('DB_DRIVER', 'mysql');
        if(!defined('DB_PORT')) define('DB_PORT', '3306');
        if(defined('DB_TESTING')) self::$db = DB_TESTING;
    }

    private static function set_temp_connection_vars() {
        self::verify_constatnts();
        $connection_data = array(DB_DRIVER, DB_HOST, DB_PORT, self::$db, DB_CHARSET);
        $format_string = "%s:host=%s;port=%s;dbname=%s;charset=%s";
        $connection_string = vsprintf($format_string, $connection_data);
        $options = array(PDO::ATTR_PERSISTENT=>true);
        return get_defined_vars();
    }

}




class DBLayerErrorHandler extends ErrorHandler {

    public static $errors;

    static function set_errors() {
        $db = DB_NAME;
        $user = DB_USER;
        $host = DB_HOST;
        $driver = ucwords(DB_DRIVER);

        self::$errors = array(
            '1045'=>array(
                'msg'=>"Usuario o clave de $driver incorrectos",
                'const'=>"DB_USER y/o DB_PASS"
            ),
            '1049'=>array(
                'msg'=>"No existe la DB '$db' para el user '$user'",
                'const'=>"DB_NAME"
            ),
            '2005'=>array(
                'msg'=>"'$host' no es un servidor de bases de datos accesible",
                'const'=>"DB_HOST"
            ),
        );
    }

    public function brief($errno) {
        self::set_errors();

        $data = array(
          "date"=>date("d/m/Y H:i:s"),
          "client"=>$_SERVER['REMOTE_ADDR'],
          "error"=>self::$errors[$errno]['msg'],
          "const"=>self::$errors[$errno]['const']
        );

        Debugger(false)->trace($data, 'pdo_connerror');
        Logger()->log($data, 'pdo_connerror');
        exit();
    }

    public function handle($mode=EH_BOTH, $errors="") {
        extract(self::get_tmp_vars());
        $data = array(
          "date"=>date("d/m/Y H:i:s"),
          "client"=>$_SERVER['REMOTE_ADDR'],
          "uri"=>DevToolsHelper::get_real_uri(),
          "model"=>DevToolsHelper::get_model_name(),
          "clsvars_str"=>DevToolsHelper::array_to_string($clsvars),
          "fields_str"=>$fields_str,
          "objvars_str"=>DevToolsHelper::array_to_string($objvars),
          "model2table"=>$m2t_str,
          "object2table"=>$o2t_str,
          "object2model"=>DevToolsHelper::get_diff_to_string($objvars, $clsvars),
          "extra"=>$errors
        );

        $both = ($mode == EH_BOTH);
        $debug = ($both || $mode == EH_DEBUG);
        $log = ($both || $mode == EH_LOG);
        if($debug) Debugger()->trace($data, 'pdo_sqlerror');
        if($log) Logger()->log($data, 'pdo_sqlerror');
        exit();
    }

    private static function help_text_no_table() {
        $table = strtolower(DevToolsHelper::get_model_name());
        return "La DB '". DB_NAME ."' no tiene una tabla '$table'";
    }

    private static function get_tmp_vars() {
        $notbl = self::help_text_no_table();

        $clsvars = DevToolsHelper::get_class_properties();
        $fields = DevToolsHelper::get_fields_from_table();
        $objvars = DevToolsHelper::get_object_properties();

        list($fields_str, $m2t_str, $o2t_str) = array_fill(0, 3, $notbl);

        if(!empty($fields)) {
            $fields_str = DevToolsHelper::array_to_string($fields);
            $m2t_str = DevToolsHelper::get_diff_to_string($clsvars, $fields);
            $o2t_str = DevToolsHelper::get_diff_to_string($objvars, $fields);
        }

        return get_defined_vars();
    }

}


function DBLayerErrorHandler() { return new DBLayerErrorHandler(); }

?>
