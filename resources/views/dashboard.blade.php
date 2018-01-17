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

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .flex-container {
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                -webkit-flex-direction: row;
                -ms-flex-direction: row;
                flex-direction: row;
                -webkit-flex-wrap: nowrap;
                -ms-flex-wrap: nowrap;
                flex-wrap: nowrap;
                -webkit-justify-content: center;
                -ms-flex-pack: center;
                justify-content: center;
                -webkit-align-content: stretch;
                -ms-flex-line-pack: stretch;
                align-content: stretch;
                -webkit-align-items: flex-start;
                -ms-flex-align: start;
                align-items: flex-start;
            }

            .flex-item {
                -webkit-order: 0;
               -ms-flex-order: 0;
               order: 0;
               -webkit-flex: 0 1 auto;
               -ms-flex: 0 1 auto;
               flex: 0 1 auto;
               -webkit-align-self: auto;
               -ms-flex-item-align: auto;
               align-self: auto;
               margin-right: 2em;
            }


            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }



            .title {
                font-size: 64px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            table {
                width: 100%;
            }
            table th {
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref ">
            <div class="content">
                <div id="currentlyTracking">
                </div>
                <hr>
            <br /><br />


            <div class="title m-b-md">Today:</div>
            <div class="flex-container">

                <div class="flex-item">
                    <div id="dailyPie"></div>
                </div>
                <div class="flex-item">
                    {!! view('timeline-table', ['timeline' => $today->data, 'reporter' => $reporter]) !!}
                </div>
            </div>
            <br />

            <div class="title m-b-md">Weekly:</div>

            <div class="flex-container">

                <div class="flex-item">
                    <div id="weeklyPie"></div>
                </div>
                <div class="flex-item">


                    {!! view('totals-table', ['totals' => $reporter->totals('weekly'), 'reporter' =>$reporter])->render() !!}
                </div>
            </div>

            <br />
            <div class="title m-b-md">All Time:</div>
            <div class="flex-container">

                <div class="flex-item">
                    <div id="allPie"></div>
                </div>
                <div class="flex-item">
                    {!! view('totals-table', ['totals' => $totals, 'reporter' =>$reporter])->render() !!}
                </div>
            </div>

            <script type="text/javascript">
                var taskTotals = <?=json_encode($totals);?>;
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.17.1/axios.min.js" charset="utf-8"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js" charset="utf-8"></script>
            <script src="js/app.js" charset="utf-8"></script>

        </div>
    </div>
    </body>
</html>
