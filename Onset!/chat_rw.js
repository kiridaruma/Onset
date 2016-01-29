function get_log(){

	var time = $.now();

	function ajax(){
		$.ajax({
			url: "src/read.php",
			type: "POST",
			datatype: "html",
			cache: false,
			data: {
				"time": time
			},

			beforeSend: function(xhr) {
				xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
			},
			success: function(data){
				if(data != "none"){
					$("chat").html(data);
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

	if(name == "" || text == ""){
		$("err").html("<b>名前と本文を入力してください</b>");
		return 0;
	}

	if(name.length > 20 || text.length > 300){
		$("err").html("<b>文字数が多すぎます</b>");
		return 0;
	}

	$.ajax({
		url: "src/write.php",
		type: "POST",
		data: {
			"name": name,
			"text": text
		},
		beforeSend: function(xhr) {
			xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		},
		success: function(){
			$("#text").val("");
			var chat = $("chat").html();
			$("chat").html("<b>送信中...</b><br><hr>" + chat);
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
