<?php
/**
* Helper administrativo para validacion y utillerias del sitio y/o panel.
*/

class ToolsHelper {

    static function clean_str( $s ) {
        $s = preg_replace('!\s+!', ' ', trim($s));
        $s = str_replace(array("\t","\r\n","\n","\0","\v"),'', $s);
        $s = htmlentities( strip_tags($s), ENT_QUOTES );
        return $s;
    }

    static function clean_int( $i ){
        return intval(ToolsHelper::clean_str($i));
    }

    static function strlen( $s, $min , $max=0 ) {
        $ok = true;
        $l = mb_strlen($s, "UTF-8");
        $ok = $l >= $min;
        $ok = $max ? ( $ok AND ($l <= $max) ) : $ok;
        return $ok;
    }

    static function alpha_numeric( $input ) {
        return (preg_match("#^[a-zA-ZÀ-ÿ0-9 ]+$#", $input) == 1);
    }

    static function randHash($qtd=5){
        //Under the string $Caracteres you write all the characters you want to be used to randomly generate the code.
        $Caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $QuantidadeCaracteres = strlen($Caracteres);
        $QuantidadeCaracteres--;
        $Hash=NULL;
        for($x=1;$x<=$qtd;$x++){
            $Posicao = rand(0,$QuantidadeCaracteres);
            $Hash .= substr($Caracteres,$Posicao,1);
        }
        return $Hash;
    }

    static function get_ip(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){ //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    static function get_browser(){
        return $_SERVER['HTTP_USER_AGENT'];
    }

    // miau's code
    static function get_protocol(){
      return stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';;
    }

    static function array_insert($array, $position, $insert_array) {
      $first_array = array_splice ($array, 0, $position);
      $array = array_merge ($first_array, $insert_array, $array);
      return $array;
    }

    static function get_numeric($val) {
      if (is_numeric($val)) {
        return $val + 0;
      }
      return 0;
    }

    public static function is_valid_luhn($number) {
      if($number===NULL || $number===""){
        return False;
      }
      settype($number, 'string');
      $sumTable = array(
        array(0,1,2,3,4,5,6,7,8,9),
        array(0,2,4,6,8,1,3,5,7,9));
      $sum = 0;
      $flip = 0;
      for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $sum += $sumTable[$flip++ & 0x1][$number[$i]];
      }
      return $sum % 10 === 0;
    }

    /**
     * Calculate a precise time difference.
     * @param string $start result of microtime()
     * @param string $end result of microtime(); if NULL/FALSE/0/'' then it's now
     * @return flat difference in seconds, calculated with minimum precision loss
     */
    public static function microtime_diff($start, $end = null){
      if (!$end) {
        $end = microtime();
      }
      @list($start_usec, $start_sec) = explode(" ", $start);
      @list($end_usec, $end_sec) = explode(" ", $end);
      $diff_sec = intval($end_sec) - intval($start_sec);
      $diff_usec = floatval($end_usec) - floatval($start_usec);
      return floatval($diff_sec) + $diff_usec;
    }

    /**
     * Encrypt any value
     * @param mixed $value Any value
     * @param string $passphrase Your password
     * @return string
     * */
    public static function encrypt($value, string $passphrase){
      $salt = openssl_random_pseudo_bytes(8);
      $salted = '';
      $dx = '';
      while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
      }
      $key = substr($salted, 0, 32);
      $iv = substr($salted, 32, 16);
      $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
      $data = ["ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt)];
      return json_encode($data);
    }

    /**
     * Decrypt a previously encrypted value
     * @param string $jsonStr Json stringified value
     * @param string $passphrase Your password
     * @return mixed
     * */
    public static function decrypt(string $jsonStr, string $passphrase){
      $jsonStr = html_entity_decode($jsonStr);
      $json = json_decode($jsonStr, true);
      $salt = hex2bin($json["s"]);
      $iv = hex2bin($json["iv"]);
      $ct = base64_decode($json["ct"]);
      $concatedPassphrase = $passphrase . $salt;
      $md5 = [];
      $md5[0] = md5($concatedPassphrase, true);
      $result = $md5[0];
      for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
      }
      $key = substr($result, 0, 32);
      $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
      return json_decode($data, true);
    }

    /**
     * format ms values
     * @param int $tms time in miliseconds
     * @return string HH:MM:SS.000 time format
     * */
    public static function ms_time_formater($tms =0){
      $ms = $tms%1000;
      $t = ($tms - $ms) / 1000;
      $secs = $t%60;
      $t = ($t - $secs)/60;
      $mins = $t%60;
      $hrs = ($t - $mins)/60;

      $string_ms =($ms<100?( $ms<10?'00':'0' ):'').$ms;
      $string_secs = ($secs<10? '0':'').$secs;
      $string_mins = ($mins<10? '0':'').$mins;
      $string_hrs = ($hrs<10? '0':'').$hrs;
      $time = $string_hrs.':'.$string_mins .':'. $string_secs .'.'. $string_ms;

      return $time;
    }

  static function shuffle(&$a, $indexes){
    $N = count($indexes);
    while ($N--) {
      $perm = rand(0, $N);
      $swap = $a[$indexes[$N]];
      $a[$indexes[$N]] = $a[$indexes[$perm]];
      $a[$indexes[$perm]] = $swap;
    }
  }
}
