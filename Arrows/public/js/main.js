var appInfo = null;
var ioSocket = null;
var authUser = null;
var message = null;
var roomList = null;

$(window).on('load', function(){
    // app
    appInfo = JSON.parse($('#app_info').val());
    // 認証ユーザー設定
    if($('#auth_user').length == 0){
        return;
    }
    const parseAuthUser = JSON.parse($('#auth_user').val());
    if(!parseAuthUser){
        return;
    }
    authUser = new User(parseAuthUser);

    const pathName = location.pathname;
    const path = pathName.substring(1);
    if(path != ''){
        $('#home').removeClass('button-active').addClass('button-inactive');
        $('#' + path).removeClass('button-inactive').addClass('button-active');
    }

    if(typeof io === "undefined"){
        alert('チャットサーバーがダウンしています');
        return;
    }
    ioSocket = io.connect(appInfo.nsocket_server);
    ioSocket.on('connect', function(data) {
        console.log('connect');
    });
    ioSocket.on('disconnect', function() {
        console.log('disconnect');
    });

    roomList = new RoomList();
    message = new Message();
});