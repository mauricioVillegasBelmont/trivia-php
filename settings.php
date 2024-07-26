<?php
/**
* Constantes de configuración personalizada.
*
* Este archivo debe renombrarse a settings.php (o ser copiado como tal)
* Al renombrarlo/copiarlo, modificar el valor de todas las constantes.
*
* @package    PymEngine
* @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
* @author     LinK <contacto@pymeweb.mx>
* @link       https://pymeweb.mx
*/


# ==============================================================================
#                                CONSTANTES
# ==============================================================================
$config_file = 'config.ini';

$options = parse_ini_file($config_file, True);
foreach($options as $section=>$config) {
    if($section != 'PLUGINS') {
        foreach($config as $constant=>$value) {
            define($constant, $value);
            if($section == 'TEMPLATE') $GLOBALS['DICT'][$constant] = $value;
        }
    }
}


# ==============================================================================
#                           CONFIGURACIÓN DE PHP
# ==============================================================================
ini_set('include_path', APP_DIR);
ini_set('session.gc_maxlifetime', SESSION_LIFE_TIME);
ini_set('session.cookie_lifetime', SESSION_LIFE_TIME);
ini_set('session.cache_expire', 7200);
date_default_timezone_set('Mexico/General');

if(!PRODUCTION) {
    ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
    ini_set('display_errors', '1');
    ini_set('track_errors', 'On');
} else {
    ini_set('display_errors', '0');
}

session_start();

if(!isset($GLOBALS['DICT'])) $GLOBALS['DICT'] = array();

# ==============================================================================
#                   HELPER PARA IMPORTACIÓN DE ARCHIVOS
# ==============================================================================
function import($str='', $exit=True) {
    $file = str_replace('.', '/', $str);
    if(file_exists(APP_DIR . "$file.php")) {
        require_once("$file.php");
    } else {
        if($exit) exit("FATAL ERROR: no se pudo importar '$str'");
    }
}

?>
