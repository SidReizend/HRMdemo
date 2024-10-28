    <!-- get all the monthly task plan against the  project in selected monthly plan start-->
<?php
if (isset($monthlytaskplan)) {
    $sno = 1;
    $timearray = array();
    $tasktotaltimearray = array();
    ?>
    <?php
    //get all the time in the taskplan for total time calculation
    foreach ($monthlytaskplan as $key => $taskplan) {
        $timearray[] = $taskplan['time'];
        ?>   

        <tr>
            <td ><?php echo $sno ?> </td>
            <td><?php echo $taskplan['task_code'] . " - " . $taskplan['task_desc'] ?> </td>
            <td><?php echo $taskplan['name'] ?> </td>
            <td class ="total" ><?php echo $taskplan['time'] ?> </td>
            <?php
            $talent = '';
            $dateFrom = date('Y-m-01', strtotime(date($hoursArray['year'] . '-' . $hoursArray['month'])));
            $dateTo = date('Y-m-t', strtotime($dateFrom));
            $time = $this->TimePredictionModel->projectTaskAmountCalProjects($projectId, $taskplan['taskid'], $talent, $dateFrom, $dateTo);
            if ($time[0]['timeSum'] != "") {
                $actual_talent_time = $this->talentAttendenceSheet->secondsToMinitues($time[0]['timeSum']);
            } else {
                $actual_talent_time = "00:00:00";
            }
            //get the talents actual hours for total calculation
            $tasktotaltimearray[] = $actual_talent_time;
            ?>
            <td><?php echo $actual_talent_time ?> </td>
            <td>
                <?php
                $taskTotalTime = $this->TimePredictionModel->projectTaskTotalTime($taskplan['taskid']);
                if ($taskTotalTime[0]['timeSum'] != "") {
                    $actual_task_time = $this->talentAttendenceSheet->secondsToMinitues($taskTotalTime[0]['timeSum']);
                } else {
                    $actual_task_time = "00:00:00";
                }
                echo $actual_task_time;
                ?>
            </td>
            <?php
            $timedifference = 0;
            $estimateTimeSeconds += $this->talentAttendenceSheet->monthlyPlanhourToSecondsConvertion($taskplan['time']);
            $timedifferenceSeconds = $time[0]['timeSum'] - $estimateTimeSeconds;
            //get the consumed precentage
            $perecentage = ($time[0]['timeSum'] / $estimateTimeSeconds) * 100;
            //float to integer conversion
            $consumedPercentage = intval($perecentage);
            ?>
            <?php
            if ($consumedPercentage > 100) {
                ?>               
                <td><span class="badge bg-red"><?php echo $consumedPercentage . "%" ?> </span></td> </td>
                <?php
            } else {
                ?>
                <td><?php echo $consumedPercentage . "%" ?> </td>
                <?php
            }
            ?>
            <?php
            //get the todal day in month 
            $total_days = intval(date("t"));
            //get the per day task time based on the estimation time
            $taskHourPerDayseconds = $estimateTimeSeconds / $total_days;
            //get the todat date today estimate time
            $today = intval(date("d"));
            $todayAcutalTaskseconds = $today * $taskHourPerDayseconds;
            $todayAcutalTaskhour = $this->talentAttendenceSheet->secondsToMinitues($todayAcutalTaskseconds);
            $taskHourPerDay = $this->talentAttendenceSheet->secondsToMinitues($taskHourPerDayseconds);
            ?>
            <?php
            //get the curret task status  for dropdown updation
            $currentstatus = $this->TaskManagementModel->gettaskData($taskplan['taskid']);
            $statusvalue = $currentstatus[0]['status'];
            //status is billed or deployed and closed not show  tends to deviate
            if ($statusvalue == "1" || $statusvalue == "2" || $statusvalue == "10") {
                ?> 
                <td> </td>
                <?php
            } else {
                ?>
                <?php
                //get the curent moth and selcted month comparision
                $now = date("Y-m");
                $month2 = $hoursArray['year'] . "-" . $hoursArray['month'];
                if (date("Ym", strtotime($now)) < date("Ym", strtotime($month2))) {
                    ?>
                    <td> </td>
                    <?php
                } elseif (date("Ym", strtotime($now)) > date("Ym", strtotime($month2))) {
                    ?>
                    <td> </td>
                    <?php
                } else {
                    ?>
                    <?php
                    if ($todayAcutalTaskseconds < $time[0]['timeSum']) {
                        ?>
                        <td> <?php echo "Yes( Expected Hours :" . $todayAcutalTaskhour . ")" ?></td>
                        <?php
                    } else {
                        ?>
                        <td> </td>
                        <?php
                    }
                }
            }
            ?>
            <td>
                <?php
                echo form_dropdown('status', $statusArray, $currentstatus[0]['status'], array(
                    'class' => 'form-control select2 statustask',
                    'id' => 'statustask',
                    'data-id' => $taskplan['taskid'],
                ));
                ?>
            </td>       
            <td><?php
                echo "&nbsp;";
                echo anchor('#', 'Delete', array(
                    'class' => 'btn btn-danger delete_task_plan',
                    'data-id' => $taskplan['id'],
                    'data-project' => $projectId,
                    'data-monthlyplan' => $monthlyplan
                ));
                ?> </td>
        </tr>
        <?php
        $sno++;
    }
    ?>
    <tr>
        <?php
        if (count($timearray) > 0) {

            $talent_time = "00:00:00";
            $talent_time = $this->TimePredictionModel->sum_time($timearray);
        }
        $estimateMonthlyPlanTimeSeconds = 0;
        $estimateTalentsTimeSeconds = 0;
        $estimateMonthlyPlanTimeSeconds += $this->talentAttendenceSheet->monthlyPlanhourToSecondsConvertion($hoursArray['hours']);
        $estimateTalentsTimeSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($talent_time);
        ?>
        <td colspan ="2"> </td>
        <td> <b>Total Estimate Hours</b></td>
        <?php
        if ($estimateTalentsTimeSeconds > $estimateMonthlyPlanTimeSeconds) {
            ?>
            <td class ="total_time"> 
                <span class="badge bg-red"><?php
                echo "<b>" . $talent_time . "</b>";
                echo "<script> alertify.error('Total time is higher Than Estimate Time')</script>";
                ?> </span></td>

            <?php
        } else {
            ?>
            <td class ="total_time"> <?php
                echo "<b>" . $talent_time . "</b>";
                ?></td>
            <?php
        }
        ?>
        <td colspan="3">
            <?php
            if (count($tasktotaltimearray) > 0) {
                $task_total_hours = "00:00:00";
                $task_total_hours = $this->TimePredictionModel->sum_time($tasktotaltimearray);
            }
            echo "<b>Total Actual Hours :" . $task_total_hours . "</b>";
            ?>

        </td>
        <td colspan="3"></td>
    </tr>

    <?php
}
?>
<!-- get all the monthly task plan against the  project in selected monthly plan end-->




<!-- get all the unplanned task details  against the monthly task plan start-->
<?php
if (isset($taskcode) && $taskcode != []) {
    ?>
    <thead>
        <tr>

            <th>#</th>
            <th>Task </th>
            <th>Time Booked</th>
            <th>Total Task Hours</th>
            <th>Status</th>
            <th>Talents </th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sino = 1;
        $timearray = array();
        foreach ($taskcode as $task) {
            ?>
            <tr>

                <td><?php echo $sino ?></td>
                <?php
                $taskname = $this->projects->getTaskName($task);
                ?>
                <td><?php echo $taskname[0]['task_code'] . " - " . $taskname[0]['task_desc'] ?></td>
                <?php
                $talent = '';
                $dateFrom = date('Y-m-01', strtotime(date($year . '-' . $month)));
                $dateTo = date('Y-m-t', strtotime($dateFrom));
                $time = $this->TimePredictionModel->projectTaskAmountCalProjects($projectId, $task, $talent, $dateFrom, $dateTo);

                if ($time[0]['timeSum'] != "") {
                    $actual_task_time = $this->talentAttendenceSheet->secondsToMinitues($time[0]['timeSum']);
                    //get all time for total calculation
                    $timearray[] = $actual_task_time;
                } else {
                    $actual_task_time = "00:00:00";
                }
                ?>
                <td> <?php echo $actual_task_time ?></td>
                <td> 
                    <?php
                    $taskTotalTime = $this->TimePredictionModel->projectTaskTotalTime($task);
                    if ($taskTotalTime[0]['timeSum'] != "") {
                        $actual_task_time = $this->talentAttendenceSheet->secondsToMinitues($taskTotalTime[0]['timeSum']);
                    } else {
                        $actual_task_time = "00:00:00";
                    }
                    echo $actual_task_time;
                    ?>
                </td>
                <td>
                    <?php
                    $currentstatus = $this->TaskManagementModel->gettaskData($task);
                    ?>
                    <?php
                    echo form_dropdown('status', $statusArray, $currentstatus[0]['status'], array(
                        'class' => 'form-control select2 statustask',
                        'id' => 'statustask',
                        'data-id' => $task,
                    ));
                    ?>
                </td>    
                <td> 
                    <?php
                    $allTalents = array();
                    $talents = $this->TimePredictionModel->getTalentsByTask($task, $dateFrom, $dateTo, $talent);
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
            $sino++;
        }
        ?>
        <tr>
            <td></td>
            <td> <b>Total Booked Hours(UnPlanned)</b></td>
            <td>
                <?php
                $talent_time = "00:00:00";
                if (count($timearray) > 0) {

                    $talent_time = $this->TimePredictionModel->sum_time($timearray);
                    echo "<b>" . $talent_time . "</b>";
                } else {
                    echo "<b>" . $talent_time . "</b>";
                }
                ?>
            </td>
            <td></td>
            <td colspan="2">
                <?php
                $time = $this->talentAttendenceSheet->secondsToMinitues($selectedtasktime[0]['timeSum']);
                $timearray[] = $time;
                $selected_task_time = $this->TimePredictionModel->sum_time($timearray);
                echo "<b>Total Booked Hours (Planned & UnPlanned) :" . $selected_task_time . "</b>";
                ?>
            </td>

        </tr>
    </tbody>
    <?php
}
?>

<!-- get all the unplanned task details  against the monthly task plan end-->