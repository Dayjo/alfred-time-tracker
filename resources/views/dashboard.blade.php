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
            <div id="dailyPie"></div>
            {!! view('timeline-table', ['timeline' => $today->data, 'reporter' => $reporter]) !!}
            <?php

            foreach ($today->data as $task):
                ?>
                <p>
                <strong>
                    <?=date('H:i:s', $task->time);?>
                     -&gt;
                     <?=date('H:i:s', $task->time + $task->length);?>
                    (<?=$reporter->secondsToTime($task->length);?>)
                    <?=$task->task;?>
                </strong><br />
                <em><?=$task->notes;?></em>
                </p>

                <?php
            endforeach;
            ?>
            <br />
            <div class="title m-b-md">Weekly:</div>
            <div id="weeklyPie"></div>
            {!! view('totals-table', ['totals' => $reporter->totals('weekly'), 'reporter' =>$reporter])->render() !!}

            <br />
            <div class="title m-b-md">All Time:</div>
            <div id="allPie"></div>

            {!! view('totals-table', ['totals' => $totals, 'reporter' =>$reporter])->render() !!}

            <div id="example"></div>

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
