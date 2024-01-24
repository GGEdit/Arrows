@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center"><strong>友だち追加</strong></div>
                <div class="card-body overflow-auto">
                    @if(isset($user))
                    <form action="/friend" method="post">
                        @csrf
                        <div class="row gy-3">
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="col-md-12">
                                <img class="d-block mx-auto" src="/storage/preset/default_avatar.png" width="96" height="96">
                            </div>
                            <div class="col-md-12 text-center">
                                <p class="h5"><b>{{$user->name}}</b></p>
                            </div>
                            <div class="col-md-12 text-center">
                                @if($isFriend)
                                <button type="button" class="btn btn-secondary d-block mx-auto" disabled>既に追加されています</button>
                                @else
                                <button type="submit" class="btn btn-success d-block mx-auto">
                                    <i class="fas fa-plus-square"></i>&nbsp;&nbsp;
                                    追加する
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>
                    @endif
                    @guest
                    <hr>
                    <div class="row gy-2">
                        <div class="col-md-12 text-center">
                            <span>アカウントをお持ちですか？</span>
                        </div>
                        <div class="col-md-12 text-center">
                            <a href="/login" type="button" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;
                                ログイン
                            </a>
                            <a href="/register" type="button" class="btn btn-success">
                                <i class="fas fa-user-plus"></i>&nbsp;&nbsp;
                                新規登録
                            </a>
                        </div>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection