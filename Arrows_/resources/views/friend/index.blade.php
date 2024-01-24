@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card room-list">
                <div class="card-header text-center"><strong>友だち追加</strong></div>
                <div class="card-body overflow-auto">
                    <form action="/friend/search" method="get">
                        <div class="form-group">
                            <label>IDを入力</label>
                            <input type="text" class="form-control" name="username">
                        </div>
                        <button type="submit" class="btn btn-primary">検索</button>
                    </form>
                    @if(isset($errMessage))
                    <hr>
                    <div class="form-group text-center">
                        <p class="h6"><b>{{$errMessage}}</b></p>
                    </div>
                    @endif
                    @if(isset($user))
                    <hr>
                    <form action="/friend" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$user->id}}">
                        <div class="form-group">
                            <img class="d-block mx-auto" src="/storage/preset/default_avatar.png" width="96" height="96">
                        </div>
                        <div class="form-group text-center">
                            <p class="h5"><b>{{$user->name}}</b></p>
                        </div>
                        @if($isFriend)
                        <button type="button" class="btn btn-success d-block mx-auto" disabled>既に追加されています</button>
                        @else
                        <button type="submit" class="btn btn-success d-block mx-auto">追加する</button>
                        @endif
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection