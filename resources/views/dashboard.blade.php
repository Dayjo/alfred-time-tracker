@section('body')
    <div class="flex-center">
        <div class="content">

            <div class="title m-b-md">Today totals</div>
            <div class="flex-container">

                <div class="flex-item">
                    <div class="LivePie" data-range="daily"></div>
                </div>
                <div class="flex-item">
                    {!! view('totals-table', ['totals' => $reporter->totals('daily'), 'reporter' => $reporter])->render() !!}
                </div>
            </div>
            <br />

            <div class="title m-b-md">Weekly totals</div>

            <div class="flex-container">

                <div class="flex-item">
                    <div id="weeklyPie" class="LivePie" data-range="weekly "></div>
                </div>
                <div class="flex-item">
                    {!! view('totals-table', ['totals' => $reporter->totals('weekly'), 'reporter' =>$reporter])->render() !!}
                </div>
            </div>

            <br />
            <div class="title m-b-md">All totals</div>
            <div class="flex-container">

                <div class="flex-item">
                    <div id="allPie" class="LivePie" data-range="alltime"></div>
                </div>
                <div class="flex-item">
                    {!! view('totals-table', ['totals' => $totals, 'reporter' =>$reporter])->render() !!}
                </div>
            </div>


        </div>
    </div>
@endsection

@extends('base')