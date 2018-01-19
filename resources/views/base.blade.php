<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Time Tracker</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <div class="flex-center">
            <div class="content">
                <div id="currentlyTracking"></div>
                {!! view('report-form')->with(get_defined_vars())->render() !!}
            </div>
        </div>


        @yield('body')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.17.1/axios.min.js" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js" charset="utf-8"></script>
        <script src="/js/app.js" charset="utf-8"></script>
    </body>
</html>
