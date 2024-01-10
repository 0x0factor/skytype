jQuery(function( $ ) {
$(function(){
	//new postスライドイン
	var more = $('p.more-link');
	
	more.click(function(){
		var hide = $('.hide');

		if(hide.css('display')=='none'){
			hide.slideDown('slow', function(){more.html('<span class="lsf">up</span>');});
		}else{
			hide.slideUp('slow', function(){more.html('more<br><span class="lsf">down</span>');});
		}
	});
	
	//トップへスクロール
	var toTop = $(".to-top");
	
	toTop.click(function(){
		$('body,html').animate({scrollTop: 0}, 800);
		return false;
		}
	);
	
	//menuスライドイン
	var bt_menu = $('.menu-mobile');
	
	bt_menu.click(function(){
		var menu = $('.nav-inner div');

		if(menu.css('display')=='none'){
			menu.slideDown('normal', function(){bt_menu.html('▲');});
		}else{
			menu.slideUp('normal', function(){bt_menu.html('MENU');});
		}
	});

	//コメント入力欄スライドイン
	var bt_go_input = $(".go-comment-arrow");
	var input = $(".comment-form");

	bt_go_input.click(function(){
		if(input.css("display")=="none"){
			input.slideDown('normal', function(){bt_go_input.html("up");});
		}else{
			input.slideUp('normal', function(){bt_go_input.html("down");});
		}
	});

});
});