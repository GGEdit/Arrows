var http = require('http');
var socketio = require('socket.io');

function is_json(data) {
	try {
		JSON.parse(data);
	} catch (error) {
		return false;
	}
	return true;
}

function emit_update_chat(data){
    io.to(data.room_name).emit('update', data.message)
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
                if(req.url === '/emit_update_chat'){
                    emit_update_chat(data);
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

io.sockets.on('connection', function(socket){
    // 接続したユーザーがルームにjoinする
    socket.on('join', (data) => {
        for(let i in data.room_ids){
            socket.join(data.room_ids[i]);
        }
    });

    socket.on('disconnect', () => {

    });
});