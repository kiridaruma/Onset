var finaltime = 1;

function get_log(){
    
    $.ajax({
        url: "src/read.php",
        type: "POST",
        cache: false,
        data: {
            "time": finaltime
        },
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            if(data != "none"){
                $(".chats").html(data);
                finaltime = $.now();
            }
            setTimeout(function(){get_log();} , 1000);
        }
    });
}



function send_chat(){
    
    var nick = $("#nick").val().trim();
    var text = $("#text").val().trim();
    var sys = $("#sys").val().trim();

    if(nick == "" || text == ""){
        $(".notice").html("<b>名前と本文を入力してください</b>");
        return 0;
    }
    $("#onsetNotice").text('送信中...');
    
    $.ajax({
        url: "src/write.php",
        type: "POST",
        data: {
            "nick": nick,
            "text": text,
            "sys": sys
        },
        dataType:"json",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            if(data.status == -1){
                var msg = data.message;
                $("#onsetNotice").text(msg);
                return;
            }
            $("#onsetNotice").text('');
            $("#text").val('');
            finaltime = 1;
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
