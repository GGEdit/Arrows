class Message {

    constructor(){
        this.deviceType = null;
        this.roomMessages = [];
        this.editMode = false;
        this.editMessageId = null;

        $('.input-area').hide();
        this.setHandler();
        $(window).trigger('refresh');
    }

    setHandler(){

        $(window).on('refresh resize', (e)=> {
            // PC・モバイル用表示設定
            const ww = $(window).width();
            let regHeight = 0;
            if(ww >= 768){
                this.deviceType = DeviceType.PC;
                regHeight = 100;
                $('.room-list').show();
                $('#chat-room').show();
            }
            else{
                this.deviceType = DeviceType.MOBILE;
                regHeight = 160;
                if(roomList.currentOpenRoomId == null){
                    $('.room-list').show();
                    $('#chat-room').hide();
                }
                else{
                    $('.room-list').hide();
                    $('#chat-room').show();
                }
            }
            // 高さ調整
            const winH = $(window).height() - regHeight;
            $('#chat-room').outerHeight(winH);
            $('.room-list').outerHeight(winH);
            // 戻るボタン表示
            $('#clear-chat-room-icon').toggle(roomList.currentOpenRoomId != null);
            // Web会議ボタン表示
            this.showMeetButton();
        });

        $('#clear-chat-room-icon').on('click', (e) => {
            roomList.closeRoom();
            this.roomMessages = [];
            this.toggleEditMode(false);
            $('#chat').empty();
            $('#chat-room-name').text(`チャットルームを選択してくだい`);
            $('.input-area').hide();
            $(window).trigger('refresh');
        });

        $('#web-meeting-btn').on('click', (e) => {
            this.startConference();
        });

        $('#join-web-meeting-btn').on('click', (e) => {
            this.joinConference(roomList.currentOpenRoomId, roomList.currentOpenRoom.opening_meet.meet_name);
        });

        $('#terminate-web-meeting-btn').on('click', (e) => {
            this.terminateConference();
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

        $('#edit-cancel-link').on('click', (e) => {
            this.toggleEditMode(false);
        });

        $('#edit-submit-link').on('click', (e) => {
            this.postMessage();
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

        $('#content').on('input', (e) => {
            const isScrolledChatArea = isScrollBottom($('#chat'));
            const lineHeight = parseInt($(e.target).css('lineHeight'));
            let lines = ($(e.target).val() + '\n').match(/\n/g).length;
            lines = lines > 1 ? lines : 2;
            const setHeight = lines > 10 ? 200 : lineHeight * lines;
            $(e.target).height(setHeight);
            // チャットエリアが最下部の時は追従させる
            if(isScrolledChatArea){
                srcollBottomObj($('#chat'));
            }
        });

        $('#chat-room').on('loadmessage', async (e, data) => {
            $('#chat').empty();
            this.roomMessages = [];
            this.toggleEditMode(false);

            const room = data.room;
            $('#chat-room-name').text(`${room.name}`);
            $('.input-area').show();
            let chatHtml = '';
            const response = await getMessageRequest(room.id);
            for(let i in response){
                chatHtml += `
                    <div class="text-center">
                        <span class="badge rounded-pill text-bg-secondary chat-date">${toDate(i)}</span>
                    </div>
                `;
                const messages = response[i];
                for(let messageIndex in messages){
                    const message = messages[messageIndex];
                    const user = room.getUser(message.user_id);
                    const content = this.createContentHtml(message);
                    chatHtml += `
                        <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}" data-message-id="${message.id}">
                            <span class="talk-icon">
                                <img src="${user.image_url}"/>
                            </span>
                            <span class="talk-user text-gray">${user.name}</span>
                            <span class="talk-timestamp text-gray">${toTime(message.created_at)}</span>
                            <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                                ${content}
                            </span>
                            <span class="chat-actions">
                                <button class="edit-btn">
                                    <i class="fas fa-edit"></i>&nbsp;&nbsp;編集
                                </button>
                                <button class="delete-btn">
                                    <i class="fas fa-trash"></i>&nbsp;&nbsp;削除
                                </button>
                            </span>
                        </p>
                    `;

                    // 配列に登録
                    this.roomMessages.push(message);
                }
            }
            $('#chat').append(chatHtml);
            // チャット画面でスクロールを最下部へ移動
            srcollBottomObj($('#chat'));
        });

        $(document).on('click', '.edit-btn', (event) => {
            const messageId = $(event.target).parent().parent().data('message-id');
            const message = this.getMessage(messageId);
            if(!message){
                return;
            }

            this.editMessageId = message.id;
            this.toggleEditMode(true);
            $('#content').val(message.content).trigger('input');
        });

        $(document).on('click', '.delete-btn', function(event){
            const messageId = $(event.target).parent().parent().data('message-id');
            if(!messageId){
                return;
            }

            Swal.fire({
                title: '削除してもいいですか？',
                text: "この操作は取り消せません",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then(async (result) => {
                if (result.value) {
                    const result = await deleteMessageRequest(messageId);
                }
            });
        });

        ioSocket.on("create", (data) => {
            this.appendMessage(data)
        });

        ioSocket.on("update", (data) => {
            this.updateMessage(data)
        });

        ioSocket.on("delete", (data) => {
            this.deleteMessage(data);
        })

        ioSocket.on("notify_conference", (data) => {
            this.notifyConference(data);
        })

        ioSocket.on("notify_terminate_conference", (data) => {
            this.notifyTerminateConference(data);
        })
    }

    toggleEditMode(enabled = true){
        if(enabled){
            this.editMode = true;
            $('#edit-annotation').show();
            return;
        }
        this.editMode = false;
        this.editMessageId = null;
        $('#edit-annotation').hide();
        $('#content').val('').trigger('input');
    }

    showMeetButton(){
        if(!roomList.currentOpenRoomId || roomList.currentOpenRoom.type == RoomType.MY_MESSAGE){
            $('#web-meeting-btn').hide();
            return;
        }
        // 会議中かどうか
        if(!roomList.currentOpenRoom.opening_meet_id){
            $('#web-meeting-btn').show();
            $('#join-web-meeting-btn').hide();
            $('#terminate-web-meeting-btn').hide();
            return;
        }
        if(roomList.currentOpenRoom.opening_meet.owner_id == authUser.id){
            $('#web-meeting-btn').hide();
            $('#join-web-meeting-btn').hide();
            $('#terminate-web-meeting-btn').show();
            return;
        }
        $('#web-meeting-btn').hide();
        $('#join-web-meeting-btn').show();
        $('#terminate-web-meeting-btn').hide();
    }

    getMessage(messageId){
        return this.roomMessages.find(message => message.id == messageId);
    }

    getMessageIndex(messageId){
        return this.roomMessages.findIndex(message => message.id == messageId);
    }

    async postMessage(){
        const room_id = roomList.currentOpenRoomId;
        const content = $('#content').val();
        const attachment = $('#attachment').prop('files')[0];
        if(!room_id || (!content && !attachment)){
            return;
        }
        let response = null;
        if(this.editMode){
            response = await updateMessageRequest(this.editMessageId, content);
        }
        else{
            response = await postMessageRequest(room_id, content, attachment);
        }
        if(response.message == 'my blocked'){
            Swal.fire({
                title: "送信エラー",
                text: "このユーザーをブロックしているため、送信できませんでした",
                icon: "error",
            });
        }
        else if(response.message == 'blocked'){
            Swal.fire({
                title: "送信エラー",
                text: "あなたはブロックされているため、送信できませんでした",
                icon: "error",
            });
        }
        else{
            if(this.editMode){
                this.toggleEditMode(false);
                return;
            }
            // メッセージを空にする
            $('#attachment').val('').change();
            $('#content').val('').trigger('input');
            $('#content').focus();
            // チャット画面でスクロールを最下部へ移動
            srcollBottomObj($('#chat'));
        }
    }

    updateLatestMessage(data){
        const room = roomList.getRoom(Number(data.room_id));
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
        return room;
    }

    createContentHtml(data){
        let content = replaceNewLineCode(data.content);
        if(data.attachment_url){
            if(content != ''){
                content += `
                    <span class="border-bottom border-secondary-subtle pb-3"></span>
                    <img src="${appInfo.s3_url + data.attachment_url}" class="img-fluid chat-img pt-3">
                `;
            }
            else{
                content += `
                    <img src="${appInfo.s3_url + data.attachment_url}" class="img-fluid chat-img">
                `;
            }
        }
        return content;
    }

    appendMessage(data){
        // ルームリストの最新メッセージを更新
        const room = this.updateLatestMessage(data);
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
        const content = this.createContentHtml(data);
        chatHtml += `
            <p class="chat-talk ${user.id == authUser.id ? 'mytalk' : ''}" data-message-id="${data.id}">
                <span class="talk-icon">
                    <img src="${user.image_url}"/>
                </span>
                <span class="talk-user text-gray">${user.name}</span>
                <span class="talk-timestamp text-gray">${toTime(data.created_at)}</span>
                <span class="talk-content ${user.id == authUser.id ? 'text-white' : ''}">
                    ${content}
                </span>
                <span class="chat-actions">
                    <button class="edit-btn">
                        <i class="fas fa-edit"></i>&nbsp;&nbsp;編集
                    </button>
                    <button class="delete-btn">
                        <i class="fas fa-trash"></i>&nbsp;&nbsp;削除
                    </button>
                </span>
            </p>
        `;
        $('#chat').append(chatHtml);
        // 配列に登録
        this.roomMessages.push(data);
    }

    updateMessage(data){
        // 当該ルームを開いている場合はメッセージを更新
        if(roomList.currentOpenRoomId == data.room_id){
            const content = this.createContentHtml(data);
            const $message = $(`[data-message-id="${data.id}"]`);
            $message.find('.talk-content').html(content);

            // 配列を更新
            const messageIndex = this.getMessageIndex(data.id);
            if(messageIndex != -1){
                this.roomMessages[messageIndex] = data;
            }
        }

        // ルームリストの最新メッセージを更新
        const room = roomList.getRoom(Number(data.room_id));
        if(room.latest_message_id == data.id){
            this.updateLatestMessage(data);
        }
    }

    deleteMessage(data){
        // 当該ルームを開いている場合はメッセージを削除
        if(roomList.currentOpenRoomId == data.room_id){
            const $message = $(`[data-message-id="${data.message_id}"]`);
            $message.remove();

            // 配列から削除
            const messageIndex = this.getMessageIndex(data.message_id);
            if(messageIndex != -1){
                this.roomMessages.splice(messageIndex, 1);
            }
        }

        // ルームリストの最新メッセージを更新
        if(!data.latest_message){
            return;
        }
        this.updateLatestMessage(data.latest_message);
    }

    startConference(){
        // localStorage設定
        localStorage.setItem('is_owner', 1);
        localStorage.setItem('room_id', roomList.currentOpenRoomId);
        localStorage.setItem('meet_name', null);
        // Meetウィンドウを開く
        window.open("/meet", "Meet", "_blank", "height=1920,width=1080");
    }

    joinConference(room_id, meet_name){
        // localStorage設定
        localStorage.setItem('is_owner', 0);
        localStorage.setItem('room_id', room_id);
        localStorage.setItem('meet_name', meet_name);
        // Meetウィンドウを開く
        window.open("/meet", "Meet", "_blank", "height=1920,width=1080");
    }

    terminateConference(){
        Swal.fire({
            title: 'オンライン会議を終了しますか？',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
        }).then(async (result) => {
            if (result.value) {
                // localStorage設定
                localStorage.setItem('is_owner', 0);
                localStorage.setItem('room_id', null);
                localStorage.setItem('meet_name', null);

                const room = roomList.currentOpenRoom;
                await notifyTerminateConferenceRequest(room.id, room.opening_meet.meet_name);
            }
        });
    }

    notifyConference(data){
        // ルームリスト更新
        const room = roomList.getRoom(Number(data.room_id));
        room.opening_meet_id = data.id;
        room.opening_meet = data;

        // 当該ルームを開いている場合はMeetボタンを更新
        if(roomList.currentOpenRoomId == data.room_id){
            this.showMeetButton();
        }

        // オーナーではない場合は着信アラートを表示
        if(room.opening_meet.owner_id != authUser.id){
            Swal.fire({
                title: 'オンライン会議着信',
                html: `
                        ルーム名: ${room.name}
                        <br>
                        主催者: ${room.opening_meet.owner.name}
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '参加する'
            }).then((result) => {
                if (result.value) {
                    this.joinConference(room.id, room.opening_meet.meet_name);
                }
            });
        }
    }

    notifyTerminateConference(data){
        // ルームリスト更新
        const room = roomList.getRoom(Number(data.room_id));
        room.opening_meet_id = null;
        room.opening_meet = null;

        // 当該ルームを開いている場合はMeetボタンを更新
        if(roomList.currentOpenRoomId == data.room_id){
            this.showMeetButton();
        }
    }
}