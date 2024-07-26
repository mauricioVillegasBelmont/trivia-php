<?php
/**
* Vistas del ABM de Page
*/


class TriviaView {

  public function show_page($dict = array('TEMPLATE' => 'home')) {
    $str = $this->get_template_element( 'trivia/' . $dict['TEMPLATE'] . '.html' );
    unset($dict['TEMPLATE']);

    $this->set_imports_page();
    if (isset($dict['LIBS'])) {
      self::set_page_libraries($dict['LIBS']);
      unset($dict['LIBS']);
    }

    if (isset($dict['list'])) {
      foreach ($dict['list'] as $key => $list) {
        $str = Template($str)->render_regex($key, $list);
      }
      unset($dict['list']);
    }
    $html = Template('')->show($str);
    self::tmpl_print($html, $dict);
    print $html;
  }

  // render__

  public function get_template_element( $template_file ){
    $file = APP_DIR . "appmodules/site/views/templates/" . $template_file;
    if (!file_exists($file)) return '';
    return file_get_contents($file);
  }

  public function show_json($resJSON = array("status"=>"fail", "msg"=>"Respuesta por default.")){
    header('Content-Type: application/json');
    print json_encode($resJSON);
  }

  public function tmpl_print(&$str, &$dict){
    if (isset($dict['remove'])) {
      foreach ($dict['remove'] as $element) {
        $str = Template($str)->delete($element);
      }
      unset($dict['remove']);
    }
    if (isset($dict['list'])) {
      foreach ($dict['list'] as $key => $list) {
        $str = Template($str)->render_regex($key, $list);
      }
      unset($dict['list']);
    }
    if (isset($dict['globals'])) {
      foreach ($dict['globals'] as $key => $g_dict) {
        $GLOBALS['DICT'][$key] = $g_dict;
      }
      unset($dict['globals']);
    }
    $GLOBALS['DICT'] = array_merge($GLOBALS['DICT'], $dict);
    $str = Template($str)->render();
  }

  # ==========================================================================
  #                       PRIVATE FUNCTIONS: Helpers
  # ==========================================================================

  private function set_imports_page(){
    $GLOBALS['DICT']['ROBOTS'] = "";
    if(!PRODUCTION){
      $GLOBALS['DICT']['ROBOTS'] ="<meta name=\"robots\" content=\"noindex\" />";
    }
    if(GOOGLE_ANALYTICS){
      $file = APP_DIR . "appmodules/site/views/templates/scripts/google_analytics.html";
      $str = file_get_contents($file);
      $GLOBALS['DICT']['GOOGLE_ANALYTICS'] = $str;
    }
    if(GOOGLE_TM){
      $file = APP_DIR . "appmodules/site/views/templates/scripts/google_tm_head.html";
      $str = file_get_contents($file);
      $GLOBALS['DICT']['GOOGLE_TM_HEAD'] = $str;
      $file = APP_DIR . "appmodules/site/views/templates/scripts/google_tm_body.html";
      $str = file_get_contents($file);
      $GLOBALS['DICT']['GOOGLE_TM_BODY'] = $str;
    }else{
      $GLOBALS['DICT']['GOOGLE_TM_HEAD'] = "";
      $GLOBALS['DICT']['GOOGLE_TM_BODY'] = "";
    }

    $GLOBALS['DICT']['REGISTRATION_NAME'] = isset($_SESSION['PREREGISTRATION']['name']) ? $_SESSION['PREREGISTRATION']['name'] : '';
    $GLOBALS['DICT']['REGISTRATION_MAIL'] = isset($_SESSION['PREREGISTRATION']['mail']) ? $_SESSION['PREREGISTRATION']['mail'] : '';
    $GLOBALS['DICT']['ERROR_MSG'] = (isset($_SESSION["error_msg"]) && $_SESSION["error_msg"] != NULL) ? $_SESSION["error_msg"] : "";


    $GLOBALS['DICT']['MQUERY_DOWN_SM'] =  'max-width: 576px';
    $GLOBALS['DICT']['MQUERY_DOWN_MD'] =  'max-width: 768px';
    $GLOBALS['DICT']['MQUERY_DOWN_LG'] =  'max-width: 992px';
    $GLOBALS['DICT']['MQUERY_UP_SM'] =  'min-width: 576px';
    $GLOBALS['DICT']['MQUERY_UP_MD'] =  'min-width: 768px';
    $GLOBALS['DICT']['MQUERY_UP_LG'] =  'min-width: 992px';

    unset($_SESSION["error_msg"]);
  }

  private function set_page_libraries($lib = array()){
    if (!isset($GLOBALS['DICT']['HEAD_IMPORTS'])) $GLOBALS['DICT']['HEAD_IMPORTS'] = '';
    if (!isset($GLOBALS['DICT']['FOOTER_IMPORTS'])) $GLOBALS['DICT']['FOOTER_IMPORTS'] = '';
    $lib = array_unique($lib);
    foreach ($lib as $code) {
      switch ($code) {
        case 'animate_css':
          $GLOBALS['DICT']['HEAD_IMPORTS'] .= '<link rel="stylesheet" type="text/css" href="{SITE_ASSETS}/css/animate.min.css" />';
          break;
        case 'trivia':
          $GLOBALS['DICT']['HEAD_IMPORTS'] .= '<link rel="prefetch" href="/get_trivia" as="fetch">';
          $GLOBALS['DICT']['FOOTER_IMPORTS'] .= "<script src='{SITE_ASSETS}/js/trivia.bundle.js' charset='utf-8'></script>";
          break;
        default:
          $GLOBALS['DICT']['FOOTER_IMPORTS'] .= '';
          $GLOBALS['DICT']['HEAD_IMPORTS'] .= '';
          break;
      }
    }
  }
}

?>