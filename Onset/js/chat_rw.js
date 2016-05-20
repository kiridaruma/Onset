var time = 1;

function get_log(){

	function ajax(){
		$.ajax({
			url: "src/chatRead.php",
			type: "POST",
			cache: false,
			data: {
				"time": time
			},

			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
			},
			success: function(data){
				if(data != "none"){
					var obj = JSON.parse(data);
					jQuery.each(obj, function(){
						if($('.chat').hasClass(this.UNIXtime) != true) {
							console.log(this.name + ' ' + this.RFC822time);
							console.dir(this);
							var div = $('<div class="' + this.UNIXtime + ' chat"></div>');
							$('.chats').prepend(div);
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'time chatTime"></div>');
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'name chatName"></div>');
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'text chatText"></div>');

							$('.' + this.UNIXtime + 'time').html(this.RFC822time);
							$('.' + this.UNIXtime + 'name').html(this.name);
							$('.' + this.UNIXtime + 'text').html(this.text);
						}
					});
					time = $.now();
				}
				setTimeout(function(){ajax();} , 1000);
			}
		});
	}

	ajax();

}



function send_chat(){

	var name = $("#name").val().trim();
	var text = $("#text").val().trim();
	var sys = $("#sys").val().trim();

	if(name == "" || text == ""){
		$(".notice").html("<b>名前と本文を入力してください</b>");
		return 0;
	}

	if(name.length > 20 || text.length > 300){
		$(".notice").html("<b>文字数が多すぎます</b>");
		return 0;
	}

	$.ajax({
		url: "src/chatWrite.php",
		type: "POST",
		data: {
			"name": name,
			"text": text,
			"sys": sys
		},
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		},
		success: function(){
			$(".notice").html("");
			time = 1;
		},
		error: function() {
			$(".notice").html('送信に失敗しました。');
		}
	});
}

$(function($){
	$("#text").keydown(function(e){
		if(e.ctrlKey && e.keyCode === 13){
			send_chat();
			return false;
		}
	});
});

function checkLoginUser(){
	$.ajax({
		url: 'src/checkLoginUser.php',
		type: 'POST',
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		},
		success: function(data){
			alert(data);
			$.ajax({
				url: 'src/checkLoginUser.php',
				type: 'POST',
				data: {'lock': 'unlock'},
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
				}
			});
		}
	});
}
