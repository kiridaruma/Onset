function get_log(){
	$.ajax({
		url: "src/chatcheck.php",
		type: "POST",
		datatype: "html",
		cache: false,
		success: function(data){
			if(data != "none"){
				$("chat").html(data);
			}
			setTimeout(get_log , 1000);
		}
	});
}
