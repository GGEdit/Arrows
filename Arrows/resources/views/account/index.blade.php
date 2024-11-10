@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center"><strong>アカウント情報編集</strong></div>
                <div class="card-body overflow-auto">
                    <form action="/account/update" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label>プロフィール画像</label>
                                <div class="media align-items-center">
                                    @if($user->image_url != NULL)
                                    <div class="p-2">
                                        <img class="mr-4" width="50" height="50" src="{{$user->image_url}}">
                                    </div>
                                    @endif
                                    <div class="media-body">
                                        <input type="file" class="form-control-file" name="image_url">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>ユーザー名</label>
                                <input type="text" class="form-control" name="username" value="{{$user->username}}">
                            </div>
                            <div class="col-md-12">
                                <label>メールアドレス</label>
                                <input type="text" class="form-control" name="email" value="{{$user->email}}">
                            </div>
                            <div class="col-md-12">
                                <label>表示名</label>
                                <input type="text" class="form-control" name="name" value="{{$user->name}}">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-redo"></i>&nbsp;&nbsp;
                                    変更を保存
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <form action="/account/password/update" method="post">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label>現在のパスワードを入力</label>
                                <input type="password" class="form-control" name="current_password">
                            </div>
                            <div class="col-md-12">
                                <label>新しいパスワードを入力</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="col-md-12">
                                <label>新しいパスワードを再度入力</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-redo"></i>&nbsp;&nbsp;
                                    パスワードを更新
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection