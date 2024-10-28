<!-- //get monthly plan against the year and month on changing start -->
<?php
if (isset($monthlyplan)) {
    $sno = 1;
    $groupname = '';
    $projectTotalTime = '';
    $projectTime = [];
    $totalActualTimeArray = [];
    $totalEstimateTimeArray = [];
    $grouped = array();
    ?>
    <?php
    foreach ($monthlyplan as $key => $plan) {
        $grouped[$plan['group_name']][] = $plan;
    }
//getting all the timme against the project and selected year amd month
    ?>
    <?php
    foreach ($grouped as $place => $rows) {
        //getting group name and group time and precentage deviation
        if ($rows[0]['group_name'] != '') {
            ?>
            <tr>

                <td colspan="2"><?php echo "<center><b>" . $rows[0]['group_name'] . "</b></center>" ?></td>
                <td colspan="2"><?php echo "<b>Total Group Hours  :" . $rows[0]['group_hours'] . "</b>" ?></td>
                <td>
                    <?php
                    $totalEstimateTimeArray[] = $rows[0]['group_hours'];
                    foreach ($rows as $time) {
                        $grouphour = '';
                        $monthlyplans = $this->projects->getMonthlyhours($dateFrom, $dateTo, $time['projectid']);
                        $timearrays = array();
                        foreach ($monthlyplans as $key => $plans) {
                            $timearrays[] = $plans['time'];
                        }
                        $actualTime = $this->TimePredictionModel->sum_time($timearrays);
                        $projectTime[] = $actualTime;
                        $grouphour = $time['group_hours'];
                    }
                    $actualTimes = "00:00:00";
                    $actualTimes = $this->TimePredictionModel->sum_time($projectTime);
                    $projectTime = [];
                    echo "<b>" . $actualTimes . "</b>";
                    ?>

                </td>

                <?php
                $actualgroupTimeSecond = 0;
                $estimategroupTimeSecond = 0;
                $timedifferenceSecond = 0;
                $actualgroupTimeSecond += $this->talentAttendenceSheet->hourToSecondsConvertion($actualTimes);

                $estimategroupTimeSecond += $this->talentAttendenceSheet->monthlyPlanhourToSecondsConvertion($grouphour);
                //get the consumed precentage
                $perecentages = ($actualgroupTimeSecond / $estimategroupTimeSecond) * 100;
                //float to integer conversion
                $consumedPercentages = intval($perecentages);
                ?>
                <?php
                if ($consumedPercentages > 100) {
                    ?>               
                    <td><span class="badge bg-red"><?php echo $consumedPercentages . "%" ?> </span></td> 
                    <?php
                    } else {
                    ?>
                    <td><?php echo $consumedPercentages . "%" ?> </td>
                    <?php
                }
                ?>
                   
                <?php
                //get the todal day in month 
                $total_day = intval(date("t"));
                //get the per day task time based on the estimation time
                $groupHourPerDayseconds = $estimategroupTimeSecond / $total_day;
                //get the todat date today estimate time
                $todays = intval(date("d"));
                $todayAcutalTasksecond = $todays * $groupHourPerDayseconds;
                $todayAcutalTaskhour = $this->talentAttendenceSheet->secondsToMinitues($todayAcutalTasksecond);
                ?>

                <?php
                //get the curent moth and selcted month comparision
                $now = date("Y-m");
                $month2 = $year . "-" . $month;
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
                    if ($todayAcutalTasksecond < $actualgroupTimeSecond) {
                        ?>
                        <td> <?php echo "Yes( Expected Hours :" . $todayAcutalTaskhour . ")" ?></td>
                        <?php
                    } else {
                        ?>
                        <td> </td>
                        <?php
                    }
                    ?>
                    <?php
                }
                ?>
                         <td></td>
            </tr>
            <?php
        }
        //empty of project code get all the project details calculate percentage  and deviation
        foreach ($rows as $row) {
            if($row['group_name'] == ''){
            $totalEstimateTimeArray[] = $row['hours'];
            }
            $monthlyplans = $this->projects->getMonthlyhours($dateFrom, $dateTo, $row['projectid']);
            $timearray = array();
            foreach ($monthlyplans as $key => $taskplan) {
                $timearray[] = $taskplan['time'];
            }
            ?>

            <tr>
                <td><?php echo $sno ?></td>
                <td><?php echo $row['project_code'] ?></td>
                <td><?php echo $row['head_name'] ?></td>
                <td><?php echo $row['hours'] ?></td>
                <?php
                $actualTime = "00:00:00";
                $actualTime = $this->TimePredictionModel->sum_time($timearray);
                $totalActualTimeArray[] = $actualTime;
                ?>
                <td> <?php echo $actualTime ?></td>
                <?php
                $actualTimeSeconds = 0;
                $estimateTimeSeconds = 0;
                $timedifference = 0;
                $actualTimeSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($actualTime);

                $estimateTimeSeconds += $this->talentAttendenceSheet->monthlyPlanhourToSecondsConvertion($row['hours']);
                $timedifferenceSeconds = $actualTimeSeconds - $estimateTimeSeconds;
                //get the consumed precentage
                $perecentage = ($actualTimeSeconds / $estimateTimeSeconds) * 100;
                //float to integer conversion
                $consumedPercentage = intval($perecentage);
                ?>
                <?php
                if ($consumedPercentage > 100) {
                    ?>               
                    <td> 
                        <span class="badge bg-red"><?php echo $consumedPercentage . "%" ?> </span></td>
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
                $timedifferenceHours = $this->talentAttendenceSheet->secondsToMinitues($timedifferenceSeconds);
                ?>

                <?php
                //get the curent moth and selcted month comparision
                $now = date("Y-m");
                $month2 = $year . "-" . $month;
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
                    if ($todayAcutalTaskseconds < $actualTimeSeconds) {
                        
                        ?>
                        <td> <?php echo ( ($row["group_name"] != '') ? "" : "Yes( Expected Hours :" . $todayAcutalTaskhour . ")"); ?></td>
                        <?php
                    } else {
                        ?>
                        <td> </td>
                        <?php
                    }
                    ?>
                    <?php
                }
                ?>


                <td> <?php
            echo anchor('#', 'Add Task', array(
                'class' => 'btn btn-info add_task',
                'data-id' => $row['id'],
                'data-project' => $row['project'],
            ));
            echo "&nbsp;";
            echo anchor('#', 'Delete', array(
                'class' => 'btn btn-danger delete_data',
                'data-id' => $row['id']
            ));
                ?>
                </td>
            </tr>

            <?php
            $sno++;
        }
    }
    ?>
    <tr>
        <td colspan="2"></td>
        <td colspan="2">
            <?php
            //var_dump($totalEstimateTimeArray);die();
            $totalEstimateTime = $this->TimePredictionModel->sum_time($totalEstimateTimeArray);
            echo "<b>Total Estimate Hours  :" . $totalEstimateTime . "</b>";
            ?>
        </td>
        <td colspan="2">
            <?php
            $totalActualTime = $this->TimePredictionModel->sum_time($totalActualTimeArray);
            echo "<b>Total Actual Hours  :" . $totalActualTime . "</b>";
            ?>
        </td>

        <td colspan="2"></td>
    </tr>
    <?php
}
?>
<!-- get monthly plan against the year and month on changing end -->

<!-- load the  popup model -->
<?php
if (isset($projectById)) {
    ?>
    <?php
    foreach ($projectById as $key => $value) {
        ?>
        <div class="row box-header with-border">
            <div class="container-fluid col-md-4">
                <label>Project : </label> <?php echo $value['name']; ?>
            </div>


            <div class="container-fluid col-md-4">
                <label>Head : </label> <?php echo $value['head_name']; ?>
            </div>

            <div class="container-fluid col-md-4">
                <label>Estimate Time : </label> <?php echo $value['hours']; ?>
            </div>
        </div>
        <?php
    }
    ?>
    <section class="content">

        <?php
        echo form_open('Project/ShowTashAddModel', array(
            'class' => 'form-horizontal',
            'id' => 'taskForm'
        ));
        ?>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">

                    <div class="box-body">
                        <div class="col-sm-12" id="showTaskElements">
                            <div class="form-group" >
                                <?php
                                echo form_label('Task', 'task', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-3    ">
                                    <?php
                                    $alltask = $task;
                                    echo form_dropdown('task', $alltask, $tasks, array(
                                        'class' => 'form-control select2 selectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'task',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talents'); ?> </span>
                                </div>
                                <?php
                                echo form_label('PMO', 'talents', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2    ">
                                    <?php
                                    $alltalents = $talent;
                                    echo form_dropdown('talents', $alltalents, $talents, array(
                                        'class' => 'form-control select2 selectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'talents',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talents'); ?> </span>
                                </div>


                                <?php
                                echo form_label('Time', 'time', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  <?php
                            $fromDateText = array(
                                'name' => 'popuptime',
                                'id' => 'popuptime',
                                'placeholder' => '000:00',
                                'class' => 'form-control',
                                'value' => '000:00',
                            );

                            echo form_input($fromDateText);
                                ?>
                                    <span class="text-danger"> <?php echo form_error('popuptime'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-sm-10">  
                                </div> 
                                <div class="col-sm-2">    
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Add Task',
                                        'id' => 'Add_Task',
                                        'class' => 'btn btn-small btn-primary',
                                        'data-id' => $project,
                                        'data-project' => $projectId
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Task </th>
                                        <th>PMO</th>
                                        <th>Estimate Hours</th>
                                        <th>Actual Hours</th>
                                        <th>Total Task Hours</th>
                                        <th>Consumed %</th>
                                        <th>Tends To Deviate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="showTaskPlan">

                                </tbody>

                            </table>
                        </div>

                        <div class="col-sm-12">
                            <table id="table_responsive" class="table table-bordered table-hover showAllTalents">


                            </table>
                        </div>
                        <div class="overlay" id="overlay" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        echo form_close();
        ?>
        <?php
    }
    ?>
    <!-- load the  popup model end -->


    <!-- get all the unassigned project against the year and month change -->

    <?php
    if (isset($unSelectedProject)) {
        $totalUnplannedHoursArray = [];
        $sno = 1;
        ?>
        <thead>
            <tr>

                <th>#</th>
                <th>Project </th>
                <th>Head</th>
                <th>Actual Hours</th>
            </tr>
        </thead>
        <body>
            <?php
            foreach ($unSelectedProject as $key => $project) {
                ?>
            <tr>
                <td>
                    <?php echo $sno; ?>
                </td>
                <td>
                    <?php echo $project['project']; ?>
                </td>
                <td>
                    <?php echo $project['head_name']; ?>
                </td>
                <td>
                    <?php
                    $todayAcutalProjecthour = $this->talentAttendenceSheet->secondsToMinitues($project['time']);
                    $totalUnplannedHoursArray[] = $todayAcutalProjecthour;
                    echo $todayAcutalProjecthour;
                    ?>
                </td>
                <?php
                $sno++;
            }
            ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="2">
                <?php
                $totalUnplannedHours = $this->TimePredictionModel->sum_time($totalUnplannedHoursArray);
                echo "<b>Total Actual Hours :" . $totalUnplannedHours . "</b>";
                ?>
            </td>
            </body>
            <?php
        }
        if (isset($ChangeProject)) {
            echo form_dropdown('name', $project, '', array(
                'class' => 'form-control select2 selectOptions',
                'id' => 'name',
                'required' => 'required'
            ));
        }
        if (isset($ChangegroupProject)) {
            echo form_dropdown('project', $project, $selectedType, array(
                'class' => 'form-control select2 selectOptions',
                'data-validation' => 'required',
                'id' => 'project',
            ));
        }
        ?>
        <!-- load the  popup model  group adding-->
        <?php
        if (isset($groupadding)) {
            ?>

        <div class="row box-header with-border">
            <div class="container-fluid col-md-4">
                <label>Year : </label> <?php echo $year; ?>
            </div>


            <div class="container-fluid col-md-4">
                <label>Month : </label> <?php echo $month; ?>
            </div>


        </div>

        <section class="content">

            <?php
            echo form_open('Project/monthlyPlanGroupGenerator', array(
                'class' => 'form-horizontal',
                'id' => 'taskForm'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">

                        <div class="box-body">
                            <div class="col-sm-12" id="showGroupAdding">
                                <div class="form-group" >
                                    <?php
                                    echo form_label('Group Name', 'group_name', array(
                                        'class' => 'col-sm-1 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3">  <?php
                                $groupNameText = array(
                                    'name' => 'group_name',
                                    'id' => 'group_name',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                );

                                echo form_input($groupNameText);
                                    ?>
                                        <span class="text-danger"> <?php echo form_error('group_name'); ?> </span>
                                    </div>


                                    <?php
                                    echo form_label('Time', 'time', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2">  <?php
                                $fromDateText = array(
                                    'name' => 'popup_grouptime',
                                    'id' => 'popup_grouptime',
                                    'placeholder' => '000:00',
                                    'class' => 'form-control',
                                    'value' => '000:00',
                                );

                                echo form_input($fromDateText);
                                    ?>
                                        <span class="text-danger"> <?php echo form_error('popup_grouptime'); ?> </span>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    < <?php
                                    echo form_label('Project', 'Project', array(
                                        'class' => 'col-sm-1 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3"  id="show_unaasigend_projects">
                                        <?php
                                        echo form_dropdown('project', $project, $selectedType, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'project',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('project'); ?> </span>
                                    </div>

                                    <?php
                                    echo form_label(' Task Time', 'time', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2">  <?php
                                $fromDateText = array(
                                    'name' => 'popup_grouptasktime',
                                    'id' => 'popup_grouptasktime',
                                    'placeholder' => '000:00',
                                    'class' => 'form-control',
                                    'value' => '000:00',
                                );

                                echo form_input($fromDateText);
                                    ?>
                                        <span class="text-danger"> <?php echo form_error('popup_grouptime'); ?> </span>
                                    </div> 
                                    <div class="col-sm-1"> 
                                        <?php
                                        $addButton = array(
                                            'type' => 'submit',
                                            'id' => 'add_grouptask',
                                            'class' => "btn btn-small  btn-info fa fa-plus",
                                            'data-year' => $year,
                                            'data-month' => $month
                                        );
                                        echo form_button($addButton)
                                        ?>
                                    </div> 
                                    <div class="col-sm-2" id="submitButton">    
                                        <?php
                                        $submitButton = array(
                                            'type' => 'submit',
                                            'content' => 'Add Group',
                                            'id' => 'add_group',
                                            'class' => 'btn btn-small btn-primary',
                                            'data-year' => $year,
                                            'data-month' => $month
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <div class="col-sm-12" id="sowgrouptaskdetailstable">
            <table id="table_responsive" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project </th>
                        <th>Group</th>
                        <th> Estimated Hours</th>
                        <th> Action</th>
                        
                    </tr>
                </thead>
                <tbody id="sowgrouptaskdetails">
                </tbody>
            </table>
        </div>
        <?php
        echo form_close();
        ?>
        <?php
    }
    ?>