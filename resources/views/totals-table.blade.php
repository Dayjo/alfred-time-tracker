<table>
    <thead>
        <tr>
            <th>Task</th>
            <th>Tracked</th>
            <th>Average Log</th>
            <th>Logs</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $total = 0;
        foreach ($totals as $taskName => $counts):
            $total+=$counts['length'];
            ?>
            <tr>
                <td><strong>
                    <?=$taskName;?>
                </strong></td>
                <td><?=$reporter->secondsToTime($counts['length'], '%h hrs %i mins');?></td>
                <td><?=$reporter->secondsToTime($counts['length'] / ($counts['count'] ? $counts['count'] : 1), '%h hrs %i mins');?></td>
                <td><?=$counts['count'];?></td>
            </tr>

            <?php
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>
                Total
            </strong></td>
            <td colspan=3>
                <?=$reporter->secondsToTime($total, '%h hrs %i mins');?>
            </td>
        </tr>

    </tfoot>
</table>