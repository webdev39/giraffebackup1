<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="author" href="humans.txt" />
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lacquer&display=swap" rel="stylesheet">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/print.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#0b5db7">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <base href="/">
    <script src="flutter-host.js"></script>
</head>
<body style="background: linear-gradient(359deg, rgb(32, 31, 69) 0%, rgb(89, 140, 188) 100%)">
    <div id="app">
        <noscript>It may sound funny, but {{ env('APP_NAME') }} requires JavaScript to sing. Please enable it.</noscript>
    </div>

    <!-- App Data for the Client Side -->
    <script src="{{ route('dataset') }}"></script>

    <!-- App JavaScript -->
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    @if (env('APP_ENV') == 'local')
        <script id="__bs_script__">//<![CDATA[
            document.write("<script async src='PROTOCOL//HOST:3000/browser-sync/browser-sync-client.js?v=2.26.5'><\/script>"
            .replace("HOST", location.hostname)
            .replace("PROTOCOL", location.protocol));
    //]]></script>
    @endif
</body>
</html>
