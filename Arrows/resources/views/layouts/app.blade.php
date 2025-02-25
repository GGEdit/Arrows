<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('layouts.import')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-location-arrow text-success"></i>&nbsp;{{ config('app.name', 'Laravel') }}
                </a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                    @auth
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">トーク</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/friend">友だち追加</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/account">アカウント</a>
                        </li>
                    </ul>
                    @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('ログイン') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('新規登録') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('ログアウト') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="pt-4">
            @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @yield('content')
            @auth
            <input type="hidden" id="auth_user" value="{{ Auth::user() }}">
            <input type="hidden" id="app_info" value="{{ json_encode($app_info) }}">
            @endauth
        </main>
        @auth
        <footer class="footer">
            <div id="buttonGroup" class="btn-group selectors">
                <a id="home" href="/" type="button" class="btn btn-secondary button-active">
                    <div class="selector-holder">
                        <i class="fas fa-comment-dots"></i>
                        <span>トーク</span>
                    </div>
                </a>
                <a id="friend" href="/friend" type="button" class="btn btn-secondary button-inactive">
                    <div class="selector-holder">
                        <i class="fas fa-user-plus"></i>
                        <span>友だち追加</span>
                    </div>
                </a>
                <a id="account" href="/account" type="button" class="btn btn-secondary button-inactive">
                    <div class="selector-holder">
                        <i class="fas fa-cog"></i>
                        <span>アカウント</span>
                    </div>
                </a>
            </div>
      </footer>
      @endauth
    </div>
</body>
</html>
