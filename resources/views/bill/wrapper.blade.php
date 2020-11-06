<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>OC</title>
{{--    <link href="{{ url('css/quill.snow.css') }}" rel="stylesheet">--}}
{{--    <link href="{{ asset('css/quill.bubble.css') }}" rel="stylesheet">--}}
{{--    <link href="{{ asset('css/quill.core.css') }}" rel="stylesheet">--}}
    <style>
        {!! file_get_contents(public_path(). '/css/quill.snow.css') !!}
    </style>
    <style type="text/css">
        @page {
            margin: 40px 60px;
            font-size: 10px;
        }

        body {
            margin-bottom: 65px;
            font-family: "{{ $font }}" !important;
        }

        p {
            padding: 0 !important;
            margin: 0 !important;
        }

        #footer,
        #footer-container {
            position: fixed;
            left: 0;
            right: 0;
            color: #2f2f2f;
            font-size: 0.9em;
        }

        #footer {
            bottom: 0;
            padding-top: 5px;
            border-top: 0.1pt solid #aaa;
        }

        #footer-container {
            bottom: 35px;
            height: 30px;
        }

        .page-number {
            text-align: right;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        /**
         * Table Style
         */
        .info-table {
            border: solid 1px #336B6B;
            border-collapse: collapse;
            border-spacing: 0;
            color: #333;
        }

        .info-table thead th,
        .info-table tbody td,
        .info-table tfoot td {
            border: solid 1px #ffff;
            padding: 5px 10px !important;
        }

        .info-table thead th {
            text-align: center;
            font-weight: 800;
            background-color: #111;
            color: #fff;
            padding: 10px;
        }

        .info-table tfoot td {
            background-color: #dfdfdf;
        }
    </style>
</head>

<body>

<div class='ql-editor'>
    {!! $content !!}
</div>

</body>
</html>
