var finaltime = 0;

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
        success: function(ret){
            if(ret.code != 1){
                alert("エラーが発生しました\nF5更新をお願いします");
                return;
            }
            ret.data.forEach(function(val, idx, arr){
                var name = $("<span></span>",{text:val.nick + ' ('+val.id+')', class:'chatName'});
                var text = $("<div></div>", {text:val.text, class:'chatText'});
                var dice = $("<div></div>", {text:val.dice, class:'chatDice'});
                var chat = $("<div></div>", {class:'chatObj'}).append(name).append(text).append(dice);
                $("#chatLog").prepend(chat);
                finaltime = ret.data[ret.data.length - 1].time;
            });
            setTimeout(function(){get_log();} , 1000);
        }
    });
}



function send_chat(){
    
    var nick = $("#nick").val().trim();
    var text = $("#text").val().trim();
    var sys = $("#sys").val().trim();

    if(nick === "" || text === ""){
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
