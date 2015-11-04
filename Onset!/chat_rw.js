function get_log(room, key){

	var time = $.now();

	function ajax(){
		$.ajax({
			url: "src/read.php",
			type: "POST",
			datatype: "html",
			cache: false,
			data: {
				"time": time,
				"room": room,
				"key": key
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

function send_chat(room, key){

	var name = $("#name").val().trim();
	var text = $("#text").val().trim();

	if(name == "" || text == ""){
		$("err").html("名前と本文を入力してください");
		return 0;
	}

	if(name.length > 20 || text.length > 300){
		$("err").html("文字数が多すぎます");
		return 0;
	}

	$.ajax({
		url: "src/write.php",
		type: "POST",
		data: {
			"name": name,
			"text": text,
			"room": room,
			"key": key
		},
		success: function(){
			$("#text").val("");
			var chat = $("chat").html();
			$("chat").html("送信中...<br><hr>" + chat);
		}
	});
}
