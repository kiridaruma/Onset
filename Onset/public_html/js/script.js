window.addEventListener('load', function(){
    $.get('src/delLeftRoom.php');
}, false);

function toggle(){
    $(".edit").slideToggle('fast');
    $(".join").slideToggle('fast');
}

function enterRoom(){
    var enter      = $("#enter");
    var playerName = enter.find("#nick").val();
    var roomName   = enter.find("input[name='room']:checked").val();
    var roomPw     = enter.find("#pass").val();

    if(playerName === '' || roomName === undefined || roomPw === ''){
        $('#enterNotice').text('空欄があります');
        return;
    }

    $('#enterNotice').text('処理中...');

    $.ajax({
        url:"src/login.php",
        type:"POST",
        data:{
            "playerName": playerName,
            "roomName"  : roomName,
            "roomPw"    : roomPw
        },
        dataType:"json",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            if(data.status != 1){
                var msg = data.message;
                $('#enterNotice').text(msg);
                return;
            }
            location.href = 'Onset.php';
        }
    });
}

function createRoom(){
    var create   = $("#create");
    var roomName = create.find("#room").val();
    var roomPw   = create.find("#pass").val();

    if(rand === '' || roomName === '' || roomPw === ''){
        $('#createNotice').text('空欄があります');
        return;
    }
    $('#createNotice').text('処理中...');

    $.ajax({
        url:"src/createRoom.php",
        type:"POST",
        data:{
            "roomName" : roomName,
            "roomPw"   : roomPw,
            "rand"     : rand
        },
        dataType:"json",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            if(data.status != 1){
                var msg = data.message;
                $('#createNotice').text(msg);
                return;
            }
            location.reload(true);
        }
    });
}

function removeRoom(){
    var enter    = $("#remove");
    var roomPw   = enter.find("#pass").val();
    var roomName = enter.find("input[name='room']:checked").val();

    if(rand === '' || roomName === undefined || roomPw === ''){
        $('#removeNotice').text('空欄があります');
        return;
    }
    $('#removeNotice').text('処理中...');

    $.ajax({
        url:"src/removeRoom.php",
        type:"POST",
        data:{
            "roomName":roomName,
            "roomPw":roomPw,
            "rand":rand
        },
        dataType:"json",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data){
            if(data.status != 1){
                var msg = data.message;
                $('#removeNotice').text(msg);
                return;
            }
            location.reload(true);
        }
    });
}
