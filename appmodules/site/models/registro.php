<?php
/**
* Modelo para el ABM de Registro
*/

class Registro extends StandardObject {
  public $registro_id;
  public $nombre;
  public $email;
  public $registrar;


  public $fecha;
  public $status;
  public $ip;
  public $browser;

  public function __construct() {
    $this->registro_id = 0;
    $this->nombre = '';
    $this->email = '';
    $this->registrar = 'trivia_demo';



    $this->fecha = '';
    $this->status = 1;
    $this->ip = '';
    $this->browser = '';
  }

  function save() {
    if($this->registro_id == 0)  parent::save() ;
  }

  function update(){
    if($this->registro_id > 0){
      parent::save();
      $this->save_session();
    }
  }

  public function save_registration(){
    // $digits = 5;
    // $rand_num = rand(pow(10, $digits-1), pow(10, $digits)-1);
    // $data_format = 'Ymd-His';

    $this->nombre = ToolsHelper::clean_str($_POST["nombre"]);
    $this->email = ToolsHelper::clean_str($_POST["email"]);


    /* ------------- default --------------- */
    $this->fecha = date("Y-m-d H:i:s", time());
    $this->ip = ToolsHelper::get_ip();
    $this->browser = ToolsHelper::get_browser();

    self::save();
  }

  public function save_session(){
    $_SESSION["registro"] = (array)$this;
  }

  public function get_session(){
    if(!isset($_SESSION["registro"])) HTTPHelper::go("/registro");
    $temp = $_SESSION["registro"];
    $this->registro_id = $temp->registro_id;
    $this->get();
    return $_SESSION["registro"];
  }

  public function user_exist(){
    $email = isset($_POST["email"])? ToolsHelper::clean_str($_POST["email"]): "";
    $sql = "SELECT registro_id FROM registro WHERE email LIKE ? LIMIT 0, 1";
    $results = DBLayer::execute($sql, array($email));
    if(count($results)>0){
      $this->registro_id = $results[0]['registro_id'];
      $this->get();
      return True;
    }else{
      return False;
    }
  }
  public function verify_register_by_value($key = 'registro_id',$value = 1, $exist = true){
    $sql = "SELECT registro_id FROM registro WHERE ? LIKE ? LIMIT 0, 1";
    $results = DBLayer::execute($sql, array($key,$value));
    if(count($results)>0){
      $this->registro_id = $results[0]['registro_id'];
      $this->get();
      if($exist) return True;
    }
    if($exist) return False;
  }

  public function get_list_count(){
    $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m-%d') AS dia, COUNT(*) AS n FROM registro group by (dia)";
    $results = DBLayer::execute($sql, array());
    if(count($results)>0){
      return $results;
    }else{
      return array();
    }
  }
  public function get_count_total(){
    $sql = "SELECT COUNT(*) AS 'n' FROM registro";
    $results = DBLayer::execute($sql, array());
    return $results[0]['n'];
  }

  function check_registro(){
    $email = isset($_POST['email'])?ToolsHelper::clean_str($_POST['email']):'';
    $sql = "SELECT email FROM registro WHERE email = ? AND pwd = ?";
    $result = DBLayer::execute($sql, array($email));
    if(count($result) > 0) {
        return True;
    } else {
        return False;
    }
  }
}

?>