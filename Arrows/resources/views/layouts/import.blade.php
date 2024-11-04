<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
<!-- Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@auth
<link rel="stylesheet" href="/css/thirdparty/sweetalert2.css?{{ $app_info['app_version'] }}">
<link rel="stylesheet" href="/css/common.css?{{ $app_info['app_version'] }}">
<link rel="stylesheet" href="/css/chat.css?{{ $app_info['app_version'] }}">
<link rel="stylesheet" href="/css/roomlist.css?{{ $app_info['app_version'] }}">
@endauth
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="/js/app.js" defer></script>
@auth
<script src="/js/thirdparty/sweetalert2.js?{{ $app_info['app_version'] }}"></script>
<script src="{{ $app_info['nsocket_server'] }}/socket.io/socket.io.js"></script>
<script src="/js/api/message.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/api/room.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/api/meet.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/common.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/user.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/room.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/message.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/util.js?{{ $app_info['app_version'] }}"></script>
<script src="/js/main.js?{{ $app_info['app_version'] }}"></script>
@endauth