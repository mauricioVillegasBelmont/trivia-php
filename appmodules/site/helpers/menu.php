<?php
class MenuMaker {

	public $menu_single_template;
	public $menu_collapse_template;
	public $menu_title_template;
	public $submenu_item;
	public $menu;
	public $arr;
	public $dict;
	public $base_templates;


	function __construct($arr = array(), $templ = 'appmodules/panel/views/templates/menu-items.html'){
		$this->base_templates = file_get_contents(APP_DIR . $templ);

		$this->menu_single_template = Template($this->base_templates)->get_substr('MENU-ITEM');;
		$this->menu_collapse_template = Template($this->base_templates)->get_substr('MENU-ITEM-COLLAPSE');;
		$this->menu_title_template = Template($this->base_templates)->get_substr('MENU-TITLE');;
		$this->submenu_item = Template($this->base_templates)->get_substr('SUBMENU');
		$this->menu = array();
		$this->arr = $arr;
		$this->dict = array(
			"TITLE" => "",
			"HREF" => "#",
			"ICON" => "",
			"SUBMENU" => "",
		);
	}

  function render_menu() {
    $menu = array();
    foreach ($this->arr as $item) {
			if (gettype($item) == 'array') {
				self::nav_element($item);
			}else{
				self::title_element($item);
			}
    }
    return $this->menu;
  }
	private function nav_element($menu_item = array()){
		$dict = $this->dict;
		if (array_key_exists("SUBMENU", $menu_item)) {
			$_tmpl =  $this->menu_collapse_template;
		} else {
			$_tmpl =  $this->menu_single_template;
		}
		foreach ($menu_item as $key => $value) {
			if ($key == 'SUBMENU') {
				$dict['ID_SUBMENU'] = strtolower($key) . '-' . ToolsHelper::randHash(5);
				$dict[$key] = Template($this->submenu_item)->render_regex('SUBMENU-ITEM', $value);
			} else {
				$dict[$key] = $value;
			}
		}
		$tmpl = Template($_tmpl)->render($dict);
		$this->menu[] = array('ITEM' => $tmpl);
	}
	private function title_element($menu_item = ''){
		$dict['TITLE'] = $menu_item;
		$tmpl = Template($this->menu_title_template)->render($dict);
		$this->menu[] = array('ITEM' => $tmpl);

	}
}

function MenuMaker($arr = array(), $templ = 'appmodules/panel/views/templates/menu-items.html'){
	return new MenuMaker($arr, $templ	);
}
