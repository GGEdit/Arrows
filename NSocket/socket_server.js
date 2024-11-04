var http = require('http');
var socketio = require('socket.io');
var axios = require('axios');

function is_json(data) {
	try {
		JSON.parse(data);
	} catch (error) {
		return false;
	}
	return true;
}

function emitCreateChat(data){
    io.to(data.room_name).emit('create', data.message)
}

function emitUpdateChat(data){
    io.to(data.room_name).emit('update', data.message)
}

function emitDeleteChat(data){
    io.to(data.room_name).emit('delete', data)
}

function emitNotifyConference(data){
    io.to(data.room_name).emit('notify_conference', data.meet)
}

function emitNotifyTerminateConference(data){
    io.to(data.room_name).emit('notify_terminate_conference', data.meet)
}

function requestListener(req, res){
    let resData = '';
    res.writeHead(200, {
        'Content-Type':'text/html'
    });
    req.on('data', function(chunk){
        resData += chunk;
    }).on('end', function(){
        try{
            if(!is_json(resData)){
                return;
            }
            // LaravelからPOSTされたデータを処理する
            const data = JSON.parse(resData);
            if(req.method === 'POST'){
                if(req.url === '/emit_create_chat'){
                    emitCreateChat(data);
                }
                else if(req.url === '/emit_update_chat'){
                    emitUpdateChat(data);
                }
                else if(req.url === '/emit_delete_chat'){
                    emitDeleteChat(data);
                }
                else if(req.url === '/emit_notify_conference'){
                    emitNotifyConference(data);
                }
                else if(req.url === '/emit_notify_terminate_conference'){
                    emitNotifyTerminateConference(data);
                }
            }
        }
        catch(e){
            console.log(e);
        }
    });
    res.end();
}

var server = http.createServer(requestListener).listen(3000);
var io = socketio(server, {
    cors: {
        origin: '*',
    }
});

const join_meets = [];

io.sockets.on('connection', function(socket){

    // 接続したユーザーがルームにjoinする
    socket.on('join', (data) => {
        for(let i in data.room_ids){
            socket.join(data.room_ids[i]);
        }
    });

    // Meetに接続したユーザーをMeet用ルームにjoinする
    socket.on('join_meet', (data) => {
        socket.join(data.meet_name);

        // 参加ユーザーを記録
        join_meets[socket.id] = {
            'api_token' : data.api_token,
            'user_id' : data.user_id,
            'room_id' : data.room_id,
            'meet_name' : data.meet_name,
        };
    });

    socket.on('disconnect', async () => {
        console.log('disconnected');

        if(!join_meets[socket.id]){
            return;
        }
        // 退室したことを通知する
        try {
            const payload = {
                api_token: join_meets[socket.id].api_token,
                user_id: join_meets[socket.id].user_id,
                room_id: join_meets[socket.id].room_id,
                meet_name: join_meets[socket.id].meet_name,
            };
            const response = await axios.post("http://localhost/api/meet/notify_terminate_conference", 
                payload,
                {
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    }
                }
            );
        } catch (error) {
            console.error("エラー:", error);
        }
    });

});