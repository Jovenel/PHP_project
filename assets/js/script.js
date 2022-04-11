let _scrollTop=0;
$(window).on('scroll', function() {
let current =  $(this).scrollTop();
    if (current> _scrollTop) {
      $('.footer').fadeOut();
    } else {
      $('.footer').fadeIn();
    }
    _scrollTop=current;
  });


  $(document).ready(function() {

  var div = $("#divToShowHide");

  var pos = div.position();

  $(window).scroll(function() {
    var windowpos = $(window).scrollTop();
    console.log(pos.top)
    if (windowpos > pos.top && pos.top+500 > windowpos) {
      div.addClass("AfterScroll");
       div.removeClass("BeforeScroll");
     
    } else {
       div.addClass("BeforeScroll");
      div.removeClass("AfterScroll");
     
    }
  });
});