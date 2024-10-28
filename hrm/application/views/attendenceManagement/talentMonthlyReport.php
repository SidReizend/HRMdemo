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
                            <h3 class="box-title">Talent's monthly report</h3>
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
                                            'class' => 'form-control select2 selectOptions',
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
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'month',
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
                                        <tr class="notitle" data-toggle="tooltip" title="" data-original-title="<?php echo $message; ?>" style="<?php echo $bg_color; ?>">
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
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['actual_hours_spend'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['od_hours'];
                                                ?>
                                            </td>
                                            <td>
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
                                                $actual_time_array = array($monthYearReportSpecific['actual_hours_spend'], $monthYearReportSpecific['od_hours'], $nonProductiveHours);
                                                $actual_time = $this->TimePredictionModel->sum_time($actual_time_array);
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
                                                    if ($monthYearReportSpecific['is_leave'] == 1) {
                                                        echo 'Leave';
                                                        if (isset($leaveTypes[$dateOfAttendence])) {
                                                            echo '(' . $this->leaves->getLeaveTypeName($leaveTypes[$dateOfAttendence]) . ')';
                                                        }
                                                    } else if ($monthYearReportSpecific['is_holiday'] == 1) {
                                                        echo 'Holiday';
                                                    } else if ($monthYearReportSpecific['avg_consider'] == 0) {
                                                        echo 'Absent';
                                                    }
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
                                    ?>

                                </tbody>
                            </table>
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
                                        <td>Extra Hours</td>
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
    });
    $("body").on("change", ".selectOptions", function (e) {
        month = $("#month").val();
        year = $("#year").val();

        if (month != '' && year != '') {
            $("#showReport").submit();

        }
    });
    $(document).on("click", "#log", function () {
        var id = $(this).data('id');
        var log = id.replace(/\,/g, ' ');
        $('#logDiv').html(log);

    });
</script>

