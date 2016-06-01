var time = 1;

// get_log ログを取得します。
//
// .chats クラス内に、 .chat クラスを生成。
// .chat クラス内に
// 	<UNIXtime>time chatTime
// 	<UNIXtime>name chatName
// 	<UNIXtime>text chatText
//
// 	をそれぞれ生成します。
//
// 	それぞれのクラス内に、RFC822time, name, textを代入します。
//
function get_log(){

	function ajax(){
		$.ajax({
			// TODO: POSTじゃなくってGETでいいのでは...?
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
					// obj is original JSON data.
					var obj = JSON.parse(data);
					// Compute data each JSON object.
					jQuery.each(obj, function(){
						// チャットデータの多重作成はお断り致し申し上げ奉ります。
						if($('.chat').hasClass(this.UNIXtime) != true) {
							// for DEBUG.
							// console.log(this.name + ' ' + this.RFC822time);
							// console.dir(this);

							// Result: <div class="<UNIXtime> chat"></div>
							var div = $('<div class="' + this.UNIXtime + ' chat"></div>');
							$('.chats').prepend(div);

							// 上記通り。
							// Result: <div class="..."></div>
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'name chatName"></div>');
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'time chatTime"></div>');
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'text chatText"></div>');
							$('.' + this.UNIXtime).append('<div class="' + this.UNIXtime + 'dice chatDice"></div>');

							// 各クラス内に値を代入。
							$('.' + this.UNIXtime + 'time').text(this.RFC2822time);
							$('.' + this.UNIXtime + 'name').text(this.name);
							$('.' + this.UNIXtime + 'text').text(this.text);
							$('.' + this.UNIXtime + 'dice').text(this.diceRes);
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
		$(".notice").html("名前と本文を入力してください");
		return 0;
	}

	if(name.length > 20 || text.length > 300){
		$(".notice").html("文字数が多すぎます");
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
			// textareaの内容を空にする。
			$("#text").val("");

			// 成功したらエラーメッセージもろども消える。
			$(".notice").html("");
			time = 1;
		},
		error: function() {
			// 「素晴らしく運がないな、君は。」
			$(".notice").html('送信に失敗しました。');
		}
	});

	get_log();
}

// Ctrl + Enter押すと上手い子と送信してくれるらしい。
$(function($){
	$("#text").keydown(function(e){
		if(e.ctrlKey && e.keyCode === 13){
			send_chat();
			return false;
		}
	});
});

// checkLoginUser
// Onset.php の上部バーの[ログイン一覧]の処理。
//
// TODO: POST?
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
