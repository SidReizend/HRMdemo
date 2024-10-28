<?php
$datepicker = 1;
$datatables = 1;
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Project"; } 
        td:nth-of-type(3):before { content: "Talent"; } 
        td:nth-of-type(4):before { content: "Task"; } 
        td:nth-of-type(5):before { content: "Date"; } 
        td:nth-of-type(6):before { content: "Time"; } 
        td:nth-of-type(7):before { content: "Description"; } 
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
                            <h3 class="box-title">Report</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl/HomeDataApproval', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <?php
                                        $track_dropdown = array(
                                            '' => 'ALL',
                                            'Accounts and Operations' => 'Accounts and Operations',
                                            'Web' => 'Web',
                                            'Mobile' => 'Mobile',
                                            'Test and Implementation' => 'Test and Implementation',
                                            'SCA' => 'SCA',
                                        );
                                        echo form_dropdown('track', $track_dropdown, '', array(
                                            'id' => 'track',
                                            'class' => 'form-control'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php
                                        echo form_dropdown('talent', $talents, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'talent',
                                            'name' => 'talent'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php
                                        echo form_dropdown('project', $projects, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'project',
                                            'name' => 'project'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php
                                        echo form_dropdown('task', $tasks, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'task',
                                            'name' => 'task'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">  <?php
                                        $fromDateText = array(
                                            'name' => 'fromdate',
                                            'id' => 'fromdate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-4">  <?php
                                        $fromDateText = array(
                                            'name' => 'todate',
                                            'id' => 'todate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">

                                        <button type="button" class="btn btn-primary" id="calculate">
                                            Calculate
                                        </button>
                                    </div>

                                </div>

                            </div> 
                            <br/>
                            <br/>
                            <br/>
                            <div class="col-sm-12" id="show_data">

                            </div>
                            <center><button class="btn btn-primary excel" type="button">Excel</button></center>
                        </div>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
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
<script>
    $(document).ready(function () {
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });
        function change_table() {
            $("#overlay1").show();
            var talent = $("#talent").val();
            var project = $("#project").val();
            var task = $("#task").val();
            var track = $("#track").val();
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ReportAjax",
                type: "get", //send it through get method
                data: {
                    track: track,
                    talent: talent,
                    project: project,
                    task: task,
                    fromdate: fromdate,
                    todate: todate
                },
                success: function (response) {
                    $("#show_data").html(response);
                    $('#table_responsive').DataTable();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        }


        $("body").on("click", ".excel", function (e) {
            var track = $("#track").val();
            var talent = $("#talent").val();
            var project = $("#project").val();
            var task = $("#task").val();
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            base_url = '<?php echo base_url(); ?>';
            window.location.href = base_url + "index.php/TimePredictionCntrl/ReportTimeExcel?talent=" + talent + "&project=" + project + "&task=" + task + "&fromdate=" + fromdate + "&todate=" + todate+ "&track=" + track;
        });

        $("body").on("click", "#calculate", function (e) {
            change_table();

        });

    });
</script>