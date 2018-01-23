<br />
<div class="btn-group">
<a class="btn btn-success" href="/"><i class="glyphicon glyphicon-home"></i></a>
<a class="btn btn-primary" href="/report/range/{!! date("Y-m-d", strtotime("first day of last month")) !!}/{!! date("Y-m-d", strtotime("last day of last month")) !!}">Last Month</a>
<a class="btn btn-primary" href="/report/range/{!! date("Y-m-d", strtotime("last monday 7 days ago")) !!}/{!! date("Y-m-d", strtotime("last friday 7 days ago")) !!}">Last Week</a>
<a class="btn btn-primary" href="/report/week">This Week</a>
<a class="btn btn-primary" href="/report/month">This Month</a>
</div>

<div class="reportform">
{!! Form::open(['class' => 'form-inline', 'id' => 'reportRangeForm']) !!}
{{-- {!! Form::select('reportRange', ['week' => 'This Week', 'month' =>'This Month', 'custom' => 'Custom Date Range'], null, ['id' => 'reportRange', 'class' => 'form-control']) !!} --}}
<div class="form-group has-feedback">
    {{-- <input type="text" class="form-control" placeholder="Username" /> --}}
    {!! Form::input('date', 'from', date('Y-m-d', strtotime($rangeStart)), ['id' => 'reportFrom', 'class' => 'form-control'])!!}
    <i class="glyphicon glyphicon-calendar form-control-feedback"></i>
</div>

<div class="form-group has-feedback">
{!! Form::input('date', 'to', date('Y-m-d', strtotime($rangeEnd)), ['id' => 'reportTo', 'class' => 'form-control'])!!}
<i class="glyphicon glyphicon-calendar form-control-feedback"></i>
</div>

{!! Form::button('Go',['class' => 'btn btn-danger', 'type'=>'submit']); !!}


{{ Form::close() }}

</div>