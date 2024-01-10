jQuery(function( $ ) {
$(function(){

	//menu固定
	var nav = $("nav");
	var header = $(".header");
	var nav_pos = nav.position().top;
	
	$(window).scroll(function () {
		if($(this).scrollTop() > nav_pos) {
			nav.css("position", "fixed");
			nav.css("top", "0");
			nav.css("opacity", "0.8");
			$(".nav-inner ul li a").css("padding", "3px 15px");
			$(".menu-mobile").css("padding", "3px 0 1px");
			header.addClass("add-margin");
		} else {
			nav.css("position", "static");
			nav.css("opacity", "1");
			$(".nav-inner ul li a").css("padding", "7px 15px");
			$(".menu-mobile").css("padding", "7px 0 4px");
			header.removeClass("add-margin");
		}
	});
	
});
});