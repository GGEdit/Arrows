@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center"><strong>友だち追加</strong></div>
                <div class="card-body overflow-auto">
                    <form action="/friend/search" method="get">
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label>ユーザー名を入力</label>
                                <input type="text" class="form-control" name="username">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>&nbsp;&nbsp;
                                    検索
                                </button>
                            </div>
                        </div>
                    </form>
                    @if(isset($errMessage))
                    <hr>
                    <div class="text-center">
                        <p class="h6"><b>{{$errMessage}}</b></p>
                    </div>
                    @endif
                    @if(isset($user))
                    <hr>
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
                </div>
            </div>
            <div class="py-2"></div>
            <div class="card">
                <div class="card-header text-center"><strong>あなたの友だち追加URL</strong></div>
                <div class="card-body overflow-auto">
                    <form action="/friend/search" method="get">
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{{ url()->current() }}/add/{{ Auth::user()->username }}">
                                    <button class="btn btn-primary" type="button" id="button-addon2">
                                        <i class="fas fa-clipboard"></i>&nbsp;&nbsp;
                                        コピー
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection