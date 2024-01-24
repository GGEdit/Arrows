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
                <div class="card-body overflow-auto">
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
                <div class="card-header">
                    <button type="button" id="back-button" class="btn btn-sm btn-light">
                        <i class="fas fa-chevron-circle-left"></i>
                    </button>
                    <strong id="chat-room-name">チャットルームを選択してくだい</strong>
                </div>
                <div class="card-body overflow-auto" id="chat"></div>
                <div class="card-footer text-muted input-area">
                    <div class="row gy-2">
                        <div class="col-md-12">
                            <input type="file" id="attachment" accept="image/*">
                            <button type="button" id="attachment-button" class="btn btn-sm btn-light"><i class="fas fa-file"></i></button>
                        </div>
                        <div class="col-md-12">
                            <textarea class="form-control" id="content" rows="3" placeholder="メッセージを入力..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <img src="" id="attachment-preview" style="display:none;">
                        </div>
                        <div class="col-md-12">
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
@endsection
