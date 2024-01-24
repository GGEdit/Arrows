class Message {

    constructor(){
        this.deviceType = null;
        $('.input-area').hide();
        this.setHandler();
        $(window).trigger('refresh');
    }

    setHandler(){

        $(window).on('refresh resize', (e)=> {
            // サイズ判定
            const ww = $(window).width();
            let regHeight = 0;
            if(ww >= 768){
                this.deviceType = DeviceType.PC;
                regHeight = 100;
            }
            else{
                this.deviceType = DeviceType.MOBILE;
                regHeight = 160;
            }

            const winH = $(window).height() - regHeight;
            $('#chat-room').outerHeight(winH);
            $('.room-list').outerHeight(winH);
            
            // チャット画面でスクロールを最下部へ移動
            srcollBottomObj($('#chat'));

            this.showWindow();
        });

        $('#back-button').on('click', (e) => {
            roomList.currentOpenRoomId = null;
            $('#chat').empty();
            $('#chat-room-name').text(`チャットルームを選択してくだい`);
            $('.input-area').hide();
            $('.room-selected').removeClass('room-selected');
            $(window).trigger('refresh');
        });

        $('#attachment').on('change', (e) => {
            const [file] = e.target.files;
            if(file){
                $('#attachment-preview').attr('src', URL.createObjectURL(file));
                $('#attachment-preview').show();
                $('.input-area').css('height', '260px');
            }
            else{
                $('#attachment-preview').hide();
                $('.input-area').css('height', '195px');
            }
        });

        $('#attachment-button').on('click', (e) => {
            $('#attachment').click();
        });

        $('#post_message_btn').on('click', (e)=>{
            this.postMessage();
        });

        $('#content').on('keydown', (e) => {
            if(e.shiftKey && e.keyCode === 13){
                this.postMessage();
                return false;
            }
        });

        $('#chat-room').on('loadmessage', (e, data) => {
            $('#chat').empty();

            const room = data.room;
            $('#chat-room-name').text(`${room.name}`);
            $('.input-area').show();
            getMessage(room.id).done(function(data){
                let chatHtml = '';
                for(let i in data){
                    chatHtml += `
                        <div class="text-center">
                            <span class="badge rounded-pill text-bg-secondary chat-date">${toDate(i)}</span>
                        </div>
                    `;
                    const messages = data[i];
                    for(let messageIndex in messages){
                        const message = messages[messageIndex];
                        const user = room.getUser(message.user_id);
                        let content = replaceNewLineCode(message.content);
                        if(message.attachment_url){
                            if(content != ''){
                                content += `
                                    <span class="border-bottom pb-2"></span>
                                    <img src="${appInfo.s3_url + message.attachment_url}" class="img-fluid chat-img pt-2">
                                `;
                            }
                            else{
                                content += `
                                    <img src="${appInfo.s3_url + message.attachment_url}" class="img-fluid chat-img">
                                `;
                            }
                        }
                        chatHtml += `
                            <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}">
                                <span class="talk-icon">
                                    <img src="${user.image_url}" width="46" height="46"/>
                                </span>
                                <span class="talk-user text-gray">${user.name}</span>
                                <span class="talk-timestamp text-gray">${toTime(message.created_at)}</span>
                                <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                                    ${content}
                                </span>
                            </p>
                        `;
                    }
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

    showWindow(){
        if(this.deviceType == DeviceType.PC){
            $('.room-list').show();
            $('#chat-room').show();
        }
        else if(this.deviceType == DeviceType.MOBILE){
            if(roomList.currentOpenRoomId == null){
                $('.room-list').show();
                $('#chat-room').hide();
            }
            else{
                $('.room-list').hide();
                $('#chat-room').show();
            }
        }
    }

    postMessage(){
        const room_id = roomList.currentOpenRoomId;
        const content = $('#content').val();
        const attachment = $('#attachment').prop('files')[0];
        if(!room_id || (!content && !attachment)){
            return;
        }
        postMessageRequest(room_id, content, attachment);
    }

    appendMessage(data){
        const room = roomList.getRoom(Number(data.room_id));
        // ルームリストのメッセージを更新
        room.latest_message_id = data.id;
        room.latest_message = data;
        const $latestMessage = $(`#rooms_${room.id}`).children().children('.latest-message');
        if($latestMessage){
            if(data.content == null && data.attachment_url){
                $latestMessage.text('ファイルを送信しました');
            }
            else{
                $latestMessage.text(data.content);
            }
        }
        // 現在開いているルームであるか
        if(roomList.currentOpenRoomId != room.id){
            return;
        }
        // チャットルームの更新
        let chatHtml = '';
        const date = toDate(data.created_at);
        const user = room.getUser(data.user_id);
        const filtered = $(".chat-date").filter(function() {
            return $(this).text() == date;
        });
        if(filtered.length == 0){
            chatHtml += `
                <div class="text-center">
                    <span class="badge rounded-pill text-bg-secondary chat-date">${date}</span>
                </div>
            `;
        }
        let content = replaceNewLineCode(data.content);
        if(data.attachment_url){
            if(content != ''){
                content += `
                    <span class="border-bottom pb-2"></span>
                    <img src="${appInfo.s3_url + data.attachment_url}" class="img-fluid chat-img pt-2">
                `;
            }
            else{
                content += `
                    <img src="${appInfo.s3_url + data.attachment_url}" class="img-fluid chat-img">
                `;
            }
        }
        chatHtml += `
            <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}">
                <span class="talk-icon">
                    <img src="${user.image_url}" width="46" height="46"/>
                </span>
                <span class="talk-user text-gray">${user.name}</span>
                <span class="talk-timestamp text-gray">${toTime(data.created_at)}</span>
                <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                    ${content}
                </span>
            </p>
        `;
        $('#chat').append(chatHtml);
        // メッセージを空にする
        $('#attachment').val('').change();
        $('#content').val('');
        $('#content').focus();
        // チャット画面でスクロールを最下部へ移動
        srcollBottomObj($('#chat'));
    }
}