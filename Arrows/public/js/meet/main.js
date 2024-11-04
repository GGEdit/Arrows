var appInfo = null;
var ioSocket = null;
var authUser = null;

$(window).on('load', (event) => {
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

    // Meet起動
    const meet = new Meet();
});