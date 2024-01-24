var ioSocket = null;
var authUser = null;
var message = null;
var roomList = null;

$(window).on('load', function(){
    // 認証ユーザー設定
    const parseAuthUser = JSON.parse($('#auth_user').val());
    if(!parseAuthUser){
        return;
    }
    authUser = new User(parseAuthUser);

    ioSocket = io.connect('http://localhost:3000');
    ioSocket.on('connect', function(data) {
        console.log('connect');
    });
    ioSocket.on('disconnect', function() {
        console.log('disconnect');
    });

    message = new Message();
    roomList = new RoomList();
});