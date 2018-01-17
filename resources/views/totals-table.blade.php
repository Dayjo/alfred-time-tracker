<table>
    <thead>
        <tr>
            <th>Task</th>
            <th>Tracked</th>
            <th>Daily Average</th>
            <th>Logs</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($totals as $taskName => $counts):
            ?>
            <tr>
                <td><strong>
                    <?=$taskName;?>
                </strong></td>
                <td><?=$reporter->secondsToTime($counts['length']);?></td>
                <td><?=$reporter->secondsToTime($counts['length'] / $counts['count']);?></td>
                <td><?=$counts['count'];?></td>
            </tr>

            <?php
        endforeach;
        ?>
    </tbody>
</table>