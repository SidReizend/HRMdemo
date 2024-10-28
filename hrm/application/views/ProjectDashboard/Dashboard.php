<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
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
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Project Type"; } 
        td:nth-of-type(2):before { content: "Targeted Value"; } 
        td:nth-of-type(3):before { content: "Date of target accomplishment"; } 
        td:nth-of-type(4):before { content: "Valid Till"; } 
        td:nth-of-type(6):before { content: "Actions"; } 
    }
    box {
        background-color: lightgrey;
        width: 300px;
        border: 15px;
        padding: 15px;
        margin: 15px;
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
                            <h3 class="box-title">Project Dashboard</h3>
                        </div>
                        <div class="col-sm-12 ">

                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'showReport',
                            ));
                            ?>
                            <div class="box-body" >
                                <div class="form-group">
                                    <div class="col-sm-2 pull-right">
                                        <?php
                                        echo form_dropdown('schedule', $schedules, $selectdSchedule, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'schedule',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('schedule'); ?> </span>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <canvas id="bar-chart-organization" width="400" height="200"></canvas>

                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style=" padding-top: 70px;">
                            <box style="float: right;">
                                <?php
                                echo "Total Target" . ' = ' . number_format($totalTarget);
                                ?>
                                <br/>

                                <?php
                                echo "Total Approved" . ' = ' . number_format($totalApproved);
                                ?>
                                <br/>

                                <?php
                                echo "Percentage of Accomplishment" . ' = ' . $totalPercentage . '%';
                                ?>

                            </box>

                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style=" padding-top: 50px;">

                            <?php
                            foreach ($projectTypes as $project) {
                                ?>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">

                                    <canvas id="pie_chart_project_types<?php echo $project['id']; ?>" width="75" height="40"></canvas>
                                </div>
                                <?php
                            }
                            ?>
                        </div>


                        <div class="box-body">
                            <br/>
                        </div>
                        <div class="box-body">

                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Project Type</th>
                                        <th>Targeted Value</th>
                                        <th>Proposed Value</th>
                                        <th>Approved Value</th>
                                        <th>Number of days left</th>
                                        <th>Short of</th>
                                        <th>% Accomplishment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($projectTypes as $project) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $project['project_type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['targeted_value']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $value = $project['totalProposed'] - $project['totalApproved'];
                                                if ($value > 0) {
                                                    echo number_format($value);
                                                    echo anchor_popup('ProjectDashboard/viewProposed/' . $project['id'], ' ' . ' +', $atts);
                                                } else {
                                                    echo 0;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['totalApproved']);
                                                echo anchor_popup('ProjectDashboard/viewApproved/' . $project['id'], ' ' . ' +', $atts);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $now = date("d-m-Y"); // current date
                                                $now1 = strtotime($now); // current date
                                                $date1 = date_create($now);
                                                $valid_date = strtotime($project['valid_date']);
                                                $datediff = 0;
                                                $date2 = date_create($project['valid_date']);
                                                if ($valid_date > $now1) {
                                                    $datediff = date_diff($date1, $date2);
                                                    echo $datediff->format("%a days");
                                                } else {
                                                    echo $datediff;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['targeted_value'] - $project['totalApproved']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format((float) ($project['totalApproved'] / $project['targeted_value'] ) * 100, 2, '.', '');
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script src="<?php echo base_url(); ?>assets/plugins/chartjs/Chart-2.1.4.js"></script>

<script>
    $(document).ready(function () {
        var schedule = $("#schedule").val();
        organizationChart(schedule);
        projectTypeChart(schedule);
        $("body").on("change", ".selectOptions", function (e) {
            var schdle = $("#schedule").val();
            base_url = '<?php echo base_url(); ?>';
            //organizationChart(schdle);
            //projectTypeChart(schdle);
            window.location.href = base_url + "index.php/ProjectDashboard/index?schedule=" + schdle;

        });
        $(".select2").select2();
    });

    function projectTypeChart(schedule) {
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "get",
            url: base_url + "index.php/ProjectDashboard/ProjectTypePiechart?schedule=" + schedule,
            dataType: 'json',
            data: {},
            success: function (json) {

                $.each(json, function (index, value) {
                    $.each(value, function (ind, val) {
                        var approvedVl = (val.totalApproved / val.targeted_value) * 100;
                        //var approvedVal = Math.round(approvedVl);
                        var approvedVal = (approvedVl).toFixed(2);
                        var pending = (100 - approvedVal).toFixed(2);
                        var remaing = "pending";
                        var percentageArr = [approvedVal, pending];
                        var typeArr = [val.project_type, remaing];
                        var id = val.id;
                        displayChart(typeArr, percentageArr, id);

                    });
                });
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }

    function displayChart(type, value, id) {
        new Chart(document.getElementById("pie_chart_project_types" + id), {
            type: 'pie',
            data: {
                labels: type,
                datasets: [{
                        label: "Population (millions)",
                        backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
                        data: value
                    }]
            },
            options: {
                title: {
                    display: true,
                    text: type[0] + ' ' + 'Status in %'
                }
            }
        });
    }

    function organizationChart(schedule) {
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "get",
            url: base_url + "index.php/ProjectDashboard/OrganizationReportPiechart?schedule=" + schedule,
            dataType: 'json',
            data: {},
            success: function (json) {
                new Chart(document.getElementById("bar-chart-organization"), {
                    type: 'pie',
                    data: {
                        labels: ['Achieved', 'Pending'],
                        datasets: [
                            {
                                label: "",
                                backgroundColor: ["#3cba9f", "#e8c3b9", "#c45850", "#3e95cd", "#8e5ea2"],
                                data: json.remainingPercentage
                            }
                        ]
                    },
                    options: {
                        legend: {display: false},
                        title: {
                            display: true,
                            text: 'Organization Based Project Status in %'
                        }
                    }
                });
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }
</script>

