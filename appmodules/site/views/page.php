<?php
/**
* Vistas del ABM de Page
*/


class PageView {

    public function show_home(){
        $this->set_imports_page();
        $file = APP_DIR . "appmodules/site/views/templates/home.html";
        $str = file_get_contents($file);
        $html = Template('')->show($str);
        print $html;
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

      $GLOBALS['DICT']['ERROR_MSG'] = (isset($_SESSION["error_msg"]) && $_SESSION["error_msg"] != NULL) ? '<p class="c__error text--center my--5">'.$_SESSION["error_msg"].'</p>' : "";
    }
}

?>