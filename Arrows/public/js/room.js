class Room {
    constructor(my, roomInfo){
        this.id = roomInfo.id;
        this.type = roomInfo.type;
        this.members = [];
        if(roomInfo.members){
            for(let i in roomInfo.members){
                this.members.push(roomInfo.members[i].user)
            }
        }
        this.name = roomInfo.name;
        this.image_url = null;
        if(this.type == RoomType.MY_MESSAGE){
            this.image_url = my.image_url;
        }
        else if(this.type == RoomType.DIRECT_MESSAGE){
            const partner = this.getPartner(my);
            this.name = partner.name;
            this.image_url = partner.image_url;
        }
        this.owner_id = roomInfo.owner_id;
        this.latest_message_id = roomInfo.latest_message_id;
        if(this.latest_message_id){
            this.latest_message = roomInfo.latest_message;
        }
        this.opening_meet_id = roomInfo.opening_meet_id;
        if(this.opening_meet_id){
            this.opening_meet = roomInfo.opening_meet;
        }
    }

    getPartner(my){
        if(this.type != RoomType.DIRECT_MESSAGE){
            return;
        }
        const partner = this.members.filter(item => item.id !== my.id);
        if(!partner){
            return;
        }
        return partner[0];
    }

    getUser(id){
        if(this.type == RoomType.MY_MESSAGE){
            return authUser;
        }
        const user = this.members.filter(item => item.id === id);
        if(!user){
            return;
        }
        return user[0];
    }
}

class RoomList {
    constructor(){
        this.items = [];
        this.currentOpenRoom = null;
        this.currentOpenRoomId = null;
        this.setHandler();
        this.load();
    }

    setHandler(){
        $(document).on('click', '.room-card', (e) =>{
            const room_id = Number(e.currentTarget.id.replace('rooms_', ''));
            if(this.currentOpenRoomId == room_id){
                return;
            }

            //既に入室しているルームがあれば退室する
            $('.room-selected').removeClass('room-selected');

            const room = this.getRoom(room_id);
            this.currentOpenRoom = room;
            this.currentOpenRoomId = room_id;
            $(e.currentTarget).addClass('room-selected');
            $('#chat-room').trigger('loadmessage', {room: room});
            $(window).trigger('refresh');
        });

        $('#room-list-search-box').on('input', (e) => {
            const keyword = $(e.target).val();
            this.draw(keyword);
        });
    }

    load(){
        getRoomList().done((datas) => {
            for(let i in datas){
                const room = new Room(authUser, datas[i]);
                this.append(room);
            }
            // 描画
            this.draw();
    
            let joinRooms = [];
            // マイルームを追加
            joinRooms.push(`MYROOM_${authUser.id}`);
            // 参加中のルームを追加
            const roomIds = this.getIds();
            for(let i in roomIds){
                joinRooms.push(`ROOM_${roomIds[i]}`);
            }
    
            // ソケットに通知
            ioSocket.emit('join', {
                room_ids: joinRooms
            });
        });
    }

    append(room){
        this.items.push(room);
    }

    draw(keyword = null){
        $('#room_list').empty();
        if(!this.items.length){
            return;
        }

        let html = '';
        for(let i in this.items){
            const room = this.items[i];
            if(keyword != null && !room.name.includes(keyword)){
                continue;
            }
            let latestMessage = '';
            if(room.latest_message_id){
                if(room.latest_message.content == null && room.latest_message.attachment_url){
                    latestMessage = 'ファイルを送信しました';
                }
                else{
                    latestMessage = room.latest_message.content;
                }
            }
            html += `
                <div id="rooms_${room.id}" class="room-card row">
                    <div class="col-auto">
                        <img class="avatar rounded-circle mr-1" src="${room.image_url}" width="48" height="48">
                    </div>
                    <div class="col-auto">
                        <p class="h6 room-name">${room.name}</p>
                        <p class="h6 latest-message">${latestMessage}</p>
                    </div>
                </div>
            `;
        }
        $('#room_list').append(html);
    }

    getIds(){
        if(!this.items.length){
            return;
        }
        return this.items.map(item => item.id);
    }

    getRoom(room_id){
        if(!this.items.length){
            return;
        }
        const room = this.items.filter(item => item.id === room_id);
        if(!room){
            return;
        }
        return room[0];
    }

    closeRoom(){
        this.currentOpenRoom = null;
        this.currentOpenRoomId = null;
        $('.room-selected').removeClass('room-selected');
    }
}