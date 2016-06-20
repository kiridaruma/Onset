function toggle(){
    $(".edit").slideToggle('fast');
    $(".join").slideToggle('fast');
}

function enterRoom(){
    var enter = $("#enter");
    var nick = enter.find("#nick").val();
    var pass = enter.find("#pass").val();
    var room = enter.find("input[name='room']:checked").val();
    
    if(nick == '' || pass == '' || room == undefined){
        $('#enterNotice').text('空欄があります');
        return;
    }
    $('#enterNotice').text('処理中...');
    
    $.ajax({
        url:"src/login.php",
        type:"POST",
        data:{
            "nick":nick,
            "pass":pass,
            "room":room
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
    var create = $("#create");
    var pass = create.find("#pass").val();
    var room = create.find("#room").val();
    
    if(rand == '' || pass == '' || room == ''){
        $('#createNotice').text('空欄があります');
        return;
    }
    $('#createNotice').text('処理中...');
    
    $.ajax({
        url:"src/createRoom.php",
        type:"POST",
        data:{
            "rand":rand,
            "pass":pass,
            "room":room
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
    var enter = $("#remove");
    var pass = enter.find("#pass").val();
    var room = enter.find("input[name='room']:checked").val();
    
    if(rand == '' || pass == '' || room == undefined){
        $('#removeNotice').text('空欄があります');
        return;
    }
    $('#removeNotice').text('処理中...');
    
    $.ajax({
        url:"src/removeRoom.php",
        type:"POST",
        data:{
            "rand":rand,
            "pass":pass,
            "room":room
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