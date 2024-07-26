<?php
/**
* Modelo para el ABM de trivia the game
*/

class Trivia extends StandardObject {
  public $trivia_id;
  public $registro_id;
  public $trivia_questions;
  public $respuestas;
  public $correctas;
  public $tiempo;
  public $timer;
  public $tiempo_php;
  public $tiempo_start;
  public $tiempo_end;
  public $fecha;
  public $status;
  public $log;


  public function __construct() {
    $this->trivia_id = 0;
    $this->registro_id = 0;
    $this->trivia_questions = '';
    $this->respuestas = '';
    $this->correctas = 0;
    $this->tiempo = '';
    $this->timer = '';
    $this->tiempo_php = '';
    $this->tiempo_start = '';
    $this->tiempo_end = '';
    $this->fecha = '';
    $this->status = 0;
    $this->log = '';
  }

  public function init_trivia($config = array()) {
    if(isset($_SESSION["trivia"]["data"]) ) return;
    $limit = 0;
    foreach($config['stack'] as $val){
      $limit += $val['limit'];
    }
    $trivia = $this->get_trivia($config);

    $ans_verification = array();
    $dict = array();

    foreach($trivia as $key => $val){
      $ans_verification[$key] = array(
        'trivia_id' => $val['trivia_id'],
        'tipo' => $val['tipo'],
        'correcta' => $val['correcta'],
      );
      $dict[$key] = array(
        'TRIVIA_ID' => $val['trivia_id'],
        'TRIVIA_PREGUNTA' => $val['pregunta'],
        'options' => array(),
      );


      $options = $config['options'] ?? array('a','b','c');
      if ($config['shuffle_ans']) shuffle($options);
      foreach($options as $k => $opc){
        $i = $k+1;
        $dict[$key]['options'][] =  array(
          "TRV_OPC" => $val["opc_{$opc}"],
          "TRV_OPC_VAL" => $opc,
        );
      }
    }


    if(!isset($_SESSION['trivia_game']) ) $_SESSION['trivia_game'] = array();

    $_SESSION["trivia_game"]['correctas'] = 0;
    $_SESSION["trivia_game"]['tiempo'] = 0;
    $_SESSION["trivia_game"]['timer'] = '00:00:00';
    $_SESSION["trivia_game"]['tiempo_start'] = microtime();

    $_SESSION["trivia_game"]['verification'] = $ans_verification;
    $_SESSION["trivia_game"]['frontConfig'] = array(
      'config'=>$config['config'],
      'trivia'=>$dict,
    );

    $_SESSION["trivia_game"]['log'] = array(
      'query_result'=>$trivia,
      'total'=>$limit,
      'verification'=>$ans_verification,
      'dict'=>$dict,
    );

  }

  private function get_trivia($config = array(1)){

    foreach($config['stack'] as $key => $stack){
      $type = (int)$stack['type'];
      $limit = (int)$stack['limit'];
      $sql = "SELECT trivia_id,tipo,pregunta, opc_a,opc_b,opc_c,correcta FROM trivia_questions WHERE tipo = ? AND status = 1 ORDER BY RAND() LIMIT ?";
      // var_dump(array($type,$limit));
      $data = array($type,$limit);

      $res[$key] = DBLayer::execute( $sql,$data);
    }
    // var_dump($res);die();

    if (count($res)>1 && $config['shuffle']) {
      $results = self::shuffle_dict_equidistant( $res );
    }else if(count($res) > 1){
      foreach ($res as $arr){
        foreach($arr as $val){
          $results[] = $val;
        }
      }
    }else{
      $results = $res[0];
    }

    return $results;
  }


  private function shuffle_dict_equidistant( $arr = array() ){
    $results = array();
    shuffle($arr);
    $_ref_arr = max($arr);
    foreach ($_ref_arr as $key => $v) {
      foreach ($arr as $arr_item) {
        if ( isset($arr_item[$key]) ) {
          $results[] = $arr_item[$key];
        }
      }
    }
    return $results;
  }


  public function get_trivia_dict() {
    return $_SESSION["trivia_game"]['dict'];
  }

  private function validar_correcta($id, $ans){
    $_k = array_search(strval($id), array_column($_SESSION["trivia_game"]['verification'], 'trivia_id'), false);
    if ($_k !== false) {
      if($_SESSION["trivia_game"]["verification"][$_k]['correcta'] == $ans) $_SESSION["trivia_game"]['correctas']++;
    }
  }


  public function validate_trivia() {
    $resJSON = array("status"=>"fail", "msg"=>"No se guardo en BD.");
    $token = $_SESSION['trivia_game']['token'];
    $post = ToolsHelper::decrypt($_POST['s'], $token);
    if (!isset($post['trivia'])) {
      return $resJSON;
    }
    $resJSON = array("status"=>"ok");

    $triv = $post['trivia'];
    $_SESSION["trivia_game"]['log']['trivia'] = $triv;


    foreach($triv as $key => $val ){
      $_id = (int)$val['trivia_id'];
      $_ans = $val['ans'];
      $_SESSION["trivia_game"]['tiempo'] = $val['tiempo'];
      $_SESSION["trivia_game"]['timer'] = $val['timer'];

      $this->validar_correcta($_id, $_ans);
    }
    return $resJSON;
  }

  public function save(){
    $this->trivia_id = 0;
    $this->registro_id = $_SESSION['registro']['registro_id']??0;
    $this->trivia_questions = serialize($_SESSION["trivia_game"]['verification']);
    $this->respuestas = serialize($_SESSION["trivia_game"]['log']['trivia']);
    $this->correctas = $_SESSION["trivia_game"]['correctas'];
    $this->tiempo = $_SESSION["trivia_game"]['tiempo'];
    $this->timer =  $_SESSION["trivia_game"]['timer'];
    $this->tiempo_start = $_SESSION["trivia_game"]['tiempo_start'];
    $this->tiempo_end = microtime();

    $this->tiempo_php = ToolsHelper::microtime_diff($this->tiempo_start, $this->tiempo_end);


    $this->fecha = date("Y-m-d H:i:s", time());
    $this->status = 1;
    $this->log = serialize($_SESSION["trivia_game"]['log']);
    return parent::save();
  }


  public function unset_all_trivia_sessions() {
    if(isset($_SESSION["trivia_game"])) unset($_SESSION["trivia_game"]);
  }

}

?>
