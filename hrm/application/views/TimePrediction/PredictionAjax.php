<?php
$colspan1 = 1;
$colspan2 = 2;
$colspan3 = 3;
$colspan4 = 4;
$colspan5 = 5;
$colspan6 = 6;
$colspan7 = 7;
$colspan8 = 8;
if (isset($inputelements)) {
    ?>
    <div class="form-group" >
        <div class="col-sm-3">
            <?php
            echo form_dropdown('project', $projects, '', array(
                'class' => 'form-control select2',
                'required' => 'required',
                'id' => 'project_id'
            ));
            ?>
            <span class="text-danger"> <?php echo form_error('project'); ?> </span>
        </div>
        <div class="col-sm-3" id="task_change">  
            <?php
            echo form_dropdown('task', $tasks, '', array(
                'class' => 'form-control select2',
                'required' => 'required',
                'id' => 'task_id'
            ));
            ?>
            <span class="text-danger"> <?php echo form_error('task'); ?> </span>
        </div>
        <?php
        if (isset($Percentage)) {
            ?>
            <div class="col-sm-2">  <?php
                $fromDateText = array(
                    'name' => 'percentage',
                    'id' => 'percentage',
                    'placeholder' => '40',
                    'class' => 'form-control',
                    'value' => '',
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('timepicker'); ?> </span>
            </div>
            <?php
        } else {
            ?>
            <div class="col-sm-2">  <?php
                $fromDateText = array(
                    'name' => 'timepicker',
                    'id' => 'timepicker',
                    'placeholder' => '10:10',
                    'class' => 'form-control timepicker',
                    'value' => '',
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('timepicker'); ?> </span>
            </div>
            <?php
        }
        ?>
        <div class="col-sm-2">  
            <?php
            $fromDateText = array(
                'name' => 'description',
                'id' => 'description',
                'placeholder' => 'why this ',
                'class' => 'form-control ',
                'value' => '',
                'rows' => 5,
            );

            echo form_textarea($fromDateText);
            ?>
            <span class="text-danger"> <?php echo form_error('desc'); ?> </span>
        </div>
        <div class="col-sm-2">
            <?php
            echo form_checkbox(array(
                'name' => 'isOutsideTime',
                'required' => 'required',
                'id' => 'isOutsideTime',
                'style' => 'height:18px;width:18px;margin-top:8px;'
            ));
            ?>
            <span style="position: relative;bottom:4px;">Is outside time</span>
        </div>
    </div>
    <?php
    if (isset($submitShow)) {
        ?>
        <div class="form-group">
            <div class="col-sm-12">  <?php
                $fromDateText = array(
                    'type' => 'button',
                    'name' => 'add',
                    'id' => 'AddRecord',
                    'placeholder' => 'dd/mm/YYYY',
                    'class' => 'form-control btn-primary',
                    'value' => 'Add Record',
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
            </div>

        </div>
        <?php
    }
}
if (isset($showTableData)) {
    ?>
    <input type="hidden" value="<?php echo $isHoliday; ?>" id="isHoliday">
    <?php
    if (!isset($viewDetails)) {
        $colspan1++;
        $colspan2++;
        $colspan3++;
        $colspan4++;
        $colspan5++;
        $colspan6++;
        $colspan7++;
        $colspan8++;
    }
    if (isset($talentName)) {
        ?>
        <tr style="all:revert;">
            <td colspan="<?php echo $colspan3; ?>">
                <div class="container-fluid">
                    <label>Date : </label> <?php echo date('d/m/Y', strtotime($date)); ?>&nbsp;
                    <label>Talent : </label> <?php echo $talentName; ?>
                    <span style="font-weight:bold;color:#E67676;float:right;" class="outsideBtn">&#8709; :  <span style="color:black;font-weight: normal;">outside time</span></span> 
                </div>
            </td>
        </tr>

        <?php
    }
    ?>
    <tr>
        <th>#</th>
        <th>Project/Task/Description</th>
        <th>Time</th>
        <?php
        if (!isset($viewDetails)) {
            ?>
            <th>Actions</th>
            <?php
        }
        ?>
    </tr>
    <?php
    $slno = 1;
    $sum = 00;
    $timearray = array();
    $declineReason = '';
    $projectCode = '';
    $taskCode = '';
    $projectTime = [];
    $taskTime = [];
    $projectTotal = '';
    $taskTotal = '';
    $projectTotalE = '';
    $taskTotalE = '';
    $taskSlNo = 1;
    $projectSlNo = 1;
    $toEnd = count($getAllTimeByDate);
    $endLoop = false;
    $outsideBtn = '';
    $allowance = [];
    foreach ($getAllTimeByDate as $timebydate) {
        if ($timebydate['allowance']) {
            $allowance = $timebydate['project_code'];
        }
        if ($timebydate['is_outside_time']) {
            $outsideBtn = '&nbsp;&nbsp;<span style="font-weight:bold;color:#E67676;" class="outsideBtn">&#8709;</span>';
        } else {
            $outsideBtn = '';
        }
        $timearray[] = $timebydate['time'];
        if (!$declineReason && $timebydate['reason']) {
            $declineReason = $timebydate['reason'];
        }
        if ($slno == 1) {
            $showTaskTime = true;
            $showProjectTime = true;
            $projectCode = $timebydate['project_code'];
            $taskCode = $timebydate['task_code'];
        } else {
            if ($timebydate['project_code'] == $projectCode) {
                $taskSlNo++;
                $showProjectTime = false;
                if ($timebydate['task_code'] == $taskCode) {
                    $showTaskTime = false;
                } else {
                    $showTaskTime = true;
                    $taskTotal = $this->TimePredictionModel->sum_time($taskTime);
                    unset($taskTime);
                }
            } else {
                $projectSlNo++;
                $taskSlNo = 1;
                $showProjectTime = true;
                $showTaskTime = true;
                $projectTotal = $this->TimePredictionModel->sum_time($projectTime);
                $taskTotal = $this->TimePredictionModel->sum_time($taskTime);
                unset($taskTime);
                unset($projectTime);
            }
        }
        $projectTime[] = $timebydate['time'];
        $taskTime[] = $timebydate['time'];
        $projectCode = $timebydate['project_code'];
        $taskCode = $timebydate['task_code'];
        if (0 === --$toEnd) {
            $endLoop = true;
            $projectTotalE = $this->TimePredictionModel->sum_time($projectTime);
            $taskTotalE = $this->TimePredictionModel->sum_time($taskTime);
        }
        if ((true === $showTaskTime || true === $showProjectTime) && $slno != 1) {
            ?>
            <tr>
                <td colspan="2"></td>
                <td colspan="<?php echo $colspan1; ?>">
                    <?php
                    echo (true === $showTaskTime && $slno != 1 ) ? " <label>Task Total :</label> " . $taskTotal : "";
                    echo (true === $showProjectTime && $slno != 1) ? " &nbsp;&nbsp;<label>Project Total :</label> " . $projectTotal : "";
                    ?>
                </td>
            </tr>
            <?php
        }
        if ($showProjectTime) {
            ?>
            <tr class="notitle">
                <td>
                    <label>
                        <?php
                        echo $projectSlNo;
                        ?>
                    </label>
                </td>
                <td colspan="<?php echo $colspan2; ?>">
                    <label><?php
                        echo $timebydate['project_code'];
                        ?>
                    </label>
                </td>
            </tr>
            <?php
        }
        if ($showTaskTime) {
            ?>
            <tr>
                <td></td>
                <td colspan="<?php echo $colspan2; ?>">
                    <label>
                        <?php
                        if ($showTaskTime) {
                            echo $taskSlNo . ") " . $timebydate['task_code'] . '-' . $timebydate['task_desc'];
                        }
                        ?>
                    </label>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td></td>
            <td><?php echo $timebydate['description']; ?></td>
            <td><?php echo $timebydate['time'] . "  " . $outsideBtn; ?></td>
            <?php
            if (!isset($viewDetails)) {
                ?>
                <td>
                    <?php
                    if (($timebydate['is_approved'] != 1) && ($timebydate['send_approval'] != 1)) {
                        echo anchor('#', 'Edit', array(
                            'class' => 'btn btn-info edit_data',
                            'data-id' => $timebydate['id']
                        ));
                        echo "&nbsp;";
                        echo anchor('#', 'Delete', array(
                            'class' => 'btn btn-danger delete_data',
                            'data-id' => $timebydate['id']
                        ));
                    } else if ($timebydate['is_approved'] == 1) {
                        echo anchor('#', 'Approved', array(
                            'class' => 'btn btn-success'
                        ));
                    } else {
                        echo anchor('#', 'Approval Send', array(
                            'class' => 'btn btn-default'
                        ));
                    }
                    ?>
                    <?php
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
        $slno++;
        if ($endLoop) {
            ?>
            <tr>
                <td colspan="2"></td>
                <td colspan="<?php echo $colspan1; ?>">
                    <?php
                    echo "<label>Task Total :</label> " . $taskTotalE;
                    echo "&nbsp;&nbsp;<label>Project Total :</label> " . $projectTotalE;
                    ?>
                </td>

            </tr>
            <?php
        }
    }
    ?>
    <?php
    if ($CheckActualHourAndOD > 0) {
        $actual = $findActualHourAndOD[0]['actual_hours_spend'];
        $log = str_replace(",", " ", $findActualHourAndOD[0]['in_out_log']);
        $actual_time_array = array($actual);
        $actual_time = $this->TimePredictionModel->sum_time($actual_time_array);
    } else {
        $actual_time = "00:00:00";
    }

    $actual_time_second = $this->TimePredictionModel->changeToSecond($actual_time);

    if ((count($timearray) > 0) || ($actual_time_second > 0)) {
        $talent_time = "00:00:00";
        if (count($timearray) > 0) {
            $talent_time = $this->TimePredictionModel->sum_time($timearray);
        }
        ?>

        <tr>
            <td colspan="2">
                Punch Time:
                <span style="font-weight: bold;">
                    <?php echo $actual_time . '<br/> L0G : ' . $log . ''; ?>
                </span>
            </td>
            <td  style="text-align:left;">Total Predicted Time:
                <span style="font-weight: bold;">
                    <?php
                    echo $talent_time;
                    ?>
                </span>

            </td>

            <?php
            if (!isset($viewDetails)) {
                ?>
                <td>
                    <?php // if ($actual_time_second > 0) {         ?>
                        <!--<a href="#" class="btn btn-warning logView" data-log="<?php echo $log; ?>">Log</a>-->
                    <?php // }        ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
        //show food allowance details
        if (isset($viewAllowanceByDate)) {

            if ($timebydate['is_approved'] != 1) {
                $getAllowanceByDate = $this->TimePredictionModel->getAllowanceByDate($date, $talent);
            } else {
                $getAllowanceByDate = $this->TimePredictionModel->getApprovedAllowanceByDate($date, $talent);
            }
            ?>
            <tr>
                <?php
                if (isset($getAllowanceByDate)) {
                    // foreach ($getAllowanceByDate as $allowance) {
                    ?>
                    <td colspan="2">
                        <b>  Food Allowance :</b>
                        <?php echo ( $getAllowanceByDate->food_allowance) ? "Yes" : "No" ?></td>
                    <td>
                        <b> Food And Travel Allowance : </b>
                        <?php echo ( $getAllowanceByDate->food_andtravel_allowance) ? "Yes" : "No" ?></td>


                    <?php
                    if (isset($showrow)) {
                        ?>
                        <td> </td>

                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
        }
        ?>
        <?php
        if (count($timearray) > 0) {
            if (($timebydate['is_approved'] != 1) && ($timebydate['send_approval'] != 1) && !isset($viewDetails)) {
                if ($viewAllTimeSheet != 1) {//checking if popup is coming from od approval page.
                    if (count($allowance) > 0) {
                        ?>
                        <tr>
                            <td colspan="2">

                                <label>
                                    <input type="radio" name="allowance"id="food_allowance" value="1">

                                    Food Allowance
                                </label>
                            </td>


                            <td>
                                <label>
                                    <input type="radio" name="allowance" id="food_andtravel_allowance" value="2">
                                    Food and local travel allowance
                                </label>

                            </td>
                            <td>

                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: right;">
                            <?php
                            if ($declineReason) {
                                echo '<b style="color: red">Decline Reason :</b> ' . $declineReason;
                            }
                            ?>
                        </td>
                        <td colspan="<?php $colspan1; ?>" style="text-align: center;">
                            <input type="button" class="btn btn-primary send_approval" Value="Send For Approval" >
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>

        <?php
        if (isset($viewDetails)) {
            if (($timebydate['is_approved'] != 1) && ($timebydate['is_approved'] != 3)) {
                ?>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($declineReason) {
                            echo '<b style="color: red">Decline Reason :</b> ' . $declineReason;
                        }
                        ?>
                    </td>
                    <td>
                        <span id="action_process<?php echo $timebydate['id']; ?>">
                            <a href="" class="btn btn-success click_approve" data-id="<?php echo $timebydate['id']; ?>"
                               data-talent="<?php echo $talent; ?>" data-date="<?php echo $date; ?>">Approve</a>
                            <a href="" style="padding-left: 10px;" class="btn btn-danger click_declain"  data-id="<?php echo $timebydate['id']; ?>" 
                               data-talent="<?php echo $talent; ?>"  data-date="<?php echo $date; ?>">Decline</a>
                        </span>
                    </td>
                </tr>
                <?php
            } else if ($timebydate['is_approved'] == 1) {
                ?>
                <tr>
                    <td colspan="2">
                    </td>
                    <td colspan="1">
                        <button class="btn btn-success">Approved</button>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="5">
                        <?php
                        if ($declineReason) {
                            echo '<b style="color: red">Decline Reason :</b> ' . $declineReason;
                        }
                        ?>
                    </td>
                    <td colspan="2">
                        <button class="btn btn-danger">Declined</button>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        <?php
    }
    ?>
    <?php
}
if (isset($showTableDataTalent)) {
    ?>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Project</th>
        <th>Task</th>
        <th>Percentage</th>
        <th>description</th>
        <th>Added By</th>
        <th>Actions</th>
    </tr>
    <?php
    $slno = 1;
    $sum = 00;
    $timearray = array();
    $percentage = 0;
    foreach ($getAllTimeByDate as $timebydate) {
        ?>
        <tr>
            <td><?php echo $slno; ?></td>
            <td><?php echo date('d/m/Y', strtotime($timebydate['date'])); ?></td>
            <td><?php echo $timebydate['project_code']; ?></td>
            <td><?php
                $task_details = $this->TaskManagementModel->gettaskData($timebydate['task']);
                echo $task_details[0]['task_code'];
                ?></td>
            <td><?php
                $percentage = $percentage + $timebydate['percentage'];
                echo $timebydate['percentage'];
                ?></td>
            <td><?php echo $timebydate['description']; ?></td>
            <td><?php echo $this->Talent->getTalentNameById($timebydate['added_by']); ?></td>
            <td><?php
                if (($timebydate['is_approved'] != 1) || ($pendingSheet > 0)) {
                    echo anchor('#', 'Edit', array(
                        'class' => 'btn btn-info edit_data',
                        'data-id' => $timebydate['id']
                    ));
                    echo "&nbsp;";
                    echo anchor('#', 'Delete', array(
                        'class' => 'btn btn-danger delete_data',
                        'data-id' => $timebydate['id']
                    ));
                } else if ($timebydate['is_approved'] == 1) {
                    echo anchor('#', 'Approved', array(
                        'class' => 'btn btn-success'
                    ));
                } else {
                    echo anchor('#', 'Approval Send', array(
                        'class' => 'btn btn-default'
                    ));
                }
                ?>
            </td>
        </tr>
        <?php
        $slno++;
    }
    ?>
    <?php
    if (($percentage == 100) && ($pendingSheet != 0)) {
        ?>
        <tr>
            <td colspan="8" style="text-align: center;">
                <input type="button" class="btn btn-primary send_approval" Value="Send For Approval" >
            </td>
        </tr>
        <?php
    }
    ?>
    <?php
}
if (isset($EditData)) {
    ?>
    <div class="form-group" >
        <div class="col-sm-3">
            <?php
            echo form_dropdown('project', $projects, $project, array(
                'class' => 'form-control select2',
                'required' => 'required',
                'id' => 'project_id'
            ));
            ?>
            <span class="text-danger"> <?php echo form_error('project'); ?> </span>
        </div>
        <div class="col-sm-3" id="task_change">   
            <?php
            echo form_dropdown('task', $tasks, $task_selected, array(
                'class' => 'form-control select2',
                'required' => 'required',
                'id' => 'task_id'
            ));
            ?>
            <span class="text-danger"> <?php echo form_error('task'); ?> </span>
        </div>
        <?php
        if (isset($percentage)) {
            ?>
            <div class="col-sm-2">  <?php
                $fromDateText = array(
                    'name' => 'percentage',
                    'id' => 'percentage',
                    'placeholder' => '40',
                    'class' => 'form-control',
                    'value' => $percentage
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('timepicker'); ?> </span>
            </div>
            <?php
        } else {
            ?>
            <div class="col-sm-2">  <?php
                $fromDateText = array(
                    'name' => 'timepicker',
                    'id' => 'timepicker',
                    'placeholder' => '10:10',
                    'class' => 'form-control timepicker',
                    'value' => $time
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('timepicker'); ?> </span>
            </div>
            <?php
        }
        ?>

        <div class="col-sm-2">  
            <?php
            $fromDateText = array(
                'name' => 'description',
                'id' => 'description',
                'placeholder' => 'why this ',
                'class' => 'form-control ',
                'rows' => 5,
                'value' => $description
            );

            echo form_input($fromDateText);
            ?>
            <span class="text-danger"> <?php echo form_error('desc'); ?> </span>
        </div>
        <div class="col-sm-2">
            <?php
            echo form_checkbox(array(
                'name' => 'isOutsideTime',
                'required' => 'required',
                'id' => 'isOutsideTime',
                'style' => 'height:18px;width:18px;margin-top:8px;',
                'checked' => $is_outside_time == 1 ? true : false
            ));
            ?>
            <span style="position: relative;bottom:4px;">Is outside time</span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">  <?php
            $fromDateText = array(
                'type' => 'button',
                'name' => 'add',
                'id' => 'EditRecord',
                'placeholder' => 'dd/mm/YYYY',
                'class' => 'form-control btn-info',
                'value' => 'Update Record',
                'data-id' => $id
            );

            echo form_input($fromDateText);
            ?>
            <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
        </div>

    </div>
    <?php
}
if (isset($ReportData)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Project</th>
                <th>Task</th>
                <th>Talent</th>
                <th>Track</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            $alltime = array();
            foreach ($ReportData as $data) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo ++$serialNumber;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['project'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['task'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['talent'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['track'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date('d/m/Y', strtotime($data['date']));
                        ?>
                    </td>
                    <td>
                        <?php
                        $alltime[] = $data['time'];
                        echo $data['time'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['description'];
                        ?>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
    if (count($alltime) > 0) {
        ?>
        <center>Total Time:<?php echo $this->TimePredictionModel->sum_time($alltime); ?></center>
        <?php
    }
}
if (isset($ChangeTask)) {
    //echo $this->db->last_query();
    echo form_dropdown('task', $tasks, '', array(
        'class' => 'form-control select2',
        'required' => 'required',
        'multiple' => '',
        'id' => 'task_id'
    ));
}
if (isset($ChangeTaskSingle)) {
    //echo $this->db->last_query();
    echo form_dropdown('task', $tasks, '', array(
        'class' => 'form-control select2',
        'required' => 'required',
        'id' => 'task_id'
    ));
}
if (isset($getTaskByProjectActual)) {
    //echo $this->db->last_query();
    echo form_dropdown('target_task_code', $tasks, '', array(
        'class' => 'form-control select2',
        'required' => 'required',
        'id' => 'target_task_code'
    ));
}
if (isset($getTaskByProject)) {
    //echo $this->db->last_query();
    echo form_dropdown('task_code', $tasks, '', array(
        'class' => 'form-control select2',
        'required' => 'required',
        'id' => 'task_code'
    ));
}
if (isset($getTaskByProjectById)) {
    //echo $this->db->last_query();
    echo form_dropdown("$task_id", $tasks, '', array(
        'class' => 'form-control select2',
        'required' => 'required',
        'id' => "$task_id"
    ));
}
if (isset($HomeGetTotalDataApproved)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Date</th>
                <th>Projects</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            foreach ($HomeGetTotalDataApproved as $data) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo ++$serialNumber;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date('d/m/Y', strtotime($data['date']));
                        ?>
                    </td>
                    <td>
                        <?php
                        $ProjectsOfDate = $this->TimePredictionModel->ProjectsOfDate($data['date'], $data['talent'], $data['is_approved']);
                        foreach ($ProjectsOfDate as $project) {
                            echo '<button class="btn btn-info">' . $project['project_code'] . '</button>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $TimeOfDate = $this->TimePredictionModel->TimeOfDate($data['date'], $data['talent'], $data['is_approved']);
                        $time_array = array();
                        foreach ($TimeOfDate as $time) {
                            $time_array[] = $time['time'];
                        }
                        echo $this->TimePredictionModel->sum_time($time_array);
                        ?>
                    </td>
                    <td>
                        <a href="#" id="view_details" 
                           data-talent="<?php echo $data['talent']; ?>" data-approv="<?php echo $data['is_approved']; ?>"
                           data-date="<?php echo $data['date']; ?>" >View Details
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
if (isset($HomeGetTotalData)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Name</th>
                <th>Date</th>
                <th>Projects</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            foreach ($HomeGetTotalData as $data) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo ++$serialNumber;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['name'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date('d/m/Y', strtotime($data['date']));
                        ?>
                    </td>
                    <td>
                        <?php
                        $ProjectsOfDate = $this->TimePredictionModel->ProjectsOfDate($data['date'], $data['talent'], $data['is_approved']);
                        foreach ($ProjectsOfDate as $project) {
                            echo '<button class="btn btn-info">' . $project['project_code'] . '</button>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $TimeOfDate = $this->TimePredictionModel->TimeOfDate($data['date'], $data['talent'], $data['is_approved']);
                        $time_array = array();
                        foreach ($TimeOfDate as $time) {
                            $time_array[] = $time['time'];
                        }
                        echo $this->TimePredictionModel->sum_time($time_array);
                        ?>
                    </td>
                    <td>
                        <a href="#" id="view_details" 
                           data-talent="<?php echo $data['talent']; ?>" data-approv="<?php echo $data['is_approved']; ?>"
                           data-date="<?php echo $data['date']; ?>" >View Details
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
if (isset($PendingSubmitReport)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Talent</th>
                <th>Date</th>
                <th>Timesheet manager</th>
                <th>Apply Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            foreach ($PendingSubmitReport as $data) {
                $approval_status = $this->TimePredictionModel->ApprovalStatus($data['talent_id'], $data['attendance_date']);

                if (count($approval_status) == 0) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo ++$serialNumber;
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['name'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo date('d/m/Y', strtotime($data['date']));
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['reporting_manager_name'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['apply_status'];
                            ?>
                        </td>

                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>  
    <?php
}
if (isset($userProductivity)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Date</th>
                <th>Productivity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            foreach ($userProductivity as $data) {
                ?>
                <tr class="notitle">
                    <td>
                        <?php
                        echo ++$serialNumber;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date('d/m/Y', strtotime($data['date']));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['productivity'];
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
if (isset($PendingSubmitReportTalent)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            foreach ($PendingSubmitReportTalent as $data) {
                $approval_status = $this->TimePredictionModel->ApprovalStatus($data['talent_id'], $data['attendance_date']);

                if (count($approval_status) == 0) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo ++$serialNumber;
                            ?>
                        </td>
                        <td>
                            <?php
                            echo date('d/m/Y', strtotime($data['date']));
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($dashboard) {
                                echo "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Add</a>";
                            } else {
                                echo "<a href='AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Add</a>";
                            }
                            ?>
                        </td>

                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>  
    <?php
}
if (isset($ProjectAmountCal)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Talent</th>
                <th>Track</th>
                <th>Approved Time</th>
                <th>Pending Time</th>
                <th>Total Time</th>
                <th>Total</th>
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
                        echo $data['name'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['track'];
                        ?>
                    </td>
                    <td>
                        <?php
                        $alltime = array();
                        $approved = $this->TimePredictionModel->ProjectAmountCalProjects($project, $data['talent'], $fromdate, $todate, 1, $task);
                        $talent[] = $data['talent'];
                        if ($approved[0]['timeSum'] != "") {
                            $approved_time = $this->talentAttendenceSheet->secondsToMinitues($approved[0]['timeSum']);
                        } else {
                            $approved_time = "00:00:00";
                        }
                        $alltime[] = $approved_time;
                        $sum_approved_time[] = $approved_time;
                        echo $approved_time;
                        ?>
                    </td>
                    <td>
                        <?php
                        $pending = $this->TimePredictionModel->ProjectAmountCalProjects($project, $data['talent'], $fromdate, $todate, 0, $task);
                        //var_dump($pending);
                        if ($pending[0]['timeSum'] != "") {
                            $pending_time = $this->talentAttendenceSheet->secondsToMinitues($pending[0]['timeSum']);
                        } else {
                            $pending_time = "00:00:00";
                        }
                        //echo $pending_time;
                        $alltime[] = $pending_time;
                        $sum_pending_time[] = $pending_time;
                        echo $pending_time;
//                        echo '<input type="text" readonly style="width:100%;" id="ma' . $serialNumber . '" data-sl="' . $serialNumber . '"  /*onblur="calc(' . $serialNumber . ',this.value)" class="timepicker pending_time" value="' . $pending_time . '">';
                        ?>
                    </td>
                    <td>
                        <?php
                        $sumtime = $this->TimePredictionModel->sum_time($alltime);
                        $sum_total_time[] = $sumtime;
                        echo $sumtime;
                        $timedecimal = $this->TimePredictionModel->decimalHours($sumtime);
                        ?>
                        <span id="<?php echo $i . 'total'; ?>" >
                        </span>
                    </td>
                    <td>
                        <?php
                        $productivityUser = 0;
                        $timeSheets = $this->TimePredictionModel->ProjectTimeSpendDayProjects($project, $data['talent'], $fromdate, $todate, $task);
                        foreach ($timeSheets as $timeSpend) {
                            $timeSpendDecimal = $this->TimePredictionModel->decimalHours($timeSpend['time']);
                            $productivityUser += ($timeSpendDecimal * $this->TimePredictionModel->productivity($data['talent'], $timeSpend['date']));
                        }
                        $productivityUser = number_format((float) $productivityUser, 2, '.', '');
                        echo $productivityUser;
                        ?>
                        <span id="<?php echo $i . 'complete'; ?>" >
                        </span>
                        <span class="expand" style="font-weight:bold;">+</span>
                    </td>

                </tr>
                <?php
                $grant_total = $grant_total + $productivityUser;
            }
            ?>

        </tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_approved_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_pending_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_total_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $grant_total; ?>"></td>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr class="exp-toggle">
            <td>Settled Exp:</td>
            <?php
            $ClaimAmount = $this->TimePredictionModel->ClaimAmountProjects($project, 1, $task, $fromdate, $todate, $talent) + $this->TimePredictionModel->transactionAmountProjects($project, 1, $task, $fromdate, $todate);
            $claimsUnderProject = $this->TimePredictionModel->ClaimsUnderProjects($project, 1, $task, $fromdate, $todate, $talent);
            $transUnderProject = $this->TimePredictionModel->TransUnderProjects($project, 1, $task, $fromdate, $todate, $talent);
            ?>
            <td>
                <input type="text" style="width:95%;" id="settled" readonly="readonly" value="<?php echo $this->claim->commaSeperated($ClaimAmount); ?>">
                <span style="font-weight:bold;">+</span>
                <?php
                if (!empty($claimsUnderProject)) {
                    ?>
                    <table class="table table-bordered" style="display:none;">
                        <thead>
                            <tr>
                                <th colspan="8" style="text-align:center;">
                                    Claims
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th>
                                    Sl. No.
                                </th>
                                <th>
                                    Talent
                                </th>
                                <th>
                                    Project
                                </th>
                                <th>
                                    Settlement
                                </th>
                                <th>
                                    Category
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Date Of Approval
                                </th>
                                <th>
                                    Approved By
                                </th>
                                <th>
                                    Creating User
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialNumber = 0;
                            foreach ($claimsUnderProject as $claim) {
                                ?>
                                <tr class="claim">
                                    <td>
                                        <?php
                                        echo ++$serialNumber;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['name'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['project'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['settlement'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['value'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $this->claim->commaSeperated($claim['amount']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo date('d/m/Y', strtotime($claim['date_of_approval']));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['approved_by'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['created_by'];
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
                if (!empty($transUnderProject)) {
                    ?>
                    <table class="table table-bordered" style="display:none;">
                        <thead>
                            <tr>
                                <th colspan="8" style="text-align:center;">
                                    Trans
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Amount</th>
                                <th>Date of approval</th>
                                <th>Creating User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialNumber = 0;
                            foreach ($transUnderProject as $claim) {
                                ?>
                                <tr class="claim">
                                    <td>
                                        <?php
                                        echo ++$serialNumber;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['company_name'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['project'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $this->claim->commaSeperated($claim['amount']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo date('d/m/Y', strtotime($claim['date_of_approval']));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['created_by'];
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
                ?>
            </td>
        </tr>
        <tr class="exp-toggle">
            <?php
            $pendingAmount = $this->TimePredictionModel->ClaimAmountProjects($project, 0, $task, $fromdate, $todate, $talent);
            $claimsUnderProject = $this->TimePredictionModel->ClaimsUnderProjects($project, 0, $task, $fromdate, $todate, $talent);
            ?>
            <td>Pending Exp:</td>
            <td>
                <input id="pending" style="width:95%;" type="text" value="<?php echo $this->claim->commaSeperated($pendingAmount); ?>">
                <span style="font-weight:bold;">+</span>
                <?php
                if (!empty($claimsUnderProject)) {
                    ?>
                    <table class="table table-bordered" style="display:none;">
                        <thead>
                            <tr>
                                <th colspan="8" style="text-align:center;">
                                    Claims
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th>
                                    Sl. No.
                                </th>
                                <th>
                                    Talent
                                </th>
                                <th>
                                    Project
                                </th>
                                <th>
                                    Settlement
                                </th>
                                <th>
                                    Category
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Date Of Approval
                                </th>
                                <th>
                                    Approved By
                                </th>
                                <th>
                                    Creating User
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialNumber = 0;
                            foreach ($claimsUnderProject as $claim) {
                                ?>
                                <tr class="claim">
                                    <td>
                                        <?php
                                        echo ++$serialNumber;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['name'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['project'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['settlement'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['value'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $this->claim->commaSeperated($claim['amount']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo date('d/m/Y', strtotime($claim['date_of_approval']));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['approved_by'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $claim['created_by'];
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
                ?>
            </td>
        </tr>
        <tr>
            <?php
            $total_amount = $pendingAmount + $ClaimAmount;
            $total_amount = number_format((float) $total_amount, 2, '.', '');
            ?>
            <td>Total Exp:</td>
            <td><input id="totall" style="width:100%;" type="text" value="<?php echo $this->claim->commaSeperated($total_amount); ?>"></td>
        </tr>
        <tr>
            <td>Grand Total:</td>
            <td><input id="grand-total" style="width:100%;" type="text" value="<?php echo $this->claim->commaSeperated($total_amount + $grant_total); ?>"></td>
        </tr>
    </table>


    <?php
}
if (isset($ProjectReportData)) {
    ?>

    <table id="table_responsive" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Sl No.</th>
                <th>Project</th>
                <th>Talent</th>
                <th>Track</th>
                <th>Task Code</th>
                <th>Task</th>
                <th>Date</th>
                <th>Estimated Hours</th>
                <th>Invoice Status</th>
                <th>Advance Invoice Number</th>
                <th>Advance Invoice Date</th>
                <th> Invoice Number</th>
                <th> Invoice Date</th>
                <th>Time</th>
                <th>Difference</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = 0;
            $alltime = array();
            foreach ($ProjectReportData as $data) {
                ?>
                <tr class="header">
                    <td>
                        <?php
                        echo ++$serialNumber;
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['project'];
                        ?>
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>
                    </td>
                    <td>

                    </td>
                    <td>
                        <?php
                        $estimatedInSeconds = $data['estimated_hours'] * 3600;
                        echo $this->talentAttendenceSheet->secondsToMinitues($estimatedInSeconds);
                        ?>
                    </td>


                    <td>

                    </td>
                    <td>
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>
                        <?php
                        $alltime[] = $this->talentAttendenceSheet->secondsToMinitues($data['time']);
                        echo $this->talentAttendenceSheet->secondsToMinitues($data['time']);
                        $diffrence = abs($data['time'] - $estimatedInSeconds);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->talentAttendenceSheet->secondsToMinitues($diffrence);
                        ?>
                    </td>
                    <td>
                        <span style="font-weight:bold;">+</span> 
                    </td>

                </tr>
                <?php
//                $dataProject = $this->TimePredictionModel->ProjectReportDataExplode($talent, $data['project_id'], $task, $fromdate, $todate);
                $dataProject = $this->TimePredictionModel->ProjectsReportDataExplode($talent, $data['project_id'], $task, $fromdate, $todate, $track, $advanceinvoicefromdate, $advanceinvoicetodate, $completedinvoicefromdate, $completedinvoicetodate, $invoicestatus);
                $totalEstimatedHours = 0;
                $serialNumber2 = 0;
                foreach ($dataProject as $dataPrjt) {
                    ?>
                    <tr class="data">
                        <td>
                            <?php
                            echo ++$serialNumber2;
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['project'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['talent'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['track'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['task'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['task_desc'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo date('d/m/Y', strtotime($dataPrjt['date']));
                            ?>
                        </td>
                        <td>
                        </td>
                        <td>
                            <?php
                            $invice_status = $this->TimePredictionModel->getSelectedInvoiceStatuses($dataPrjt['invoice_status']);
                            echo $invice_status['status'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $advance_invoice_number = $dataPrjt['advance_invoice_number'];
                            if ($dataPrjt['advance_invoice_number'] == 0) {
                                $advance_invoice_number = '';
                            }
                            echo $advance_invoice_number;
                            ?>
                        </td>
                        <td>
                            <?php
                            $advance_invoice_date = date('d/m/Y', strtotime($dataPrjt['advance_invoice_date']));
                            if ($dataPrjt['advance_invoice_date'] == null) {
                                $advance_invoice_date = '';
                            }
                            echo $advance_invoice_date;
                            ?>
                        </td>
                        <td>
                            <?php
                            $invoice_number = $dataPrjt['invoice_number'];
                            if ($dataPrjt['invoice_number'] == 0) {
                                $invoice_number = '';
                            }
                            echo $invoice_number;
                            ?>
                        </td>
                        <td>
                            <?php
                            $invoice_date = date('d/m/Y', strtotime($dataPrjt['invoice_date']));
                            if ($dataPrjt['invoice_date'] == null) {
                                $invoice_date = '';
                            }
                            echo $invoice_date;
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['time'];
                            ?>
                        </td>
                        <td>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['description'];
                            ?>
                        </td>

                    </tr>
                    <?php
                }
                ?>
                <?php
            }
            ?>
        </tbody>
    </table>

    <?php
    if (count($alltime) > 0) {
        ?>
        <center>Total Time:<?php echo $this->TimePredictionModel->sum_time($alltime); ?></center><br/>
        <div>
            <center>
                <button type="button" id="excel" class="btn btn-small btn-primary">
                    Excel
                </button>
                <button type="button" id="detailedExcel" class="btn btn-small btn-primary">
                    Detailed Excel
                </button>
            </center>
        </div>
        <?php
    }
}
if (isset($totalTimeSpentForInterval)) {
    ?>
    <center>Total Time : <?php echo $totalTimeSpentForInterval; ?></center>
    <?php
}
if (isset($ProjectReportPieData)) {

    function random_color_part() {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    function random_color() {
        return random_color_part() . random_color_part() . random_color_part();
    }

    $response = array();
    foreach ($ProjectReportPieData as $data) {
        $response[] = array(
            'project' => $data['project'],
            'time' => $this->talentAttendenceSheet->secondsToMinitues($data['time']),
            'color' => random_color()
        );
    }
    echo json_encode($response);
}
if (isset($canvas)) {
    ?>
    <div class="box-body">
        <canvas id="pieChart" style="height:250px"></canvas>
    </div>
    <?php
}
if (isset($talentsProductivity)) {
    foreach ($talentsProductivity as $talent) {
        $prdtty = $this->TimePredictionModel->Productivity($talent['id'], $date);
        ?>
        <div class="form-group">
            <?php
            echo form_label($talent['name'], 'TaskCode', array(
                'class' => 'col-sm-2 control-label talentName',
                'data-talent' => $talent['id'],
                'data-toggle' => "modal",
                'data-target' => "#productivityModal",
                'data-name' => $talent['name'],
                'style' => 'cursor:pointer'
            ));
            ?>
            <div class="col-sm-4">  <?php
                $fromDateText = array(
                    'name' => 'taskcode',
                    'id' => 'taskcode',
                    'class' => 'form-control txtpdt',
                    'required' => 'required',
                    'value' => $prdtty,
                    'type' => 'text',
                    'data-talent' => $talent['id']
                );

                echo form_input($fromDateText);
                ?>
                <span class="text-danger"> <?php echo form_error('taskcode'); ?> </span>
            </div>
        </div>
        <?php
    }
}
?>
<?php
if (isset($talentAllowance)) {
    $si = 1;
    ?>
    <table id="table_responsive" class="table table-bordered table-hover" >
        <thead style="text-align:center;">
            <tr>
                <th>Sl No.</th>
                <th>Talent</th>
                <th>Food Allowance</th>
                <th>Food And Travel Allowance</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody >

            <?php
            foreach ($talentAllowance as $allowance) {
                if ($allowance['count_foodallowance'] != 0 || $allowance['count_food_andtravel_allowance'] != 0) {
                    ?>

                    <tr>
                        <td><?php echo $si ?></td>
                        <td> <?php echo $allowance['talent'] ?></td>
                        <td> <?php echo $allowance['count_foodallowance'] ?></td>
                        <td> <?php echo $allowance['count_food_andtravel_allowance'] ?></td>
                        <td> <a href="#" id="allowance_details" data-talentname="<?php echo $allowance['talent']; ?>"
                                data-talent="<?php echo $allowance['id']; ?>" >View Details
                            </a></td>
                    </tr> 


                    <?php
                    $si++;
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}
if (isset($talentDetailedAllowance)) {
    $si = 1;
    ?>
    <div class="row box-header with-border">
        <div class="container-fluid col-md-4">
            <label>Talent : </label> <?php echo $talentname; ?>
        </div>
    </div>
    <table id="table_responsive" class="table table-bordered table-hover popupAllowance">
        <thead>
            <tr>
                <th>#</th>
                <th>Date </th>
                <th>Food Allowance</th>
                <th>Food And Travel Allowance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($talentDetailedAllowance as $allowance) {
                if ($allowance['food_allowance'] != 0 || $allowance['food_andtravel_allowance'] != 0) {
                    ?>

                    <tr>
                        <td><?php echo $si ?></td>
                        <td> <?php echo $allowance['date'] ?></td>
                        <td> <?php echo ($allowance['food_allowance'] != 0) ? "Yes" : "" ?></td>
                        <td> <?php echo ($allowance['food_andtravel_allowance'] != 0) ? "Yes" : "" ?></td>
                    </tr> 
                    <?php
                    $si++;
                }
            }
            ?>
        </tbody>

    </table>


    <?php
}
?>   
