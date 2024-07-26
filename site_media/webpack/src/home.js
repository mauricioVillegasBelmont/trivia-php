import "../../node_modules/slick-slider/slick/slick.scss";

// import $ from 'jquery';
// global.jQuery = global.$ = $;
import "slick-slider";

const home_slider_config = {
  autoplay: true,
  slidesToShow: 1,
  slidesToScroll: 1,
  // centerMode: false,
  nextArrow: "#btn-next",
  prevArrow: "#btn-prev",
};
const jq2 = jQuery.noConflict();
jq2(function ($) {
  $("#homeslider").slick(home_slider_config);
});
