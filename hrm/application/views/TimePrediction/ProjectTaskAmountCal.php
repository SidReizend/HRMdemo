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
        .notitle>td:nth-of-type(1):before { content: "Sl No"; } 
        .notitle>td:nth-of-type(2):before { content: "Talent"; } 
        .notitle>td:nth-of-type(3):before { content: "Approved Time"; } 
        .notitle>td:nth-of-type(4):before { content: "Pending Time"; } 
        .notitle>td:nth-of-type(5):before { content: "Total Time"; } 
        .notitle>td:nth-of-type(6):before { content: "Productivity"; } 
        .notitle>td:nth-of-type(7):before { content: "Total"; } 
        .detailed>td:nth-of-type(1):before { content: "Date"; } 
        .detailed>td:nth-of-type(2):before { content: "Time"; } 
        .detailed>td:nth-of-type(3):before { content: "Prod."; } 
        .detailed>td:nth-of-type(4):before { content: "Total"; } 
        .claim>td:nth-of-type(1):before { content: "Sl. No."; } 
        .claim>td:nth-of-type(2):before { content: "Talent"; } 
        .claim>td:nth-of-type(3):before { content: "Settlement"; } 
        .claim>td:nth-of-type(4):before { content: "Category"; } 
        .claim>td:nth-of-type(5):before { content: "Amount"; } 
        .claim>td:nth-of-type(6):before { content: "Date of Approval"; } 
        .claim>td:nth-of-type(7):before { content: "Approved By"; } 
        .claim>td:nth-of-type(8):before { content: "Created BY"; } 
    }
    tr.header
    {
        cursor:pointer;
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container-fluid">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Project Task Statistics</h3>
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
                                    <div class="col-sm-4">
                                        <?php
                                        echo form_dropdown('talent', $talents, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'talent',
                                            'multiple' => '',
                                            'name' => 'talent'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>                         
                                    <div class="col-sm-4">
                                        <?php
                                        echo form_dropdown('project', $projects, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'project',
                                            'multiple' => '',
                                            'name' => 'project'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <div class="col-sm-4" id="task_change">
                                        <?php
                                        echo form_dropdown('task', $tasks, '', array(
                                            'class' => 'form-control select2',
                                            'id' => 'task_id',
                                            'multiple' => '',
                                            'name' => 'task_id'
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
    var base_url = '<?php echo base_url(); ?>';
    function calc(sl, str) {
        var t1 = str;
        if (t1.includes(":") == false) {
            t1 = t1 + ":00";
        }
        var first = t1;
        var t2 = $("#a" + sl).val();
        t1 = t1.split(':');
        t2 = t2.split(':');
        mins = Number(t1[1]) + Number(t2[1]);
        minhrs = Math.floor(parseInt(mins / 60));
        hrs = Number(t1[0]) + Number(t2[0]) + minhrs;
        mins = mins % 60;
        t1 = hrs + ':' + mins
        $("#s" + sl).val(t1);
        $("#ma" + sl).val(first);
        var dec = hrs + (mins / 60);
        var pdt = $("#p" + sl).val();
        var tot = pdt * dec;
        tot = tot.toFixed(2)
        $("#t" + sl).val(tot);
        var sum = 0;
        $('.totaltime').each(function () {
            sum = sum + +$(this).val();
        });
        sum = sum.toFixed(2)
        $("#grand_total").val(sum);
    }
    $(document).ready(function () {
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });
        $("#task_id").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $("#project").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $("body").on("change", "#project", function (e) {
            e.preventDefault();
            var project = [];
            $('#project :selected').each(function (i, selected) {
                project[i] = $(selected).val();
            });
            base_url = '<?php echo base_url(); ?>';
            if (project != '') {
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/ChangeTask",
                    type: "get", //send it through get method
                    data: {
                        project: project
                    },
                    success: function (response) {
                        $("#task_change").html(response);
                        $("#task_id").select2({
                            dropdownAutoWidth: true,
                            width: '100%'
                        });

                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }

                });
            }
        });
        function change_table() {
            var fromdate = $("#fromdate").val();
            var talent = $("#talent").val();
            var todate = $("#todate").val();
            //fetching array of projects
            var project = [];
            $('#project :selected').each(function (i, selected) {
                project[i] = $(selected).val();
            });
            //fetching array of tasks
            var task = [];
            $('#task_id :selected').each(function (i, selected) {
                task[i] = $(selected).val();
            });

            if ((fromdate != "") && (todate != "")) {
                $("#overlay1").show();
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/ProjectTaskAmountCalAjax",
                    type: "get", //send it through get method
                    data: {
                        project: project,
                        fromdate: fromdate,
                        todate: todate,
                        talent: talent,
                        task: task
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
        }
        $("body").on("click", "#calculate", function (e) {
            change_table();

        });
        $("body").on("change", "#pending", function (e) {
            var pending = +$(this).val();
            var settled = +$("#settled").val();
            var total = pending + settled;
            total = total.toFixed(2)
            $("#totall").val(total);
        });

        $("body").on("click", ".showDetailsTr", function (e) {
            $("#overlay1").show();
            var element = $(this);
            element.find('.expand').text(function (_, value) {
                return value == '-' ? '+' : '-';
            });
            var text = element.find('.expand').text();
            if (text == '-') {
                element.find('table').css("display", "block");
            } else {
                element.find('table').css("display", "none");
                $("#overlay1").hide();
                return;
            }
            var project = element.data('project');
            var fromdate = element.data('from');
            var todate = element.data('to');
            var talent = element.data('talent');
            var task = element.data('task');
            var id = element.data('id');
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/billableNonBillableDetails",
                type: "post", //send it through get method
                data: {
                    project: project,
                    fromdate: fromdate,
                    todate: todate,
                    talent: talent,
                    task: task
                },
                success: function (response) {
                    $('#' + id + 'total').html(response);
                    $('#table_responsive').DataTable();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/billableNonBillableDetailsComplete",
                type: "post", //send it through get method
                data: {
                    project: project,
                    fromdate: fromdate,
                    todate: todate,
                    talent: talent,
                    task: task
                },
                success: function (response) {
                    $('#' + id + 'complete').html(response);
                    $('#table_responsive').DataTable();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });

        });
        $("body").on("click", ".exp-toggle", function (e) {
            $(this).find('span').text(function (_, value) {
                return value == '-' ? '+' : '-';
            });
            var text = $(this).find('span').text();
            if (text == '-') {
                $(this).find('table').css("display", "block");
            } else {
                $(this).find('table').css("display", "none");
            }

        });
    });
</script>