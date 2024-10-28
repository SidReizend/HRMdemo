<?php
$datatables = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Date"; } 
        .notitle>td:nth-of-type(3):before { content: "Day"; } 
        .notitle>td:nth-of-type(4):before { content: "Act. Hr Spend"; } 
        .notitle>td:nth-of-type(5):before { content: "OD Hours"; } 
        .notitle>td:nth-of-type(6):before { content: "Productive Hours"; } 
        .notitle>td:nth-of-type(7):before { content: "Extra Att. Hr"; } 
        .notitle>td:nth-of-type(8):before { content: "Extra OD Hours"; } 
        .notitle>td:nth-of-type(9):before { content: "Day Status"; } 
        .notitle>td:nth-of-type(10):before { content: "Log"; } 
        .notitle>td:nth-of-type(11):before { content: "Edit"; } 
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Talent's attendance date wise</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'showReport',
                            ));
                            ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <?php
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2    ">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2" id="talents">
                                        <?php
                                        echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2 control-label" style="font-weight: bold;" id="talents">
                                        <?php
                                        //if talent is resigned then shows resigned date
                                        if ($selectedTalent) {
                                            $talentDetails = $this->talent->getTalentDetailsByTalentId($selectedTalent);
                                            if (1 == $talentDetails->is_resigned) {
                                                echo 'Resigned on : ' . date('d/m/Y', strtotime($talentDetails->date_of_resignation));
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Actual Hours Spend</th>
                                        <th>OD Hours</th>
                                        <th>Productive Hours</th>
                                        <th>Extra Attendance Hours</th>
                                        <th>Extra OD Hours</th>
                                        <th>Day Status</th>
                                        <th>Log</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $serialNumber = 1;
                                    $encashment = 0;
                                    $nonProductiveHours = '00:00:00';
                                    foreach ($monthYearReport as $monthYearReportSpecific) {
                                        $message = '';
                                        $bg_color = "";
                                        if (in_array($monthYearReportSpecific['attendance_date'], $odDates)) {
                                            $bg_color = "background-color: #bddbe7";
                                            $message .= "Od Applied.";
                                        }
                                        if (in_array($monthYearReportSpecific['attendance_date'], $leaveAppliedDates)) {
                                            $message .= "Leave Applied.";
                                            $bg_color = "background-color: #C2DDBC";
                                        }
                                        if ($monthYearReportSpecific['encashment'] == 1) {
                                            $bg_color = "background-color: #E7DBBD";
                                            $message .= "Encashment approved.";
                                            $encashment++;
                                        }
                                        ?>
                                        <tr class="notitle" data-toggle="tooltip" 
                                            title="" data-original-title="<?php echo $message; ?>"
                                            style="<?php echo $bg_color; ?>"
                                            id="trId<?php echo $monthYearReportSpecific['id']; ?>">
                                            <td>
                                                <?php
                                                echo $serialNumber++;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['attendance_date_formatted'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['day'];
                                                ?>
                                            </td>
                                            <td id="actual<?php echo $monthYearReportSpecific['id']; ?>">
                                                <?php
                                                echo $monthYearReportSpecific['actual_hours_spend'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['od_hours'];
                                                ?>
                                            </td>
                                            <td id="productive<?php echo $monthYearReportSpecific['id']; ?>">
                                                <?php
                                                echo $monthYearReportSpecific['productive_hrs'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['extra_attendance_hrs'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['extra_od_hrs'];
                                                ?>
                                            </td>
                                            <td  id="dayStatus<?php echo $monthYearReportSpecific['id']; ?>">
                                                <?php
                                                $date = DateTime::createFromFormat('d/m/Y', $monthYearReportSpecific['attendance_date_formatted']);
                                                $dateOfAttendence = $date->format('Y-m-d');
                                                $croneJobStatus = 0;
                                                $actual_time_array = array($monthYearReportSpecific['actual_hours_spend'], $monthYearReportSpecific['od_hours'], $nonProductiveHours);
                                                $actual_time = $this->TimePredictionModel->sum_time($actual_time_array);
                                                if (strtotime($dateOfAttendence) <= strtotime($lastCronJobDate)) {
                                                    $croneJobStatus = 1;
                                                }
                                                if ($monthYearReportSpecific['avg_consider'] == 1) {
                                                    echo 'Worked';
                                                    if ($monthYearReportSpecific['holiday_hr_status'] == 1 && $monthYearReportSpecific['is_holiday'] == 1) {
                                                        echo '-CompOff';
                                                    }
                                                    if ($monthYearReportSpecific['holiday_hr_status'] == 1 && $monthYearReportSpecific['is_leave'] == 1) {
                                                        echo '-LWC';
                                                    }
                                                } else {
                                                    $nonProductiveHours = $actual_time;
                                                    if ($monthYearReportSpecific['is_holiday'] == 1) {
                                                        echo 'Holiday';
                                                    }
                                                    if ($monthYearReportSpecific['is_leave'] == 1) {
                                                        echo 'Leave';
                                                        if (isset($leaveTypes[$dateOfAttendence])) {
                                                            echo '(' . $this->leaves->getLeaveTypeName($leaveTypes[$dateOfAttendence]) . ')';
                                                        }
                                                    }
                                                    if ($monthYearReportSpecific['is_holiday'] == 0 && $monthYearReportSpecific['is_leave'] == 0 && $monthYearReportSpecific['avg_consider'] == 0) {
                                                        echo 'Absent';
                                                    }
                                                }
                                                ?>
                                                <?php
                                                $date_app = $monthYearReportSpecific['attendance_date_formatted'];
                                                $date = DateTime::createFromFormat('d/m/Y', $date_app);
                                                $date_app_status = $date->format('Y-m-d');
                                                $approval_status = $this->TimePredictionModel->ApprovalStatus($selectedTalent, $date_app_status);
                                                $tag = "";


                                                $actual = $monthYearReportSpecific['actual_hours_spend'];
                                                $od = $monthYearReportSpecific['od_hours'];
                                                $actual_time_array = array($actual, $od);
                                                $actual_time = $this->TimePredictionModel->sum_time($actual_time_array);
                                                $actual_time_second = $this->TimePredictionModel->changeToSecond($actual_time);
                                                if ($actual_time_second > 0) {
                                                    $tag = '<i class="fa fa-close"></i>';
                                                }
                                                if (count($approval_status) > 0) {
                                                    if ($approval_status[0]['send_approval'] == 1) {
                                                        $tag = '<i class="fa fa-arrow-circle-right "></i>';
                                                    }
                                                    if ($approval_status[0]['is_approved'] == 1) {
                                                        $tag = '<i class="fa  fa-check "></i>';
                                                    }
                                                }
                                                echo $tag;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('#', 'Log', array(
                                                    'data-toggle' => "modal",
                                                    'data-id' => $monthYearReportSpecific['in_out_log'],
                                                    'data-target' => "#logModel",
                                                    'id' => "log"
                                                ));
                                                ?>
                                            </td>
                                            <?php
                                            $message = '';
                                            $editDatas = $this->talentAttendenceSheet->getEditDeatils($monthYearReportSpecific['id']);
                                            if ($editDatas != '') {
                                                $message .= 'Old Act Hr : ' . $editDatas->old_actual . '
                                                ';
                                                $message .= 'Updated By : ' . $editDatas->first_name . '
                                                ';
                                                $message .= 'Updated On : ' . date('d/m/y', strtotime($editDatas->updated_on)) . '';
                                            } else {
                                                $message .= 'Not Edited';
                                            }
                                            ?>
                                            <td class="edit_details" id="<?php echo $monthYearReportSpecific['id']; ?>"
                                                data-original-title="<?php echo $message; ?>"
                                                data-toggle="tooltip">
                                                    <?php
                                                    if ((0 == $talentDetails->is_resigned && 0 == $croneJobStatus) && (1 == $isManagement) && (0 == $monthYearReportSpecific['holiday_hr_status']) && (0 == $monthYearReportSpecific['encashment'])) {
                                                        echo anchor('#', '<i class="fa fa-fw fa-edit"></i>', array(
                                                            'data-toggle' => "modal",
                                                            'data-id' => $monthYearReportSpecific['id'],
                                                            'data-target' => "#actualModel",
                                                            'data-hr' => date("H", strtotime($monthYearReportSpecific['actual_hours_spend'])),
                                                            'data-min' => date("i", strtotime($monthYearReportSpecific['actual_hours_spend'])),
                                                            'data-name' => $talentDetails->name,
                                                            'id' => "editTime"
                                                        ));
                                                    }
                                                    ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>

                                </tbody>
                            </table>
                            <div id="actualModel" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">
                                                Edit Actual Hour
                                            </h4>
                                            <div class="form-group">
                                                <br/>
                                                Hr : <input type="number" max="23" value="" name="actualHourSpend" id="actualHourSpend">
                                                Min : <input type="number" max="59" value="" name="actualMinuteSpend" id="actualMinuteSpend">
                                                <input type="hidden" id="attendanceId">
                                                <input type="hidden" id="name">
                                            </div>
                                        </div>
                                        <center>
                                            <button type="button" 
                                                    class="btn btn-small btn-primary" 
                                                    name="update"
                                                    id="update"
                                                    data-dismiss="modal">
                                                update
                                            </button>
                                        </center>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                            <div id="logModel" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">
                                                In-out-log
                                            </h4>
                                            <br/>
                                            <div class="form-group" id="logDiv">
                                            </div>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                            <table class="table" style="background-color: #D9D6D6;">
                                <tbody>
                                    <tr>
                                        <td style="width: 60%">Total present days</td>
                                        <td><span class="badge bg-green"><?php echo $presentDay; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Average hour required</td>
                                        <td><span class="badge bg-yellow"> <?php echo $averageHourRequired; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Average hour earned</td>
                                        <td><span class="badge bg-light-blue"><?php echo $averageProductiveHours; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Hour Required</td>
                                        <td><span class="badge bg-red"><?php echo $totalHourRequired; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Hour Earned For Attendance</td>
                                        <td><span class="badge bg-red"><?php echo $totalPrdouctiveHour; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Grand Total Hour</td>
                                        <td><span class="badge bg-red"><?php
                                    $actual_time_array = array($totalPrdouctiveHour);
                                    $actual_time = $this->TimePredictionModel->sum_time($actual_time_array);
                                    echo ($actual_time);
                                    ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Hour Shortage</td>
                                        <td><span class="badge bg-red"><?php echo $hourShortage; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Absent </td>
                                        <td><span class="badge bg-red"><?php echo $totalAbsent; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Traveling Hours </td>
                                        <td>
                                            <span class="badge bg-green"><?php echo $travelHours; ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Extra Hours </td>
                                        <td>
                                            <span class="badge bg-green"><?php echo $extraHours; ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Encashment Available </td>
                                        <td>
                                            <span class="badge bg-green"><?php echo $encashment; ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $('#attendenceTable').DataTable();
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        month = $("#month").val();
        year = $("#year").val();
        talent = $("#talent").val();
        talentLoad(month, year, talent);
    });
    $("body").on("change", ".selectOptions", function (e) {
        month = $("#month").val();
        year = $("#year").val();
        talent = $("#talent").val();

        if (month != '' && year != '' && talent != '') {
            $("#showReport").submit();

        }
    });
    $("body").on("change", ".talentChoose", function (e) {
        month = $("#month").val();
        year = $("#year").val();
        talent = $("#talent").val();

        if (month != '' && year != '') {
            talentLoad(month, year, talent);
        }
    });
    function talentLoad(month, year, talent) {
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if (month != '' && year != '') {
            $.ajax({
                url: base_url + "index.php/talentAttendanceDateWise/talentDropDown",
                type: "post", //send it through get method
                data: {
                    month: month,
                    year: year,
                    talent: talent,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                },
                success: function (response) {
                    $("#talents").html(response);
                    $(".select2").select2();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $('[name="' + csrf_name + '"]').val(cct);
            });
        }
    }
    $(document).on("click", "#editTime", function () {
        var ths = $(this);
        var hr = $(this).data('hr');
        var min = $(this).data('min');
        var name = $(this).data('name');
        var id = $(this).data('id');
        $('#actualHourSpend').val(hr);
        $('#actualMinuteSpend').val(min);
        $('#attendanceId').val(id);
        $('#name').val(name);

    });
    $(document).on("click", "#log", function () {
        var id = $(this).data('id');
        var log = id.replace(/\,/g, ' ');
        $('#logDiv').html(log);

    });
    $(document).on("click", "#update", function () {
        var hr = $('#actualHourSpend').val();
        var min = $('#actualMinuteSpend').val();
        var id = $('#attendanceId').val();
        var name = $('#name').val();
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if ((hr < 23) && (min < 59)) {
            alertify.confirm(" The data of " + name + " is getting modified, are you sure of the modification", function (e) {
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/talentAttendanceDateWise/editActualHourSpend",
                        type: "post", //send it through get method
                        data: {
                            hr: hr,
                            min: min,
                            id: id,
                            '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                        },
                        success: function (response) {
                            if (response == 0) {
                                alertify.alert("some error found");
                            } else {
                                alertify.success("updated Successfully");
                            }
                            var rowData = response.split(',');
                            $("#actual" + id).text(rowData[0]);
                            $("#productive" + id).text(rowData[1]);
                            var dayStatus = $.trim($("#dayStatus" + id).text());
                            if ((dayStatus == "Absent") || (dayStatus == "Worked")) {
                                $("#dayStatus" + id).text(rowData[2]);
                            }
                            $('[data-id=' + id + ']').data('hr', hr)
                            $('[data-id=' + id + ']').data('min', min)
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    });
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        } else {
            alertify.alert("Invalid Time");
        }
    });
</script>
