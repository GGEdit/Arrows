<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meet</title>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="{{ $app_info['nsocket_server'] }}/socket.io/socket.io.js"></script>
    <script src="{{ $app_info['meet_server'] }}/external_api.js"></script>
    <script src="/js/user.js?{{ $app_info['app_version'] }}"></script>
    <script src="/js/util.js?{{ $app_info['app_version'] }}"></script>
    <script src="/js/api/meet.js?{{ $app_info['app_version'] }}"></script>
    <script src="/js/meet/main.js?{{ $app_info['app_version'] }}"></script>
    <script src="/js/meet/meet.js?{{ $app_info['app_version'] }}"></script>
    <style>
        /* iframeのレスポンシブ対応のため、親要素に高さと幅を指定 */
        #jitsi-container {
        width: 100%;
        height: 100vh;
        overflow: hidden;
        }
    </style>
</head>
<body>
    <div id="jitsi-container"></div>
    <input type="hidden" id="auth_user" value="{{ Auth::user() }}">
    <input type="hidden" id="app_info" value="{{ json_encode($app_info) }}">
</body>
</html>