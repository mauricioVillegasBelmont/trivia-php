<?php

// menu items
$dashboard_menu = array(
	"TYPE" => "MENU-ITEM",
	"TITLE" => "Dashboard",
	"HREF" => WEB_DIR. 'dashboard',
	"ICON" => "<i class='fa fa-tachometer fa-fw' aria-hidden='true'></i>"
);
$reporte_menu = array(
	"TYPE" => "MENU-ITEM",
	"TITLE" => "Reportes",
	"HREF" => "#",
	"ICON" => "<i class=\"fa fa-line-chart\" aria-hidden=\"true\"></i>",
	"SUBMENU" => array(
		array(
			"TITLE" => "Registro",
			"HREF" => WEB_DIR.'panel/reporting/reporte/registro',
		),
		array(
			"TITLE" => "Game",
			"HREF" => WEB_DIR . "panel/reporting/reporte/game"
		),
	),
);
$user_menu = array(
	"TYPE" => "MENU-ITEM",
	"TITLE" => "Usuarios",
	"HREF" => "#",
	"ICON" => "<i class='fa fa-user fa-fw' aria-hidden='true'></i>",
	"SUBMENU" => array(
		array(
			"TITLE" => "Ver Todos",
			"HREF" => WEB_DIR . "panel/user/listar"
		),
		array(
			"TITLE" => "Agregar",
			"HREF" => WEB_DIR . "panel/user/agregar"
		),
	),
);



$GLOBALS['DICT']['YEAR'] = date("Y");
$GLOBALS['menu'] = array(
	array(
		'href'=>'/',
		'anchor'=>'Home'
	),
);

if(isset($_SESSION['level']) && $_SESSION['level']>0){
    $templ = 'appmodules/panel/views/templates/menu-items.html';
    $menu_arr = array();
    switch($_SESSION['level']){
			case 1:
				$menu_arr[] = 'Administrador';
				$menu_arr[] = $dashboard_menu;
				// $menu_arr[] = $reporte_menu;
				$menu_arr[] = $user_menu;
				break;
			case 2:
				$menu_arr[] = 'Colaborador';
				$menu_arr[] = $dashboard_menu;
				$menu_arr[] = $reporte_menu;
				break;
			default:
			break;
    }
    $GLOBALS['menu'] = MenuMaker($menu_arr, $templ)->render_menu();
		// var_dump($menu_arr);die();
}
?>