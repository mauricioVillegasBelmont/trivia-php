<?php
/**
* Controlador para trivia
**/


class TriviaController extends Controller {
  private $config = array(
    'redirect' => '/trivia/gracias',
  );
  private $trivia_config = array(
    'options' => array('a','b','c'),
    'config' =>array(
      'countdown' => false,
    ),
    'stack' => array(
      array(
        'type' => 1,
        'limit' => 2,
      )
    ),
    'shuffle' => true,
    'shuffle_ans' => true,
  );



  public function redirect(){ HTTPHelper::go("/"); }

  public function get_trivia(){
    $resJSON = array("status"=>"fail", "msg"=>"fail");
    if ( isset($_SESSION['registro']) and isset($_SESSION["trivia_game"]) ){
      $token = $_SESSION['trivia_game']['token'];
      $frontConfig = $_SESSION["trivia_game"]['frontConfig'];
      $resJSON = ToolsHelper::encrypt($frontConfig, $token );
    }
    $this->view->show_json($resJSON);
   }

  public function game_manager($args = array('juego')){
		if ( !isset($_SESSION['registro']) ) {
      return HTTPHelper::go("/participar");
		}

		// $args[ 0 ] => ID de juego: trivia
		// $args[ 1 ] => step: instrucciones | juego | gracias

    $__game_steps = array(
      'instrucciones',
      'juego',
      'gracias',
    );
    if ( !isset($_SESSION['trivia_progress']) || $_SESSION['trivia_progress'] === null ) {
      $_SESSION['trivia_progress'] = $__game_steps;
		}
    self::clean_page_args($args);
    if (count($args) < 2 or count($args) > 2) {
      return HTTPHelper::go("/");
    }
    if (  !in_array($args[1], $__game_steps) ) {
			return HTTPHelper::go("/");
    }

		if ( empty($_SESSION['trivia_progress']) ) {
      self::__unset_trivia();
      unset($_SESSION['trivia_progress']);
			unset($_SESSION['registro']);
			return HTTPHelper::go("/");
		}
		if (PRODUCTION) {
			/* FUERZA AL USUARIO A QUE LLEVE UNA CONTINUIDAD */
			if($args[1] != $_SESSION['trivia_progress'][0]) {
				HTTPHelper::go('/'. $args[0] .'/'. $_SESSION['trivia_progress'][0] );
				return;
			}
			array_shift($_SESSION['trivia_progress']);
		}


		$dict = array();

    $func = 'get_trivia_'.$args[1];
		$this->$func($dict);
		$dict['URL_ID'] = $args[0];
    $dict['remove'][] = 'TO_LANDSCAPE';
    $dict['CHEV--MASK'] = $this->view->get_template_element('elements/chev-mask.svg');


		$this->view->show_page($dict);
	}

  public function save_trivia(){
    $resJSON = array("status"=>"fail", "msg"=>"No se guardo en BD.");
    if( isset($_POST['s']) || is_string($_POST['s'])){
      $resJSON = $this->model->validate_trivia();
      // $resJSON['status'] = 'ok';
      $resJSON['redirect'] = $this->config['redirect'];
	  }
		$this->view->show_json($resJSON);
    $this->model->save();
	}


  # ==========================================================================
	#                       PRIVATE FUNCTIONS: Helpers
	# ==========================================================================
  private function clean_page_args(&$args = ''){
    if (is_array($args)) {
      $args = array_values(array_filter($args));
    } else if (is_string($args)) {
      $args = explode('/', $args);
      if (empty($args[0])) {
        $args[0] = 'home';
      }
    }
	}


  private function get_trivia_instrucciones(&$dict){
    $dict['TEMPLATE'] = 'trivia.instrucciones';
		$dict['LIBS'][] = 'page';
  }

  private function get_trivia_juego(&$dict){
		$dict['BODY_CLASSES'] = 'bg1';
		$this->model->unset_all_trivia_sessions();
    $config = $this->trivia_config;
    $this->model->init_trivia($config);
    $dict = array();
    $dict['TEMPLATE'] = 'trivia.juego';
    $dict['TRIVIA_TOKEN'] = $_SESSION['trivia_game']['token'] = ToolsHelper::randHash(5);
    $dict['LIBS'][] = 'trivia';
	}
  private function get_trivia_gracias(&$dict){
    $dict['TEMPLATE'] = 'trivia.gracias';
    $dict['LIBS'][] = 'page';
    $dict['CORRECTAS'] =$_SESSION["trivia_game"]['correctas']??0;
    $dict['TIMEPO'] = $_SESSION["trivia_game"]['timer']??0;
    self::__unset_trivia();
    unset($_SESSION['registro']);
  }

	/* PRIVATE TRIVIA */
	private function __unset_trivia(){
    $this->model->unset_all_trivia_sessions();
	}


}