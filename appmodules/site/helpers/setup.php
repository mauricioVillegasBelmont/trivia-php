<?php
/**
* Helper administrativo para instalacion y configuracion de sitio y/o panel.
*/

class SetupHelper {
    
    static function install_users() {
        $rootpass = "2..1qaz2wsx!";
        $hash = SECURITY_LAYER_ENCRYPT_PASSWORD_HASH;
        $rootpass = hash($hash, EuropioCode::reverse($rootpass));
        if( function_exists("mcrypt_create_iv") ) {
            $salt = mcrypt_create_iv(64, MCRYPT_DEV_URANDOM);
        } else {
            $salt = random_bytes(64);
        }
        $strange = $rootpass . $salt;
        $pwd = hash('sha256', $strange); 
        $sql = "SELECT user_id FROM user WHERE level=1";
        $result = DBLayer::execute($sql);
        if( $result ) {
            $uid = $result[0]['user_id'];
            $sql = "UPDATE user SET pwd=?, salt=? WHERE user_id=?";
            DBLayer::execute( $sql, array( $pwd, $salt, $uid ) );
            echo "Update Done";
        } else {
            $data = array("root",$pwd,$salt,"1","1");
            $sql = "INSERT INTO user (user, pwd, salt, level, active) VALUES (?,?,?,?,?)";
            DBLayer::execute($sql, $data);
            echo "Create Done";
        }
    }//END static install_users    
}