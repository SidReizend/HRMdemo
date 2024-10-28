<?php
//echo date('Y-m-d H:i:s');
$chartjs = 1;
$datepicker = 1;
$select2 = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
$atts = array(
    'width' => 800,
    'height' => 600,
    'scrollbars' => 'yes',
    'status' => 'yes',
    'resizable' => 'yes',
    'screenx' => 0,
    'screeny' => 0,
    'location' => 0,
    'window_name' => '_blank',
    'title' => 'View Policy',
    'class' => 'product-title',
);
?>
<style>
    ul.policies {
        list-style-type: square;
    }
    ul.policies li {
        margin-top: 15px;
        font-size: 14px;
    }
    .font-count{
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container">
        <section class="content">
            <?php
            if (true === $approval_user) {
                ?>
                <div class="row">
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <span class="font-count">
                                    <?php
                                    echo $HomeGetTotalData;
                                    ?>
                                </span>
                                <strong>Time Approvals</strong>
                            </div>
                            <?php
                            echo anchor('TimePredictionCntrl/HomeDataApproval?dashbord=True', 'View All <i class="fa fa-arrow-circle-right"></i>', array('class' => 'small-box-footer'));
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <span class="font-count">
                                    <?php
                                    echo $leaveWaiting;
                                    ?>
                                </span>
                                <strong>Leave Approvals</strong>
                            </div>

                            <?php
                            echo anchor('leaveApproval', 'View All <i class="fa fa-arrow-circle-right"></i>', array('class' => 'small-box-footer'));
                            ?>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-gray">

                            <div class="inner">
                                <span class="font-count">
                                    <?php
                                    echo $compWaiting;
                                    ?>
                                </span>
                                <strong>Compoff Approvals</strong>
                            </div>
                            <?php
                            echo anchor('compOffManagement', 'View All <i class="fa fa-arrow-circle-right"></i>', array('class' => 'small-box-footer'));
                            ?>
                        </div>
                    </div>
                    <!-- ./col -->
                    <?php
                    if ($this->defaultValues->getValue(26)) {
                        ?>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-purple">

                                <div class="inner">
                                    <span class="font-count">
                                        <?php
                                        echo $encashmentWaiting;
                                        ?>
                                    </span>
                                    <strong>Encashment Approvals</strong>
                                </div>

                                <?php
                                echo anchor('encashmentCntrl', 'View All <i class="fa fa-arrow-circle-right"></i>', array('class' => 'small-box-footer'));
                                ?>
                            </div>
                        </div>
                        <!-- ./col -->
                        <?php
                    }
                    ?>
                </div>

            <?php } ?>
            <?php
            $notif_count = count($notifications);
            ?>
            <div class="row">
                <div class="col-md-6">

                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">MY Pending Timesheets</h3>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="overlay" id="overlay1" style="display:none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <ul class="nav nav-stacked">
                                <li>
                                    <a href="#"> 
                                        My Pending Timesheets Last One Month
                                        <span class="pull-right badge bg-red">
                                            <?php echo $countTimesheetPending; ?>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <table class="table table-bordered table-hover">
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
                                    foreach ($pendingSheetData as $data) {
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
                                                echo "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Add</a>";
                                                ?>
                                            </td>

                                        </tr>
                                        <?php
                                        if ($serialNumber > 4) {
                                            break;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <a href="#" class="timesheetPendingDiv"> 
                                View All
                            </a>
                        </div>
                    </div>
                    <?php
                    if ($is_talent) {
                        ?>
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Leave Balance</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="form-horizontal">
                                <div class="box-body">
                                    <ul class="list-group">
                                        <?php
                                        foreach ($leaveKit as $lk) {
                                            if ($lk['type'] == 'EL') {
                                                ?>
                                                <a href="leaveCreditDetails/EL">
                                                    <li class="list-group-item">
                                                        <?php echo $lk['type']; ?>  <small class="label pull-right bg-blue"><?php
                                                        if ($halfDayMechanism == 1) {
                                                            echo $lk['leave_left'];
                                                        } else {
                                                            echo round($lk['leave_left']);
                                                        }
                                                        ?></small>
                                                    </li>
                                                </a>
                                                <?php
                                            } else if ($lk['type'] == 'Comp off') {
                                                ?>
                                                <a href="leaveCreditDetails/comp">
                                                    <li class="list-group-item">
                                                        <?php echo $lk['type']; ?>  <small class="label pull-right bg-blue"><?php echo round($lk['leave_left']); ?></small>
                                                    </li>
                                                </a>
                                                <?php
                                            } else {
                                                $showLeaveLeft = TRUE;
                                                $paternityEnabled = $this->defaultValues->getValue(32);
                                                $emergencyEnabled = $this->defaultValues->getValue(33);
                                                if ($emergencyEnabled == 0 && $lk['id'] == 3) {
                                                    $showLeaveLeft = false;
                                                }
                                                if ($paternityEnabled == 0 && ($lk['id'] == 4 || $lk['id'] == 5)) {
                                                    $showLeaveLeft = FALSE;
                                                }
                                                if ($showLeaveLeft) {
                                                    ?>
                                                    <li class="list-group-item">
                                                        <?php echo $lk['type']; ?>  <small class="label pull-right bg-blue"><?php echo round($lk['leave_left']); ?></small>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    <?php } ?>

                    <?php
//                    if ($is_talent) {
//                        
                    ?>
                    <!--                        <div class="box box-info">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Knowledge Portal</h3>
                                                    <span class="label label-warning pull-right">
                                                        //<?php // echo $kshopCount;   ?>
                                                    </span>
                                                </div>
                                                <div class="box-footer no-padding">
                                                    <ul class="nav nav-stacked">
                                                        <li>
                                                            <a style="font-size: 14px;">
                                                                //<?php
//                                            if (isset($lastKshopDescription))
//                                                echo $lastKshopHeading . ' : ' . $this->KnowledgeRepository->reduceLength($lastKshopDescription, 200);
//                                            
                    ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="box-footer text-center">
                                                    //<?php // echo anchor('knowledgeRepo', 'View All');   ?>
                                                </div>
                                            </div>
                                            <div class="box box-info">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">MY Knowledge Portal</h3>
                                                </div>
                                                <div class="box-footer no-padding">
                                                    <ul class="nav nav-stacked">
                                                        <li>
                                                            <a href="#"> 
                                                                My Contributions
                                                                <span class="pull-right badge bg-blue">
                                                                    //<?php // echo $myKnowledgeCount;   ?>
                                                                </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#"> 
                                                                My approved contributions
                                                                <span class="pull-right badge bg-blue">
                                                                    //<?php // echo $myApprovedKnowledgeCount;   ?>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="box-footer text-center">
                                                    //<?php // echo anchor('talentKnowledge', 'View All');   ?>
                                                </div>
                                            </div>
                    -->

                    <?php
//                    }
                    if (!empty($holidayDetails)) {
                        ?>
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upcoming Holidays</h3>
                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                                    </button>
                                    <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
                                    <?php
                                    $holiday_shown = false;
                                    foreach ($holidayDetails as $holiday) {
                                        ?>
                                        <li><a href="#"> <?php echo $holiday['name_of_holiday'] ?> <span class="pull-right badge bg-blue"><?php echo $holiday['dt_holiday']; ?></span></a></li>

                                        <?php
                                        $holiday_shown = true;
                                    }

                                    if (false === $holiday_shown) {
                                        ?>
                                        <li><a href="#">No data found!</a></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="box-footer text-center">
                                <?php echo anchor('holidayCalendar', 'View All'); ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>
                <div class="col-md-6">
                    <?php
                    if ($is_talent) {
                        ?>
                        <div class="box box-info" id="monthlyReport" style="cursor:pointer">
                            <div class="box-header with-border">
                                <h3 class="box-title">Attendance Status</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="form-horizontal">
                                <div class="box-body">
                                    <table class="table" style="background-color: #D9D6D6;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 60%">Date Consideration</td>
                                                <td><span class="badge bg-green"><?php echo date("d/m/Y", strtotime($attendance_statistics['dateFrom'])) . " - " . date("d/m/Y", strtotime($attendance_statistics['dateTo'])); ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Last date of attn. upload</td>
                                                <td><span class="badge bg-green"><?php echo date("d/m/Y", strtotime($attendance_statistics['lastDateOfUploaded'])); ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Total present days</td>
                                                <td><span class="badge bg-green"><?php echo $attendance_statistics['presentDay']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Average hour required</td>
                                                <td><span class="badge bg-yellow"> <?php echo $attendance_statistics['averageHourRequired']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Average hour earned</td>
                                                <td><span class="badge bg-light-blue"><?php echo $attendance_statistics['averageProductiveHours']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Total Hour Shortage</td>
                                                <td><span class="badge bg-red"><?php echo $attendance_statistics['hourShortage']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Total Absent </td>
                                                <td><span class="badge bg-yellow-gradient"><?php echo $attendance_statistics['totalAbsent']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Extra Hours </td>
                                                <td>
                                                    <span class="badge bg-green"><?php echo $attendance_statistics['extraHours']; ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <?php
                    }
                    if (!empty($newJoiners)) {
                        ?>
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">New Joiners</h3>
                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                                    </button>
                                    <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <ul class="users-list clearfix">
                                    <?php
                                    foreach ($newJoiners as $bd) {
                                        $prof_img = ("" == $bd['profile_image'] || null == $bd['profile_image']) ? base_url() . "assets/dist/img/default.png" : base_url() . 'uploads/' . $bd['profile_image'];
                                        ?>
                                        <li>
                                            <img alt="User Image" src="<?php echo$prof_img; ?>">
                                            <a href="javascript:void(0)" class="users-list-name"><?php echo $bd['first_name'] . ' ' . $bd['middle_name'] . ' ' . $bd['last_name'] ?></a>
                                            <span class="users-list-date"><?php echo $bd['t_dob']; ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <!-- /.users-list -->
                            </div>
                        </div>
                        <?php
                    }
                    if (!empty($anniversaryDetails)) {
                        ?>
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Celebrating Anniversary</h3>
                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                                    </button>
                                    <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <ul class="users-list clearfix">
                                    <?php
                                    foreach ($anniversaryDetails as $bd) {
                                        $prof_img = ("" == $bd['profile_image'] || null == $bd['profile_image']) ? base_url() . "assets/dist/img/default.png" : base_url() . 'uploads/' . $bd['profile_image'];
                                        ?>
                                        <li>
                                            <span style="font-size:16px;font-weight: bold;" class="years circle-sketch-highlight"><?php echo round(($bd['t_years'] / 365), 0) . " YEARS!"; ?></span>
                                            <img alt="User Image" src="<?php echo$prof_img; ?>">
                                            <a href="javascript:void(0)" class="users-list-name"><?php echo $bd['first_name'] . ' ' . $bd['middle_name'] . ' ' . $bd['last_name'] ?></a>
                                            <span class="users-list-date"><?php echo $bd['t_doj']; ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <!-- /.users-list -->
                            </div>
                        </div>
                        <?php
                    }
                    if (!empty($birthDayDetails)) {
                        ?>
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upcoming Birthdays</h3>
                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                                    </button>
                                    <button data-widget="remove" class="btn btn-box-tool" type="button"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <ul class="users-list clearfix">
                                    <?php
                                    foreach ($birthDayDetails as $bd) {
                                        $prof_img = ("" == $bd['profile_image'] || null == $bd['profile_image']) ? base_url() . "assets/dist/img/default.png" : base_url() . 'uploads/' . $bd['profile_image'];
                                        ?>
                                        <li>
                                            <img alt="User Image" src="<?php echo$prof_img; ?>">
                                            <a href="javascript:void(0)" class="users-list-name"><?php echo $bd['first_name'] . ' ' . $bd['middle_name'] . ' ' . $bd['last_name'] ?></a>
                                            <span class="users-list-date"><?php echo $bd['t_dob']; ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <!-- /.users-list -->
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php if (isset($management_message) && !empty($management_message)) { ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Messages</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <?php
                                echo $management_message;
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php
                    if (!empty($policies)) {
                        ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Company Policies</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <ul class="policies" >
                                    <?php
                                    foreach ($policies as $policy) {
                                        ?>
                                        <li class="item">
                                            <div class="product-img">
                                            </div>
                                            <div class="product-info">
                                                <?php echo anchor_popup('companyPolicyManagement/viewPolicy/' . $policy['id'], ' ' . $policy['policy_name'], $atts); ?>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($isManagement) {
                    form_open();
                    ?>
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Project Consolidated Report</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <?php
                                        echo form_dropdown('talent', $talents, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'talent',
                                            'name' => 'talent'
                                        ));
                                        ?>
                                    </div>
                                    <div class="col-sm-4">  <?php
                                        $startDayOfMonth = '01/' . date('m/Y');
                                        $fromDateText = array(
                                            'name' => 'fromdate',
                                            'id' => 'fromdate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => date('d/m/Y', strtotime($startDayOfMonth)),
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                    </div>
                                    <div class="col-sm-4">  <?php
                                        $fromDateText = array(
                                            'name' => 'todate',
                                            'id' => 'todate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => date('d/m/Y'),
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                    </div>
                                </div>
                                <br/>
                                <br/>
                                <div id="canvasShow">
                                </div>
                                <div id="totalTimeDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    form_close();
                }
                ?>
            </div>
    </div>
</section>
<!-- /.content -->
</div>
<div class="modal" id="modelshowbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="leaveCreditDiv"> 

                </div>


            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.container -->
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<span id="siteseal"><script async type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=omFQxIRIbMDAH99P0hpU0QCXSc5engjnpSWYdfIzCSZKYHURmMWeJ4amdhwT"></script></span>
<script>

    $(document).ready(function () {
        $("body").on("click", "#showLeaveCreditDetails", function (e) {
            $("#overlay1").show();
            talent = "<?php echo $talent; ?>";
            console.log(talent)
            $.ajax({
                url: base_url + "index.php/TalentLeaveCredit/leaveCreditHistory",
                type: "post", //send it through get method
                data: {
                    talent: talent,
                },
                success: function (response) {
                    $("#leaveCreditDiv").html(response);
                    $("#modelshowbody").modal('show');
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        });
        $("body").on("click", ".timesheetPendingDiv", function (e) {
            $("#overlay1").show();
            talent = "<?php echo $talent; ?>";
            console.log(talent)
            $.ajax({
                url: base_url + "index.php/home/getTimesheetPendingList",
                type: "post", //send it through get method
                data: {
                    talent: talent,
                },
                success: function (response) {
                    $("#leaveCreditDiv").html(response);
                    $("#modelshowbody").modal('show');
                    $('#table_responsive').DataTable();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        });
        pieChartDataLoad();
        function piechart(response) {
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas);
            valuess = 10;
            elements = [];
            $.each(response, function (key, value) {
                time = value.time;
                t1 = time.split(':');
                ortime = Number(t1[0]) + (Number(t1[1]) / 60);
                or = parseFloat(ortime).toFixed(2);
                elements.push({
                    value: or,
                    color: "#" + value.color,
                    highlight: "#" + value.color,
                    label: value.project
                });
            });
            var PieData = elements;
            var pieOptions = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke: true,
                //String - The colour of each segment stroke
                segmentStrokeColor: "#fff",
                //Number - The width of each segment stroke
                segmentStrokeWidth: 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps: 100,
                //String - Animation easing effect
                animationEasing: "easeOutBounce",
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate: true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
            };
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            pieChart.Doughnut(PieData, pieOptions);
        }
        function pieChartDataLoad() {
            var talent = [];
            talentId = $("#talent").val();
            if (talentId) {
                talent = talentId;
            }
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: "get",
                url: base_url + "index.php/TimePredictionCntrl/ProjectReportpiechart",
                dataType: 'json',
                data: {
                    talent: talent,
                    project: '',
                    task: '',
                    fromdate: fromdate,
                    todate: todate
                },
                success: function (json) {
                    $("#canvasShow").html('<canvas id="pieChart" style="height:250px"></canvas>');
                    //console.log(json);
                    piechart(json);
                    $.ajax({
                        type: "get",
                        url: base_url + "index.php/TimePredictionCntrl/TotalTime",
                        data: {
                            talent: talent,
                            project: '',
                            task: '',
                            fromdate: fromdate,
                            todate: todate
                        },
                        success: function (response) {
                            $("#totalTimeDiv").html(response);
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    });
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        }
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });
        $("body").on("click", "#monthlyReport", function (e) {
            window.location.href = "talentMonthlyReport";
        });
        $("body").on("changeDate", "#fromdate", function (e) {
            pieChartDataLoad();
        });
        $("body").on("changeDate", "#todate", function (e) {
            pieChartDataLoad();

        });
        $("body").on("change", "#talent", function (e) {
            pieChartDataLoad();
        });
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
    });
</script>
