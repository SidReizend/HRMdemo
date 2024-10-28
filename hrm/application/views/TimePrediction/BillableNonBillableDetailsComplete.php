
<table>
    <thead>
        <tr>
            <th>
                Date
            </th>
            <th>
                Project
            </th>
            <th>
                Task
            </th>
            <th>
                Time
            </th>
            <th>
                Productivity
            </th>
            <th>
                Total
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        $timeSheets = $this->TimePredictionModel->ProjectTimeSpendDayProjects($project, $talent, $fromdate, $todate, $task);
        foreach ($timeSheets as $timeSpend) {
            ?>
            <tr class="detailed">
                <td>
                    <?php
                    echo date('d/m/Y', strtotime($timeSpend['date']));
                    ?>
                </td>
                <td>
                    <?php
                    echo $timeSpend['project_code'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $timeSpend['task_code'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $timeSpend['time'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $this->TimePredictionModel->productivity($talent, $timeSpend['date']);
                    ?>
                </td>
                <td>
                    <?php
                    $timeSpendDecimal = $this->TimePredictionModel->decimalHours($timeSpend['time']);
                    echo $timeSpendDecimal * $this->TimePredictionModel->productivity($talent, $timeSpend['date']);
                    ?>
                </td>
            </tr>

            <?php
        }
        ?>
    </tbody>
</table>