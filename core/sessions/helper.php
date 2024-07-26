<?php


class SessionHelper {

    public static function get_user() {
        $u = "";
        if(isset($_POST['user'])) {
            $u = htmlentities(strip_tags($_POST['user']), ENT_QUOTES);
        }
        return $u;
    }

    public static function get_pwd() {
        $p = "";
        if(isset($_POST['pwd'])) {
            if(defined('SECURITY_LAYER_ENCRYPT_PASSWORD')) {
                if(!SECURITY_LAYER_ENCRYPT_PASSWORD) {
                    $p = md5(EuropioCode::reverse($_POST['pwd']));
                } else {
                    $p = $_POST['pwd'];
                }
            }
        }
        return $p;
    }

    public static function set_query() {
        $sql = "SELECT user_id, name, lastname, user, email, level, created, last_login, active FROM user
                WHERE (user = ? OR email = ?) AND pwd = ?";
        return $sql;
    }

    public static function set_salt_query() {
        $sql = "SELECT salt FROM user WHERE (user = ? OR email = ?)";
        return $sql;
    }

    public static function set_last_login_query(){
        $sql = "UPDATE user SET last_login = ? WHERE user_id = ?";
        return $sql;
    }
}

?>
