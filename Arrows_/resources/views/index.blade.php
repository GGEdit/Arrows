@extends('layouts.app')
@section('content')
<?php
    $user_id = Auth::user()->id;
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card room-list">
                <div class="card-header"><strong>トークリスト</strong></div>
                <div class="card-body overflow-auto">
                    <div id="room_list">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div id="chat-room" class="card">
                <div id="chat-room-name" class="card-header"><strong>チャットルームを選択してくだい</strong></div>
                <div class="card-body overflow-auto" id="chat"></div>
                <div class="card-footer text-muted">
                    <div class="form-group">
                        <textarea class="form-control" id="content" rows="3" placeholder="メッセージを入力..."></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" id="post_message_btn" class="btn btn-primary">送信</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection