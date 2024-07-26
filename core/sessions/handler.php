<?php

import('core.sessions.helper');
import('core.orm_engine.dblayer');
import('core.helpers.http');


class SessionBaseHandler {
    public $state;

    public function __construct() {
        $this->state = False;
    }

    /*
        Comprobar que nombre de usuario y contraseña sean válidos
    */
    public function check_user() {
        $user = SessionHelper::get_user();
        $pwd = SessionHelper::get_pwd();        
        $salt_ = DBLayer::execute(SessionHelper::set_salt_query(), array($user, $user));
        $salt = (count($salt_) == 1) ? $salt_[0]['salt'] : "";
        $hash = hash("sha256", $pwd . $salt);
        $data = array($user, $user, $hash);
        $result = DBLayer::execute(SessionHelper::set_query(), $data);
        if(count($result) > 0) {
            $this->start_session($result[0]);
        } else {
            $this->destroy_session();
        }
    }

    /*
        Iniciar la sesión del usuario
        TODO verificar $_SESSION['uri'] ¿dónde se genera?
    */
    public function start_session($data=array()) {
        $_SESSION['login_date'] = time();
        $_SESSION['level'] = $data['level'];
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['username'] = $data['name'];
        $_SESSION['user'] = $data;
        $this->state = True;
        $fecha = date("Y-m-d H:i:s", time());
        DBLayer::execute(SessionHelper::set_last_login_query(), array($fecha, $data['user_id']));
        $_SESSION['uri'] = (isset($_SESSION['uri']) && $_SESSION['uri']=="/")?"/dashboard":$_SESSION['uri'];
        if(isset($_SESSION['uri']) && $_SESSION['uri']!=="/logout") HTTPHelper::go($_SESSION['uri']);
        HTTPHelper::go("/dashboard");
    }

    /*
        Destruir la sesión del usuario
        TODO verficar que realmente tenga sentido la línea 52
    */
    public function destroy_session($login=False) {
        $this->reset_session_vars();
        $this->state = False;
        $this->set_session_uri();
        
        $url = (!$login) ? WEB_DIR . "login" : DEFAULT_VIEW;
        exit(HTTPHelper::go($url));
    }
    
    /*
        Establecer la variable de sesión 'uri' empleada para redireccionar 
        al usuario
    */
    private function set_session_uri() {
        $user_module = $u = "/panel/user/";
        $resources = array("{$u}check", "{$u}login", "{$u}logout");

        $uri = $_SERVER['REQUEST_URI'];
        if(in_array($uri, $resources)) $uri = DEFAULT_VIEW;

        $_SESSION['uri'] = $uri;
    }
    
    /*
        Reestablecer todas las variables de sesión a 0
    */
    public function reset_session_vars() {
        $_SESSION['login_date'] = 0;
        $_SESSION['level'] = 0;
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = '';
        $_SESSION['uri'] = DEFAULT_VIEW;
        $_SESSION['user'] = NULL;
    }
    
    /*
        Verificar que el usuario tenga el nivel y permiso necesario
        TODO verificar que la sesión expire de forma correcta
    */
    public function check_state($level=1, $uid=0, $strict=SESSION_STRICT_LEVEL) {
        // si no existe la variable de sesión
        if(!isset($_SESSION['level'])) $_SESSION['level'] = 0;
        // si la variable de sesion level es 0, destruyo la sesión (a loguearse!)
        if(!$_SESSION['level']) $this->destroy_session();

        $is_admin = ($_SESSION['level'] == 1);

        $allow_level = $this->check_level($level, $strict);
        $allow_user = (!$is_admin && $uid) ? $this->check_userid($uid) : True;
        
        // si no tiene nivel o permisos => 403 Forbiden (no destruyo la sesión)
        if(!$allow_level || !$allow_user) HTTPHelper::exit_by_forbiden();
    }

    /*
        Verificar que el nivel del usuario satisfaga el nivel requerido
    */
    public function check_level($required, $strict) {
        if(!isset($_SESSION['level'])) $_SESSION['level'] = 0;
        $actual = $_SESSION['level'];
        $allowed = ($strict) ? ($actual == $required) : ($actual <= $required);
        $admin = ($actual == 1) ? True : False;
        return ($admin or $allowed) ? True : False;
    }

    /*
        Verificar que la ID del usuario sea la ID requerida
    */
    public function check_userid($userid) {
        if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 0;
        return ($_SESSION['user_id'] == $userid) ? True : False;
    }

}


# Compatibilidad para PHP 5.3
function SessionHandler() {
    return new SessionBaseHandler();
}


# Seteo automático de variables de sesión
if(!isset($_SESSION['user_id'])) SessionHandler()->reset_session_vars();
?>
