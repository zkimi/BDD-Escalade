$(document).ready(function(){
var touch   = $('#resp-menu');
var menu    = $('.menu');

$(touch).on('click', function(e) {
    e.preventDefault();
    menu.slideToggle();
});

$(window).resize(function(){
    var w = $(window).width();
    if(w > 767 && menu.is(':hidden')) {
        menu.removeAttr('style');
    }
});

});

$(".modal-trigger").click(function(e){
  e.preventDefault();
  dataModal = $(this).attr("data-modal");
  $("#" + dataModal).css({"display":"block"});
  // $("body").css({"overflow-y": "hidden"}); //Prevent double scrollbar.
});

$(".close-modal, .modal-sandbox").click(function(){
  $(".modal").css({"display":"none"});
  // $("body").css({"overflow-y": "auto"}); //Prevent double scrollbar.
});
