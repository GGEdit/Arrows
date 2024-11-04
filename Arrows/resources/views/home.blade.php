@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card room-list">
                <div class="card-header">
                    <i class="fas fa-list-ul"></i>&nbsp;&nbsp;
                    <strong>ルームリスト</strong>
                </div>
                <div class="card-body overflow-auto" id="room-list-card">
                    <div class="">
                        <input type="text" class="form-control font-awesome" id="room-list-search-box" placeholder="&#xf002; 検索">
                    </div>
                    <hr>
                    <div id="room_list">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div id="chat-room" class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="clear-chat-room">
                        <i class="fas fa-chevron-circle-left" id="clear-chat-room-icon">&nbsp;&nbsp;</i>
                        <strong id="chat-room-name">チャットルームを選択してくだい</strong>
                    </div>
                    <div class="meet-btn">
                        <button type="button" id="web-meeting-btn" class="btn btn-sm btn-success">
                            <i class="fas fa-microphone-alt"></i>&nbsp;&nbsp;開始
                        </button>
                        <button type="button" id="join-web-meeting-btn" class="btn btn-sm btn-warning text-white">
                            <i class="fas fa-play-circle"></i>&nbsp;&nbsp;進行中の会議に参加
                        </button>
                        <button type="button" id="terminate-web-meeting-btn" class="btn btn-sm btn-danger">
                            <i class="fas fa-times-circle"></i>&nbsp;&nbsp;会議を終了
                        </button>
                    </div>
                </div>
                <div class="card-body overflow-auto" id="chat"></div>
                <div class="card-footer text-muted input-area">
                    <div class="row gy-1 py-2">
                        <div class="col-md-12">
                            <textarea class="form-control" id="content" placeholder="ここにメッセージ内容を入力&#13;(Shift + Enterキーで送信)"></textarea>
                        </div>
                        <div class="col-md-12" id="edit-annotation">
                            <span>
                                Escキーで<a id="edit-cancel-link" href="javascript:void(0);">キャンセル</a>
                                /
                                Shift + Enterキーで<a id="edit-submit-link" href="javascript:void(0);">保存</a>
                            </span>
                        </div>                        
                        <div class="col-md-12">
                            <img src="" id="attachment-preview">
                        </div>
                        <div class="col-md-12">
                            <div class="float-start">
                                <input type="file" id="attachment" accept="image/*">
                                <button type="button" id="attachment-button" class="btn btn-sm btn-light"><i class="fas fa-file-image"></i></button>
                            </div>
                            <div class="float-end">
                                <button type="button" id="post_message_btn" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>&nbsp;&nbsp;送信
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
