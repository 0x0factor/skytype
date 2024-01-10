$(function(){
	//詳細スクロール
	var shousai = $('p.shousai');
	//var arrow = $('p.shousai span');
	
	shousai.click(function(){
		var message = $(this).prev('div').children('div.shousai-hide');
		var arrow = $(this).children('span');

		if(message.css('display')=='none'){
			message.slideDown('nomal', function(){arrow.html('▲');});
			
		}else{
			message.slideUp('nomal', function(){arrow.html('▼');});
			
		}
	});

	//スマホ用　地域から探すスクロール
	var load = $('p.load-search-top');
	var box = $('div.search-top');
	var arrow1 = $('p.load-search-top span');
	
	load.click(function(){
		if(box.css('display')=='none'){
			load.css('border-bottom', 'none');
			box.slideDown('nomal', function(){arrow1.html('▲');});
			
		}else{
			box.slideUp('nomal', function(){arrow1.html('▼');});
			load.css('border-bottom', '2px solid #95D78A');
		}
	});

	//地域名で検索　ボタン制御
	var submit_search = $('#searchsubmit');
	var text_search = $('#s');

	if (text_search.val().length == 0) {
		submit_search.attr('disabled', 'disabled');
	}

	text_search.bind('keydown keyup keypress change', function() {
		if ($(this).val().length > 0) {
			submit_search.removeAttr('disabled');
		} else {
			submit_search.attr('disabled', 'disabled');
		}
	});

	

});

//登録フォームの記入漏れチェック
	function checkForm(){

		var text_name = $('input.name').val();
		var text_manager = $('input.manager').val();
		var text_address1 = $('input.address1').val();
		var text_address2 = $('input.address2').val();
		var text_address3 = $('input.address3').val();
		var text_address4 = $('input.address4').val();
		var text_phone = $('input.phone').val();
		var text_mail = $('input.mail').val();

		var check1 = $('input.check1').prop("checked");
		var check2 = $('input.check2').prop("checked");

		var frag_text = true;
		var frag_check = true;

		if(text_name==""){frag_text = false;}
		if(text_manager==""){frag_text = false;}
		if(text_address1==""){frag_text = false;}
		if(text_address2==""){frag_text = false;}
		if(text_address3==""){frag_text = false;}
		if(text_address4==""){frag_text = false;}
		if(text_phone==""){frag_text = false;}
		if(text_mail==""){frag_text = false;}

		if(!check1 && !check2){frag_check = false}

		if(frag_text && frag_check){
			return true;
		}else{
			alert('必須項目を全て入力してください');
			return false;
		}
	}
	
	//登録フォームへスクロール
	var to_form = $('#to-form');
	var form = $('#touroku').position().top;

	to_form.click(function(){
		$('body,html').animate({scrollTop: form}, 500);
		return false;
	});
