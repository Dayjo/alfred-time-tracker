<div class="timeline">

<?php
// dd($timeline);
// Days
foreach ($timeline as $date => $tasks):?>

    <div class="timeline-date">{!! date('D jS M o', strtotime($date)) !!}</div>

<?php
    // Tasks
    foreach($tasks as $task => $logs):
?>

    <div class="timeline-task">{{$task}}</div>
        <table class="timeline-table">
            <thead>
                <tr>
                    <th width="20%">Started</th>
                    <th width="20%">Stopped</th>
                    <th width="20%">Length</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
<?php
        // Logs
        $taskTotal = 0;
        foreach ($logs as $log):
            $taskTotal += $log->length;
            if ( $log->length < 60 ) continue;
?>
                <tr class="timeline-log-row">
                    <td><?=date('H:i:s', $log->time);?></td>
                    <td><?=date('H:i:s', $log->time + $log->length)?></td>
                    <td><?=$reporter->secondsToTime($log->length);?></td>
                    <td><?=$log->notes?></td>
                </tr>

<?php
        endforeach;
?>

        </tbody>
        <tfoot>

            <tr class="table-total-row">
                <td><strong> Total</strong></td>
                <td colspan=3>{{ $reporter->secondsToTime($taskTotal) }}</td>
            </tr>
        </tfoot>
    </table>

<?php
    endforeach;
endforeach;
?>


</div>