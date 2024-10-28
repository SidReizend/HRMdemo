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
                            <h3 class="box-title">Talent's Late report</h3>
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
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $serialNumber = 1;
                                    $encashment = 0;
                                    $morningLateCount = 0;
                                    $eveningEarly = 0;
                                    foreach ($monthYearReport as $monthYearReportSpecific) {
                                        $startTime = $this->talentAttendenceSheet->startTime();
                                        $endTime = $this->talentAttendenceSheet->endTime();
                                        $message = '';
                                        $bg_color = "";
                                        $firstInHour = substr($monthYearReportSpecific['in_out_log'], 0, 5).':00';
                                        $lastOutHour = substr($monthYearReportSpecific['in_out_log'], -11,5).':00';
                                        $lastOutSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($lastOutHour);
                                        $firstInSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($firstInHour);
                                        $firstInRequired = $this->talentAttendenceSheet->hourToSecondsConvertion($startTime);
                                        $lastOutRequired = $this->talentAttendenceSheet->hourToSecondsConvertion($endTime);
                                        $show = 0;
                                        if ($firstInSeconds > $firstInRequired){
                                            $morningLateCount++;
                                            $show = 1;
                                        }
                                        if(($lastOutSeconds < $lastOutRequired) && $firstInSeconds){
                                            $eveningEarly++;
                                            $show = 1;
                                        }
                                        if ($show == 1) {
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
                                                    if (strtotime($dateOfAttendence) <= strtotime($lastCronJobDate)) {
                                                        $croneJobStatus = 1;
                                                    }
                                                    if ($monthYearReportSpecific['is_holiday'] == 1) {
                                                        echo 'Holiday';
                                                    } else if ($monthYearReportSpecific['is_leave'] == 1) {
                                                        echo 'Leave';
                                                        if (isset($leaveTypes[$dateOfAttendence])) {
                                                            echo '(' . $this->leaves->getLeaveTypeName($leaveTypes[$dateOfAttendence]) . ')';
                                                        }
                                                    } else if ($monthYearReportSpecific['avg_consider'] == 0) {
                                                        echo 'Absent';
                                                    } else if ($monthYearReportSpecific['avg_consider'] == 1) {
                                                        echo 'Working Day';
                                                    }
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
                                            </tr>
                                            <?php
                                        }
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
                                        <td style="width: 60%">Total Morning Late</td>
                                        <td><span class="badge bg-green"><?php echo $morningLateCount; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Evening Early</td>
                                        <td><span class="badge bg-yellow"> <?php echo $eveningEarly; ?></span></td>
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
    $(document).on("click", "#editTime", function () {
        var ths = $(this);
        var hr = $(this).data('hr');
        var min = $(this).data('min');
        var id = $(this).data('id');
        $('#actualHourSpend').val(hr);
        $('#actualMinuteSpend').val(min);
        $('#attendanceId').val(id);

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
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if ((hr < 23) && (min < 59)) {
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
                    if ((dayStatus == "Absent") || (dayStatus == "Working Day")) {
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
            alertify.alert("Invalid Time");
        }
    });
    $("body").on("change", ".talentChoose", function (e) {
        month = $("#month").val();
        year = $("#year").val();
        talent = $("#talent").val();

        if (month != '' && year != '' && talent == '') {
            talentLoad(month, year, talent);
        }
    });
    function talentLoad(month, year, talent) {
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if (month != '' && year != '' && talent == '') {
            $.ajax({
                url: base_url + "index.php/talentAttendanceDateWise/talentDropDown",
                type: "post", //send it through get method
                data: {
                    month: month,
                    year: year,
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
</script>

