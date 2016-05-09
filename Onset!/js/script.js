function toggle(){
    $(".form").slideToggle('fast');
    $("#edit").slideToggle('fast');
    if($("#toggle").text() != '閉じる'){$("#toggle").text('閉じる');}
    else{$("#toggle").text('部屋の作成/削除');}
}
