var finaltime = 1;

function get_log(){
    $.ajax({
        url:    "/room/read",
        type:   "POST",
        cache: false,
        data: {
            "time": finaltime
        },
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(result){
            if(result.status === true){
                $(".chats").html(result.data);
                // finaltime = $.now();
            }
            setTimeout(function(){get_log();} , 1000);
        }
    });
}



function send_chat(){
    var playerName  = $("#nick").val().trim();
    var chatContent = $("#text").val().trim();
    var diceSystem  = $("#sys").val().trim();

    if(playerName === "" || chatContent === ""){
        $(".notice").html("<b>名前と本文を入力してください</b>");
        return 0;
    }

    $("#onsetNotice").text('送信中...');

    $.ajax({
        url: "/room/write",
        type: "POST",
        data: {
            "playerName" : playerName,
            "chatContent": chatContent,
            "diceSystem" : diceSystem
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
        url: '/room/users',
        type: 'POST',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            alert(data);
            $.ajax({
                url: '/room/users',
                type: 'POST',
                data: {'lock': 'unlock'},
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                }
            });
        }
    });
}
