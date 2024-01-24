class Message {

    constructor(){
        this.setHandler();
    }

    setHandler(){

        $('#post_message_btn').on('click', function(){
            const room_id = roomList.currentOpenRoomId;
            const content = $('#content').val();
            if(!room_id || !content){
                return;
            }
            postMessage(room_id, content);
        });

        $('#chat-room').on('loadmessage', (e, data) => {
            $('#chat').empty();

            const room = data.room;
            $('#chat-room-name').html(`<strong>${room.name}</strong>`);
            getMessage(room.id).done(function(data){
                let chatHtml = '';
                for(let i in data){
                    const message = data[i];
                    const user = room.getUser(message.user_id);
                    chatHtml += `
                        <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}">
                            <span class="talk-icon">
                                <img src="${user.image_url}" width="46" height="46"/>
                            </span>
                            <span class="talk-user text-gray">${user.name}</span>
                            <span class="talk-timestamp text-gray">${toDateTime(message.created_at)}</span>
                            <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                                ${replaceNewLineCode(message.content)}
                            </span>
                        </p>
                    `;
                }
                $('#chat').append(chatHtml);
                // チャット画面でスクロールを最下部へ移動
                srcollBottomObj($('#chat'));
            });
        });

        ioSocket.on("update", (data) => {
            this.appendMessage(data)
        });
    }

    appendMessage(data){
        const room = roomList.getRoom(Number(data.room_id));

        // ルームリストのメッセージを更新
        room.latest_message_id = data.id;
        room.latest_message = data.content;
        const $latestMessage = $(`#rooms_${room.id}`).children('.col-auto').children('.latest-message');
        if($latestMessage){
            $latestMessage.text(data.content);
        }

        // 現在開いているルームであるか
        if(roomList.currentOpenRoomId != room.id){
            return;
        }
        
        // チャットルームの更新        
        const datetime = toDateTime(data.created_at);
        const user = room.getUser(data.user_id);
        let chatHtml = '';
        chatHtml += `
            <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}">
                <span class="talk-icon">
                    <img src="${user.image_url}" width="46" height="46"/>
                </span>
                <span class="talk-user text-gray">${user.name}</span>
                <span class="talk-timestamp text-gray">${datetime}</span>
                <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                    ${replaceNewLineCode(data.content)}
                </span>
            </p>
        `;
        $('#chat').append(chatHtml);
        // メッセージを空にする
        $('#content').val('');
        $('#content').focus();
        // チャット画面でスクロールを最下部へ移動
        srcollBottomObj($('#chat'));
    }
}