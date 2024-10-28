<?php
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
            <div class="col-sm-3">  <?php
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
            <div class="col-sm-3">  <?php
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
        <div class="col-sm-3">  
            <?php
            $fromDateText = array(
                'name' => 'description',
                'id' => 'description',
                'placeholder' => 'why this ',
                'class' => 'form-control ',
                'rows' => 5,
                'value' => '',
            );

            echo form_input($fromDateText);
            ?>
            <span class="text-danger"> <?php echo form_error('desc'); ?> </span>
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
    <tr>
        <th>#</th>
        <th>Project</th>
        <th>Task</th>
        <th>Time</th>
        <th>description</th>
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
    foreach ($getAllTimeByDate as $timebydate) {
        $timearray[] = $timebydate['time'];
        ?>
        <tr class="notitle">
            <td><?php echo $slno; ?></td>
            <td><?php echo $timebydate['project_code']; ?></td>
            <td><?php
                $task_details = $this->TaskManagementModel->gettaskData($timebydate['task']);
                echo $task_details[0]['task_code'];
                ?></td>
            <td><?php echo $timebydate['time']; ?></td>
            <td><?php echo $timebydate['description']; ?></td>
            <?php
            if (!isset($viewDetails)) {
                ?>
                <td><?php
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
    }
    ?>
    <?php
    if ($CheckActualHourAndOD > 0) {
        $actual = $findActualHourAndOD[0]['actual_hours_spend'];
        $od = $findActualHourAndOD[0]['od_hours'];
        $log = str_replace(",", " ", $findActualHourAndOD[0]['in_out_log']) . '<br/>OD Hours : ' . $od;
        $actual_time_array = array($actual, $od);
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
            <td colspan="4" style="text-align:right;">Total Predicted Time:
                <span style="font-weight: bold;">
                    <?php
                    echo $talent_time;
                    ?>
                </span>

            </td>
            <td>
                Actual Time:
                <span style="font-weight: bold;">
                    <?php echo $actual_time; ?>
                </span>
            </td>
            <?php
            if (!isset($viewDetails)) {
                ?>
                <td>
                    <?php if ($actual_time_second > 0) { ?>
                        <a href="#" class="btn btn-warning logView" data-log="<?php echo $log; ?>">Log</a>
                    <?php } ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
        if ((count($timearray) > 0) && ($actual_time_second > 0) && ($actual_time == $talent_time)) {
            if (($timebydate['is_approved'] != 1) && ($timebydate['send_approval'] != 1)) {
                ?>
                <tr>
                    <td colspan="6" style="text-align: center;">
                        <input type="button" class="btn btn-primary send_approval" Value="Send For Approval" >
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
            <td colspan="7" style="text-align: center;">
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
            <div class="col-sm-3">  <?php
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
            <div class="col-sm-3">  <?php
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

        <div class="col-sm-3">  
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
                <th>Talent</th>
                <th>Task</th>
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
                        echo $data['talent'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $data['task'];
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
                <th>Actions</th>
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
                    <td>
                        <?php
                        if ($data['is_approved'] == 1) {
                            ?>
                            <span id="action_process<?php echo $data['id']; ?>">
                                <button class="btn btn-success">Approved</button>
                                <a href="" class="btn btn-danger click_declain"  data-id="<?php echo $data['id']; ?>" data-talent="<?php echo $data['talent']; ?>"  data-date="<?php echo $data['date']; ?>">Decline</a>

                            </span>
                        <?php } ?>
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
                <th>Date</th>
                <th>Projects</th>
                <th>Time</th>
                <th>Status</th>
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
                    <td>
                        <?php
                        if (($data['is_approved'] != 1) && ($data['is_approved'] != 3)) {
                            ?>
                            <span id="action_process<?php echo $data['id']; ?>">
                                <a href="" class="btn btn-success click_approve" data-id="<?php echo $data['id']; ?>" data-talent="<?php echo $data['talent']; ?>" data-date="<?php echo $data['date']; ?>">Approve</a>
                                &nbsp;
                                <a href="" class="btn btn-danger click_declain"  data-id="<?php echo $data['id']; ?>" data-talent="<?php echo $data['talent']; ?>"  data-date="<?php echo $data['date']; ?>">Decline</a>
                            </span>
                            <?php
                        } else if ($data['is_approved'] == 1) {
                            ?>
                            <button class="btn btn-success">Approved</button>
                        <?php } else { ?>
                            <button class="btn btn-danger">Declined</button>
                        <?php } ?>
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
                <th>Actual Hours Spend</th>
                <th>OD Hours</th>
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
                            echo date('d/m/Y', strtotime($data['attendance_date']));
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['actual_hours_spend'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['od_hours'];
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
                <th>Actual Hours Spend</th>
                <th>OD Hours</th>
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
                            echo date('d/m/Y', strtotime($data['attendance_date']));
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['actual_hours_spend'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $data['od_hours'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo "<a href='AddNewPrediction/date/" . $data['attendance_date'] . "' class='btn btn-info' >Add</a>";
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
            foreach ($ProjectAmountCal as $data) {
                ?>
                <tr class="notitle header">
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
                        $alltime = array();
                        $approved = $this->TimePredictionModel->ProjectAmountCalProjects($project, $data['talent'], $fromdate, $todate, 1, $task);
                        $talent[] = $data['talent'];
                        if ($approved[0]['timeSum'] != "") {
                            $approved_time = $approved[0]['timeSum'];
                        } else {
                            $approved_time = "00:00:00";
                        }
                        $alltime[] = $approved_time;
                        $sum_approved_time[] = $approved_time;
                        echo '<input style="width:100%;" type="text" id="a' . $serialNumber . '" readonly  value="' . $approved_time . '">';
                        ?>
                    </td>
                    <td>
                        <?php
                        $pending = $this->TimePredictionModel->ProjectAmountCalProjects($project, $data['talent'], $fromdate, $todate, 0, $task);
                        //var_dump($pending);
                        if ($pending[0]['timeSum'] != "") {
                            $pending_time = $pending[0]['timeSum'];
                        } else {
                            $pending_time = "00:00:00";
                        }
                        //echo $pending_time;
                        $alltime[] = $pending_time;
                        $sum_pending_time[] = $pending_time;
                        echo '<input type="text" style="width:100%;" id="ma' . $serialNumber . '" data-sl="' . $serialNumber . '"  onblur="calc(' . $serialNumber . ',this.value)" class="timepicker pending_time" value="' . $pending_time . '">';
                        ?>
                    </td>
                    <td>
                        <?php
                        $sumtime = $this->TimePredictionModel->sum_time($alltime);
                        $sum_total_time[] = $sumtime;
                        echo '<input type="text" style="width:100%;" id="s' . $serialNumber . '" readonly class="sumtime" value="' . $sumtime . '">';
                        $timedecimal = $this->TimePredictionModel->decimalHours($sumtime);
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
                                    $projectAndProductivity = $this->TimePredictionModel->TalentTimeSpend($data['talent'], $fromdate, $todate);
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
                                             echo round(($this->TimePredictionModel->decimalHours($totalBilableFormat))*100/($this->TimePredictionModel->decimalHours($totalTimeFormat)),2).'%';
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                             echo round(($this->TimePredictionModel->decimalHours($totalNonBilableFormat))*100/($this->TimePredictionModel->decimalHours($totalTimeFormat)),2).'%';
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
                            <?php
                        } else {
                            ?>
                            <table style="display:none;">
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
                                    $taskAndProductivity = $this->TimePredictionModel->talentTimeSpendGroupByTask($data['talent'], $fromdate, $todate, $project);
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
                        ?>
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
                        echo '<input type="text"  style="width:90%;" id="t' . $serialNumber . '"  readonly class="totaltime" value="' . $productivityUser . '">';
                        ?>
                        <span style="font-weight:bold;">+</span>
                        <table style="display:none;">
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
                                $timeSheets = $this->TimePredictionModel->ProjectTimeSpendDayProjects($project, $data['talent'], $fromdate, $todate, $task);
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
                                            echo $this->TimePredictionModel->productivity($data['talent'], $timeSpend['date']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $timeSpendDecimal = $this->TimePredictionModel->decimalHours($timeSpend['time']);
                                            echo $timeSpendDecimal * $this->TimePredictionModel->productivity($data['talent'], $timeSpend['date']);
                                            ?>
                                        </td>
                                    </tr>

                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
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
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_approved_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_pending_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $this->TimePredictionModel->sum_time($sum_total_time); ?>"></td>
            <td><input type="text" style="width:100%;" id="grand_total" value="<?php echo $grant_total; ?>"></td>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr class="header">
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
        <tr class="header">
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
            <td><input id="totall" style="width:100%;" type="text" value="<?php echo $this->claim->commaSeperated($total_amount + $grant_total); ?>"></td>
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
                <th>Task</th>
                <th>Date</th>
                <th>Time</th>
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
                        <?php
                        $alltime[] = $data['time'];
                        echo $data['time'];
                        ?>
                    </td>
                    <td>
                        <span style="font-weight:bold;">+</span> 
                    </td>

                </tr>
                <?php
//                $dataProject = $this->TimePredictionModel->ProjectReportDataExplode($talent, $data['project_id'], $task, $fromdate, $todate);
                $dataProject = $this->TimePredictionModel->ProjectsReportDataExplode($talent, $data['project_id'], $task, $fromdate, $todate);
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
                            echo $dataPrjt['task'];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo date('d/m/Y', strtotime($dataPrjt['date']));
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $dataPrjt['time'];
                            ?>
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
        <center>Total Time:<?php echo $this->TimePredictionModel->sum_time($alltime); ?></center>
        <div>
            <center>
                <button type="button" id="excel" class="btn btn-small btn-primary">
                    Excel
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
            'time' => $data['time'],
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