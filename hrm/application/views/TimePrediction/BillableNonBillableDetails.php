<?php
if (empty($project)) {
    ?> 
    <table style="display:none;">
        <thead>
            <tr>
                <th>
                    Project
                </th>
                <th>
                    Billable
                </th>
                <th>
                    Non Billable
                </th>
                <th>
                    Time
                </th>
                <th>
                    Total
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalBillable = array();
            $nonBillable = array();
            $totalTime = array();
            $totalProductivity = 0;
            $projectAndProductivity = $this->TimePredictionModel->TalentTimeSpend($talent, $fromdate, $todate);
            foreach ($projectAndProductivity as $projectPro) {
                ?>
                <tr class="detailed">
                    <td>
                        <?php
                        echo $projectPro['project_code'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $projectPro['billable_time'];
                        $totalBillable[] = $projectPro['billable_time'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $projectPro['non_billable_time'];
                        $nonBillable[] = $projectPro['non_billable_time'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $projectPro['time'];
                        $totalTime[] = $projectPro['time'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $projectPro['productivity'];
                        $totalProductivity += $projectPro['productivity'];
                        ?>
                    </td>
                </tr>

                <?php
            }
            ?>
            <tr>
                <td>
                    Total
                </td>
                <td>
                    <?php
                    echo $totalBilableFormat = $this->TimePredictionModel->sum_time($totalBillable);
                    ?>
                </td>
                <td>
                    <?php
                    echo $totalNonBilableFormat = $this->TimePredictionModel->sum_time($nonBillable);
                    ?>
                </td>
                <td>
                    <?php
                    echo $totalTimeFormat = $this->TimePredictionModel->sum_time($totalTime);
                    ?>
                </td>
                <td>
                    <?php
                    echo $totalProductivity;
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    Percentage
                </td>
                <td>
                    <?php
                    echo round(($this->TimePredictionModel->decimalHours($totalBilableFormat)) * 100 / ($this->TimePredictionModel->decimalHours($totalTimeFormat)), 2) . '%';
                    ?>
                </td>
                <td>
                    <?php
                    echo round(($this->TimePredictionModel->decimalHours($totalNonBilableFormat)) * 100 / ($this->TimePredictionModel->decimalHours($totalTimeFormat)), 2) . '%';
                    ?>
                </td>
            </tr>
        </tbody>
    </table> 
    <?php
} else {
    ?>
    <table>
        <thead>
            <tr>
                <th>
                    Task
                </th>
                <th>
                    Time
                </th>
                <th>
                    Total
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $taskAndProductivity = $this->TimePredictionModel->talentTimeSpendGroupByTask($talent, $fromdate, $todate, $project);
            foreach ($taskAndProductivity as $taskPro) {
                ?>
                <tr class="detailed">
                    <td>
                        <?php
                        echo $taskPro['task_code'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $taskPro['time'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $taskPro['productivity'];
                        ?>
                    </td>
                </tr>

                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
