function get_log(){

	var time = Math.floor($.now());

	function ajax(){
		$.ajax({
			url: "src/read.php",
			type: "POST",
			datatype: "html",
			cache: false,
			data: {
				"time": time
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
	var name = $("#name").val();
	var text = $("#text").val();

	$.ajax({
		url: "src/write.php",
		type: "POST",
		data: {
			"name": name,
			"text": text
		}
	});
}
