$(window).on('load', function() {
    var winH = $(window).height() - 103;
    $('#chat-room').outerHeight(winH);
    $('.room-list').outerHeight(winH);
    
    // チャット画面でスクロールを最下部へ移動
    srcollBottomObj($('#chat'));

    $(window).on('resize',function(){
        winH = $(window).height() - 103;
        $('#chat-room').outerHeight(winH);
        $('.room-list').outerHeight(winH);
        // チャット画面でスクロールを最下部へ移動
        srcollBottomObj($('#chat'));
    });
});