class Meet {

    constructor(){
        this.is_owner = null;
        this.room_id = null;
        this.meet_name = null;
        this.api = null;
        this.meetName = null;
        this.load();
        this.setHandler();
    }


    load(){
        // localStorageからデータ取得
        this.is_owner = localStorage.getItem('is_owner') == '1';
        this.room_id = localStorage.getItem('room_id');
        this.meet_name = localStorage.getItem('meet_name');

        // Meet名を生成
        this.meetName = this.is_owner ? randomString(25) : this.meet_name;

        // Meet初期化
        const options = {
            roomName: this.meetName,
            width: "100%",
            height: "100%",
            parentNode: document.querySelector('#jitsi-container'),
            lang: 'ja',
            configOverwrite: {
                startWithAudioMuted: true,
                startWithVideoMuted: true,
                prejoinPageEnabled: false,
            },
            interfaceConfigOverwrite: {
                DISABLE_DOMINANT_SPEAKER_INDICATOR: true,
                TOOLBAR_BUTTONS: [
                    'microphone', 
                    'camera', 
                    'hangup', 
                    'settings', 
                    'tileview',
                    'desktop',
                    'fullscreen',
                ]
            },
            userInfo: {
                email: authUser.email,
                displayName: authUser.name
            }
        };
        this.api = new JitsiMeetExternalAPI(appInfo.meet_domain, options);
    }

    setHandler(){

        // 会議にユーザーが参加した時のイベンドハンドラ
        this.api.addEventListener("participantJoined", (event) => {
            console.log("Participant joined:", event);
            console.log("Participant ID:", event.id);
            console.log("Participant Name:", event.displayName);
        });

        // 自分が会議に参加した時のイベントハンドラ(iframe読み込み完了)
        this.api.on("videoConferenceJoined", async () => {

            // Socket.ioに通知
            ioSocket.emit('join_meet', {
                api_token: authUser.api_token,
                user_id: authUser.id,
                room_id: this.room_id,
                meet_name: this.meetName
            });

            // オーナーが会議を開始した時にリクエストを送信する
            if(!this.is_owner){
                return;
            }
            await notifyConferenceRequest(this.room_id, this.meetName);
        });
    }
}