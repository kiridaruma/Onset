function get_log(){

	var old_data = $("chat").html();

	function ajax(){
		$.ajax({
			url: "log/xxlogxx.txt",
			type: "POST",
			datatype: "html",
			cache: false,
			success: function(data){
				if(data != old_data){
					$("chat").html(data);
					old_data = data;
				}
				setTimeout(function(){ajax()} , 3000);
			}
		});
	}

	ajax();

}
