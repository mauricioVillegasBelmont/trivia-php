function Pymeweb() {
	this.ready = false;
	this.current_query = 0;
	this.orient = '';
	this.mq = {
		sm: 576,
		md: 768,
		lg: 992,
		xl: 1200,
		xx: 1560
	};
	this.modules = {}
	this.timeout_timers = {};
	this.resize_routines = ['check_mobil']
}
Pymeweb.prototype.check_mobil = function () {
	let check = false;
	(function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|Mobile|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\ -(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
	this.is_mobil = check;
}
Pymeweb.prototype.delay = function (callback, ms, uniqueId) {
	var self = this;
	if (!uniqueId) { uniqueId = "dId"; console.warn('delay function without "uniqueId"') };
	if (this.timeout_timers[uniqueId]) clearTimeout(this.timeout_timers[uniqueId]);
	this.timeout_timers[uniqueId] = setTimeout(callback, ms);
};
Pymeweb.prototype.merge_obj = function (obj1, obj2) {
	var obj3 = {};
	for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
	for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
	return obj3;
}
Pymeweb.prototype.init_ready = function (conf, triggers) {
	var self = this;
	this.ready = true;
	this.conf = conf;
	this.triggers = triggers;
	this.check_mobil();

	this.init_menu();
	this.init_inputs();
	if ($('form').length) {
		this.init_input_validation();
	}

	$(window).on('resize', function (e) {
		if (self.resize_routines.length) {
			self.delay(function () {
				self.resize_routines.forEach(function (value, index, array) {
					if (typeof self[value] === 'function') self[value](e);
				});
			}, 100, "pyme_resize_routines");
		}
	});
}

Pymeweb.prototype.init_load = function () {
	var self = this;
	if (!this.ready) {
		this.init_ready(conf, triggers);
	}
	Object.keys(this.triggers).forEach(function (key) {
		if (typeof self[key] === 'function') { self[key](self.triggers[key]); }
		else { console.warn(key + ' method called at this page') };
	});
}
Pymeweb.prototype.init_menu = function () {
	const sidebarToggle = document.body.querySelector('#sidebarToggle');
	if (sidebarToggle) {
		// Uncomment Below to persist sidebar toggle between refreshes
		// if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
		//     document.body.classList.toggle('sb-sidenav-toggled');
		// }
		sidebarToggle.addEventListener('click', event => {
			event.preventDefault();
			document.body.classList.toggle('sb-sidenav-toggled');
			localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
		});
	}
}

Pymeweb.prototype.init_inputs = function () {
	$('input[type="text"],input[type="mail"]').on("focus", function () {
		$('body').addClass('input-mode');
	});
	$('input[type="text"],input[type="mail"]').on("blur", function () {
		$('body').removeClass('input-mode');
	});

}

Pymeweb.prototype.init_input_validation = function () {
	var self = this;
	if ($('form input[required]').length) {
		$('form input[required], form select[required]').on('input', function (e) {
			e.target.setCustomValidity('');
		});
		$('form input[name="confirm"]').on('change', function (e) {
			if ($('input[name="psw"]').val() == $(this).val()) {
				e.target.setCustomValidity('');
			} else {
				e.target.setCustomValidity('Tu contraseña no coincide.');
			}
		});
		$('input[type=file]').on('input', function (e) {
			var file = $(this).val().replace(/.*(\/|\\)/, '');
			var target = $(this).data('target');
			$('#' + target).text(file);
		})
		$('form input[type="radio"]').on('click', function (e) {
			var name = $(this)[0].name;
			window['ric_' + name] = true;
			$('input[name="' + name + '"]').each(function () {
				$(this)[0].setCustomValidity('');
			});
		});
	}
}

function InvalidMsg(element) {
	// console.log(element.validity);
	if (element.type == 'text') {
		switch (element.name) {
			case 'nombre':
				element.setCustomValidity('Por favor, ingresa tu nombre.');
				break;
			case 'telefono':
				element.setCustomValidity('Por favor, ingresa un numero de telefono valido eg.:(52)55 1234 5678');
				break;
			default:
				element.setCustomValidity('Por favor, ingresa este dato.');
		}
	}
	if (element.type == 'email') {
		element.setCustomValidity('Por favor, ingresa una cuenta de correo válida eg.: nombre@dominio.com');
	}
	if (element.type == 'checkbox') {
		switch (element.name) {
			case 'terminos':
				element.setCustomValidity('Por favor, acepta los términos y condiciones.');
				break;
			default:
				element.setCustomValidity('Por favor, selecciona una casilla.');
		}
	}
	if (element.type == 'psw' && element.name == 'psw') {
		element.setCustomValidity('Tu contraseña deve tener almenos una mayuscula, una minuscula, un numero y un caracter especia.');
	}
	if (element.name == 'estado') {
		element.setCustomValidity('Por favor, selecciona el estado donde resides.');
	}
	if (element.type == 'file') {
		element.setCustomValidity('Por favor, sube un archivo ".jpg" o ".png".');
	}
	if (element.type == 'radio' && !window['ric_' + name]) {
		element.setCustomValidity('Por favor, selecciona una opción.');
	}
	return true;
}

Pymeweb.prototype.init_collectorTable = function (table) {
	for (let i = 0; i < table.length; i++) {
		var _id = table[i].id;
		var _config = table[i].config;
		$(_id).dataTable(_config);
	}
}
Pymeweb.prototype.collectorTable_delete = function (modulo, modelo, id) {
	var _id = id.toString();
	var _modelo = modelo.toString();
	var _modulo = modulo.toString();
	var _action = `/${modulo}/${modelo}/eliminar/${_id}`;
	var pregunta = `¿Está seguro de eliminar el \"${_modelo}\" con registro id: \"${_id}\" del modulo \"${_modulo}\"?`;
	var confirmacion = `El \"${_modelo}\" con registro id: \"${_id}\" ha sido eliminado`;
	Swal.fire({
		text: pregunta,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Eliminar'
	}).then((result) => {
		if (result.isConfirmed) {
			location.href = _action;
		}
	})

}


const pymengine = new Pymeweb();
$(window).on('DOMContentLoaded', function () {
	pymengine.init_ready(conf, triggers);
});
$(window).on('load', function () {
	pymengine.init_load();
});
