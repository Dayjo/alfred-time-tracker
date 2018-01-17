<table>
    <thead>
        <tr>
            <th>Started</th>
            <th>Stopped</th>
            <th>Task</th>
            <th>Length</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($timeline as $task):
            ?>
            <tr>
                <td><?=date('H:i:s', $task->time);?></td>
                <td><?=date('H:i:s', $task->time + $task->length)?></td>
                <td><strong>
                    <?=$task->task;?>
                </strong></td>
                <td><?=$reporter->secondsToTime($task->length);?></td>
            </tr>

            <?php
        endforeach;
        ?>
    </tbody>
</table>