<div class="reportform">
{!! Form::open(['class' => 'form-inline', 'id' => 'reportRangeForm']) !!}
{{-- {!! Form::select('reportRange', ['week' => 'This Week', 'month' =>'This Month', 'custom' => 'Custom Date Range'], null, ['id' => 'reportRange', 'class' => 'form-control']) !!} --}}
{!! Form::input('date', 'from', date('Y-m-d', strtotime($rangeStart)), ['id' => 'reportFrom', 'class' => 'form-control'])!!}
{!! Form::input('date', 'to', date('Y-m-d', strtotime($rangeEnd)), ['id' => 'reportTo', 'class' => 'form-control'])!!}
{!! Form::button('Go',['class' => 'btn btn-danger', 'type'=>'submit']); !!}

<div class="btn-group">
<a class="btn btn-primary" href="/report/week">This Week</a>
<a class="btn btn-primary" href="/report/month">This Month</a>
<a class="btn btn-success" href="/"><i class="glyphicon glyphicon-home"></i></a>
</div>

{{ Form::close() }}

</div>