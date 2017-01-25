
/*
 * jQuery.Deferred call(string srcPoint);
 * src/フォルダ以下のsrcPointのパスにアクセスする
 * dataObjは送信するデータのオブジェクト
 * jQueryのajax関数の返り値(jQuery.Deferred)を返します
 * src/以下のAPIにアクセスする場合は、call()を経由して行ってください
 */
function call(srcPoint = '', dataObj = {}){
    return $.ajax({
        url: "src/"+srcPoint+".php",
        type: 'POST',
        cache: false,
        data: dataObj,
        dataType: 'json',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        }
    });
}

function delLeftRoom(){
    call('delLeftRoom')
    .done(function(ret){
        console.log(ret.message);
    });
}

function toggle(){
    $(".edit").slideToggle('fast');
    $(".login").slideToggle('fast');
}

function enterRoom(){
    var enter = $("#enter");
    var nick = enter.find("#nick").val();
    var pass = enter.find("#pass").val();
    var room = enter.find("input[name='room']:checked").val();
    
    if(nick === '' || pass === '' || room === undefined){
        $('#enterNotice').text('空欄があります');
        return;
    }
    $('#enterNotice').text('処理中...');
    
    call('login', {"nick":nick, "pass":pass, "room":room})
    .done(function(data){
            if(data.status != 1){
                var msg = data.message;
                $('#enterNotice').text(msg);
                return;
            }
            location.href = 'Onset.php';
    });
}

function createRoom(){
    var create = $("#create");
    var pass = create.find("#pass").val();
    var room = create.find("#room").val();
    var rand = create.find("#create_rand").val();
    if(rand === '' || pass === '' || room === ''){
        $('#createNotice').text('空欄があります');
        return;
    }
    $('#createNotice').text('処理中...');
    
    call('createRoom', {"rand":rand, "pass":pass, "room":room, "rand":rand})
    .done(function(data){
        if(data.status != 1){
            var msg = data.message;
            $('#createNotice').text(msg);
            return;
        }
        location.reload(true);
    });
}

function removeRoom(){
    var remove = $("#remove");
    var pass = remove.find("#pass").val();
    var room = remove.find("input[name='room']:checked").val();
    var rand = remove.find("#remove_rand").val();
    if(rand === '' || pass === '' || room === undefined){
        $('#removeNotice').text('空欄があります');
        return;
    }
    $('#removeNotice').text('処理中...');
    
    call('removeRoom', {"rand":rand, "pass":pass, "room":room, "rand":rand})
    .done(function(data){
        if(data.status != 1){
            var msg = data.message;
            $('#removeNotice').text(msg);
            return;
        }
        location.reload(true);
    });
}

function get_log(finaltime = 0.0){
    call('read', {"time": finaltime}).done(function(ret){
        if(ret.status != 1){
            //アプリケーションエラー処理
            return;
        }
        ret.data.forEach(function(val, idx, arr){
            var name = $("<span></span>",{text:val.nick + ' ('+val.id+')', class:'chat-nick'});
            var text = $("<div></div>", {text:val.text, class:'chat-text'});
            text.html( text.html().replace("\n", "<br />") );
            var dice = $("<div></div>", {text:val.dice, class:'chat-dice'});
            var chat = $("<div></div>", {class:'chat-obj'}).append(name).append(text).append(dice);
            chat.hide().prependTo("#chatLog").fadeIn(500);
        });
        if(ret.data.length != 0) finaltime = ret.data[ret.data.length - 1].time;
        $("#onsetNotice").text('');
    }).fail(function(){
        //通信エラー処理
    }).always(function(){
        setTimeout(function(){get_log(finaltime);} , 1000);
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

    call("write", {"nick": nick, "text": text, "sys": sys})
    .done(function(data){
        if(data.status == -1){
            var msg = data.message;
            $("#onsetNotice").text(msg);
            return;
        }
        $("#text").val('');
    }).fail(function(){
        $("#onsetNotice").text('通信エラー、再送信をお願いします');
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
    call('checkLoginUser')
    .done(function(data){
        alert(data.message);
        call('checkLoginUser', {'lock': 'unlock'});
    });
}

