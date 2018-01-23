<div class="timeline">

<?php
// dd($timeline);
// Days

foreach ($timeline as $date => $tasks):?>
<div class="timeline-date stickyHeading">{!! date('D jS M o', strtotime($date)) !!}
</div>
<div class="LivePie" data-range="{{$date}},{{$date}}" data-width="150"></div>


<?php
    $dayTotal = 0;
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
            if ( $log->length < 10 ) continue;
?>
                <tr class="timeline-log-row">
                    <td><?=date('H:i:s', $log->time);?></td>
                    <td><?=date('H:i:s', $log->time + $log->length)?></td>
                    <td><?=$reporter->secondsToTime($log->length, '%h hrs %i mins %s seconds');?></td>
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
    $dayTotal += $taskTotal;
    endforeach;
    ?>


        <div class="dailyTotal">
            <strong>Daily Total: </strong> {{ $reporter->secondsToTime($dayTotal) }}
        </div>

    <?php
endforeach;
?>


</div>