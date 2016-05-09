var time = 1;

function get_log(){

    function ajax(){
        $.ajax({
            url: "src/read.php",
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
                    $(".chats").html(data);
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
        url: "src/write.php",
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
            $("#text").val("");
            var chat = $(".chats").html();
            $(".chats").html("<b>送信中...</b><br>" + chat);
            $(".notice").html("");
            time = 1;
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
                url: '../src/checkLoginUser.php',
                type: 'POST',
                data: {'lock': 'unlock'},
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                }
            });
        }
    });
}
