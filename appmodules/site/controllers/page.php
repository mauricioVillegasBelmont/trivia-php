<?php
/**
* Controlador para el Home
**/


class PageController extends Controller {

    # ==========================================================================
    #                    Recursos básicos (estándar)
    # ==========================================================================

    public function default() {
        $this->view->show_home();
    }

    public function player_registration($args = ''){

		unset($_SESSION["registro"]);
		$resJSON = array("status"=>"fail", "msg"=>"error.");

		if (
			empty($_POST['nombre']) ||
			empty($_POST['email'])
		) {
			$_SESSION["error_msg"] = 'Por favor. Complete el formulario.';
			HTTPHelper::go("/");
		};


		$reg = new Registro;
		$registered = $reg->user_exist();
		if( $registered ) {
			$_SESSION["error_msg"] = $resJSON['msg'] = 'Este email ya fue registrado';
			HTTPHelper::go("/");
		}

		$reg->save_registration();
		$reg->save_session();

		HTTPHelper::go("/trivia/instrucciones");
	}

    # ==========================================================================
    #                       PRIVATE FUNCTIONS: Helpers
    # ==========================================================================

    private function __test_validar($test=0) {
    }

}

?>