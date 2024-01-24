@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card room-list">
                <div class="card-header text-center"><strong>アカウント情報編集</strong></div>
                <div class="card-body overflow-auto">
                    <form action="/account/update" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>プロフィール画像</label>
                            <div class="media align-items-center">
                                @if($user->image_url != NULL)
                                <img class="mr-4" width="50" height="50" src="{{$user->image_url}}">
                                @endif
                                <div class="media-body">
                                    <input type="file" class="form-control-file" name="image_url">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>ユーザー名</label>
                            <input type="text" class="form-control" name="username" value="{{$user->username}}">
                        </div>
                        <div class="form-group">
                            <label>メールアドレス</label>
                            <input type="text" class="form-control" name="email" value="{{$user->email}}">
                        </div>
                        <div class="form-group">
                            <label>表示名</label>
                            <input type="text" class="form-control" name="name" value="{{$user->name}}">
                        </div>
                        <button type="submit" class="btn btn-primary">変更を保存</button>
                    </form>
                    <hr>
                    <form action="/account/password/update" method="post">
                        @csrf
                        <div class="form-group">
                            <label>現在のパスワードを入力</label>
                            <input type="password" class="form-control" name="current_password">
                        </div>
                        <div class="form-group">
                            <label>新しいパスワードを入力</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <label>新しいパスワードを再度入力</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                        <button type="submit" class="btn btn-primary">パスワードを更新</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection