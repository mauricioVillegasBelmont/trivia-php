import { PECrypto } from './trivia/pecrypto';
import moment from 'moment';
import "jquery.loadtemplate";
import $ from "jquery";


class Timer {
  _emp = null;
  _pse = null;
  _res = null;
  _intervl = 10;
  start() {
    this._emp = moment();
    this.crono = setInterval(()=>this.cronoTimer(), this._intervl);
  }

  pse() {
    this._pse = moment();
    window.clearInterval(this.crono);
  }
  rsm() {
    this._res = moment();
    var __cro__ = this._res.diff(_pse, "ms");
    this._emp.add(__cro__, "ms");
    this.crono = setInterval(()=>this.cronoTimer(), this._intervl);
  }
  end() {
    this.gameover = true;
    window.clearInterval(this.crono);
  }
  cronoTimer() {
    var actual = moment();
    var cro = actual.diff(this._emp, "ms");
    function addZ(n) {
      return (n < 10 ? '0' : '') + n;
    }
    var ms = cro % 1000;
    var s = (cro - ms) / 1000;
    var secs = s % 60;
    s = (s - secs) / 60;
    var mins = s % 60;
    var hrs = (s - mins) / 60;
    var time = addZ(mins) + ':' + addZ(secs) + ':' + addZ(parseInt(ms / 10));
    var _tiempo = time;
    var _temp = time + " - " + cro;
    var _finalTime = addZ(hrs) + 'h' + addZ(mins) + 'm' + addZ(secs) + 's' + addZ(parseInt(ms / 10)) + "ms" + cro + "cro";


    $("input[name='tiempo']").val(cro);
    $("input[name='timer']").val(time);
    $("#timer").html(time);
  }
}



class Trivia{
  #timer = new Timer();
  #data = [];
  gameover = false;
  constructor(config){
    var self = this;
    $.extend( true, this, config );
    this.config.token = $('#trivia').data('token');
    this.config.action = $('#trivia').attr('action');
    this.config.method = $('#trivia').attr('method');


    jQuery.fn.showModal = function() {
      const el = $(this);
      if (el.is('dialog')) {
          el[0].showModal();
      }
      return el;
    };


    $('#trivia').submit(function(e){
        e.preventDefault();
        const data = $(this).serializeArray()
        self.setData(data);
        self.#logic();
    });
    this.#logic();
    this.visual_start();
    this.#timer.start()
  }
  #logic(){
    this.clearTrivia();
    this.disableSubmit()
    if (this.trivia.length) {
      return this.renderTrivia();
    }
    this.#finish()
  }
  visual_start(){
    $(".trivia__visualstarter").addClass("start");
  }
  visual_end(){
    $(".trivia__visualstarter").removeClass("start");
  }
  disableSubmit(){
    $('[type="submit"]').addClass('disabled').prop( "disabled", true );
  }
  resolveTemplate(id) {
    return $(id).contents();
  }
  clearTrivia(){
    $('.trivia__wrapper').empty();
  }
  renderTrivia(){
    const question = this.trivia[0];
    question['options'].forEach((option,index) => {
      option.ID = `opc_${(new Date().getTime()).toString(36)}-${index}}`
    });
    $("#trivia__request").loadTemplate($("#trivia__question"), question );
    $("#trivia__answers").loadTemplate($("#trivia__field"), question['options'] );

    $('input:radio[name="ans"]').off('change').on('change', function(){
      $('[type="submit"]').removeClass('disabled').prop( "disabled", false );
    })

  }
  setData(data){
    let _data = {};
    for (const item of data) {
      var name = item['name'];
      var value = item['value'];
      if (name === 'trivia_id' || name === 'tiempo') {
        value = Number.parseInt(value);
      }
      _data[name] = value;
    }
    this.#data.push(_data);
    this.trivia.shift();
  }
  #finish(){
    const conf = this.config;
    const ans = this.#data;
    const _data = {s: PECrypto.encrypt({trivia: ans }, conf.token)};
    this.#timer.end();
    this.visual_end();
    $.ajax({
      method: conf.method.toUpperCase(),
      url: conf.action,
      data:  _data,
      cache: false,
      dataType: 'json',
      beforeSend: function() {
        $('#loader').showModal();
      }
    })
    .always(function() {
      $(window).unbind("beforeunload").off("beforeunload");
    })
    .done( (response) =>{
      if (response.status == "ok") {
        return (window.location.href = response.redirect);
      }
      window.alert("Hubo un problema. Por favor, pongase en contacto con el administrador.");
      return window.location.href = "/";
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.log( "error", jqXHR, textStatus, errorThrown );
      alert('Algo salio mal. Porfavor, pongase en contacto con el administrador.')
    })


  }
}


$.get( "/get_trivia")
.done(function(data ) {
  const token = $('#trivia').data('token');
  const trivia =  PECrypto.decrypt(data,token);
  const t = new Trivia(trivia);
  $(window).bind("beforeunload", function () {
    if (!t.gameover) {
      return "¿Seguro que quieres irte?";
    }
  })
})
.fail(function(data ) {
  new Error("HTTP error " + data.status);
})