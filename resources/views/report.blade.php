@section('body')
    <div class="flex-center position-ref ">
        <div class="content">
            <div class="title">
                Totals for {{ $reportRangeTitle }}
                <div class="subtitle">
                 <div class="title-date-range"><span>{{ $rangeStart }}</span> to <span>{{ $rangeEnd }}</span></div>
                </div>
            </div>
            <div class="flex-container">
                <div class="flex-item">
                    <div class="LivePie" range="{{ $pieRange }}"></div>
                </div>
                <div class="flex-item">

                    {!! view('totals-table', ['totals' => $totals, 'reporter' => $reporter]) !!}
                </div>
            </div>
        </div>
</div>
<div class="flex-center position-ref ">
        <div class="content">
            <div class="title">
                Timeline
            </div>
            <div class="flex-container">
                <div class="flex-item">
                    {!! view('timeline-table', ['timeline' => $timeline, 'reporter' => $reporter]) !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('base')
