<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl No.</th>
            <th>Task</th>
            <th>Task Description</th>
            <th>Parent Task</th>
            <th>Parent Project</th>
            <th>Total Time</th>
            <th>Talents</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 0;
        $sum_approved_time = array();
        $sum_pending_time = array();
        $sum_total_time = array();
        $grant_total = 0;
        $talent = array();
        $i = 0;
        foreach ($ProjectAmountCal as $data) {
            ?>
            <tr class="notitle header showDetailsTr" data-talent="<?php echo $data['talent']; ?>" 
                data-from="<?php echo $fromdate; ?>" data-to="<?php echo $todate; ?>" 
                data-id="<?php echo ++$i; ?>"
                data-task='<?php echo json_encode($task); ?>'  data-project='<?php echo json_encode($project); ?>'  >
                <td>
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['task_name'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['task_desc'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['parent_task'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['parent_project'];
                    ?>
                </td>
                <td>
                    <?php
                    $alltime = array();
                    $time = $this->TimePredictionModel->projectTaskAmountCalProjects($project, $data['task'], $searchTalent, $fromdate, $todate);
                    $talent[] = $data['talent'];
                    $task[] = $data['task'];
                    if ($time[0]['timeSum'] != "") {
                        $actual_time = $this->talentAttendenceSheet->secondsToMinitues($time[0]['timeSum']);
                    } else {
                        $actual_time = "00:00:00";
                    }
                    $alltime[] = $actual_time;
                    $sum_time[] = $actual_time;
                    echo $actual_time;
                    ?>
                </td>
                <td>
                    <?php
                    $allTalents = array();
                    $talents = $this->TimePredictionModel->getTalentsByTask($data['task'], $fromdate, $todate, $searchTalent);
                    $count = 0;
                    // to show talent name if talent is selected, otherwise show all talents related to task
                    if ($data['talent'] != '' || $data['talent'] != null) {
                        echo $data['talent_name'];
                    } else {
                        foreach ($talents as $talent) {
                            if (count($talents) > 1) {
                                $count++;
                                if ($count === count($talents)) {
                                    echo $talent['talent_name'];
                                } else {
                                    echo $talent['talent_name'] . ", ";
                                }
                            } else {
                                echo $talent['talent_name'];
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo $this->TimePredictionModel->sum_time($sum_time); ?></td>
        <td></td>
    </tr>
</table>
