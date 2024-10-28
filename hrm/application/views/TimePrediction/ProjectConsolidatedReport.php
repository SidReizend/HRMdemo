<?php
$datepicker = 1;
$datatables = 1;
$alertify = 1;
$select2 = 1;
$chartjs = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */
        td:nth-of-type(1):before {
            content: "Sl No.";
        }
        td:nth-of-type(2):before {
            content: "Project";
        }
        td:nth-of-type(3):before {
            content: "Talent";
        }
        td:nth-of-type(4):before {
            content: "Date";
        }
        td:nth-of-type(5):before {
            content: "Time";
        }
        td:nth-of-type(6):before {
            content: "Description";
        }
    }
    table, tr, td, th
    {
        border: 1px solid black;
        border-collapse:collapse;
    }
    tr.header
    {
        cursor:pointer;
    }
    tr.data{
        display:none;
    }
    .form-group-equal-height {
        display: flex;
    }

    .form-group-equal-height > div {
        flex: 1;
    }
    .align-down{
        position: absolute;
        bottom: 0;
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
                            <h3 class="box-title">Project Consolidated Report</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl/HomeDataApproval', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_label('All', 'all', array(
                                            'class' => ' control-label'
                                        ));
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
                                            'class' => 'form-control select2'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_label('Talents', 'talents', array(
                                            'class' => ' control-label'
                                        ));
                                        echo form_dropdown('talents', '', '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'talent',
                                            'name' => 'talent',
                                            'multiple' => '',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_label('Projects', 'project', array(
                                            'class' => ' control-label'
                                        ));
                                        echo form_dropdown('project[]', '', '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'project',
                                            'name' => 'project',
                                            'multiple' => '',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                    <!--                                </div>
                                                                    <div class="form-group">-->
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_label('Task', 'task', array(
                                            'class' => ' control-label'
                                        ));
                                        echo form_dropdown('task[]', '', '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'task',
                                            'multiple' => '',
                                            'name' => 'task'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Advance Invoice Fromdate', 'advanceinvoicefromdate', array(
                                            'class' => ' control-label'
                                        ));
                                        $fromDateText = array(
                                            'name' => 'advanceinvoicefromdate',
                                            'id' => 'advanceinvoicefromdate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('advanceinvoicefromdate'); ?> </span>
                                    </div>
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Advance Invoice Todate', 'advanceinvoicetodate', array(
                                            'class' => ' control-label'
                                        ));
                                        $fromDateText = array(
                                            'name' => 'advanceinvoicetodate',
                                            'id' => 'advanceinvoicetodate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('advanceinvoicetodate'); ?> </span>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Completed Invoice Fromdate', 'completedinvoicefromdate', array(
                                            'class' => ' control-label'
                                        ));
                                        $fromDateText = array(
                                            'name' => 'completedinvoicefromdate',
                                            'id' => 'completedinvoicefromdate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('completedinvoicefromdate'); ?> </span>
                                    </div>
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Completed Invoice Todate', 'completedinvoicetodate', array(
                                            'class' => ' control-label'
                                        ));
                                        $fromDateText = array(
                                            'name' => 'completedinvoicetodate',
                                            'id' => 'completedinvoicetodate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => '',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('completedinvoicetodate'); ?> </span>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Fromdate', 'fromdate', array(
                                            'class' => ' control-label'
                                        ));
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
                                    <div class="col-sm-6">  <?php
                                        echo form_label('Todate', 'todate', array(
                                            'class' => ' control-label'
                                        ));
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

                                </div>
                                <div class="form-group form-group-equal-height">
                                    <div class="col-sm-6">
                                        <?php
                                        echo form_label('Invoice Status', 'invoicestatus', array(
                                            'class' => ' control-label'
                                        ));
                                        echo form_dropdown('invoicestatus', $invoiceStatus, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'invoicestatus',
                                            'name' => 'invoicestatus',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('invoicestatus'); ?> </span>
                                    </div>
                                    <div class="col-sm-6"> 

                                        <div class="col-sm-offset-5 align-down">

                                            <button type="button" class="btn btn-primary" id="calculate">
                                                Calculate
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div> 
                            <div class="col-sm-6" id="canvasShow" >

                            </div>
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
            //fetching array of projects
            var talent = [];
            $('#talent :selected').each(function (i, selected) {
                talent[i] = $(selected).val();
            });
            var project = [];
            $('#project :selected').each(function (i, selected) {
                project[i] = $(selected).val();
            });
            //fetching array of tasks
            var task = [];
            $('#task :selected').each(function (i, selected) {
                task[i] = $(selected).val();
            });

            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            var track = $("#track").val();
            var advanceinvoicefromdate = $("#advanceinvoicefromdate").val();
            var advanceinvoicetodate = $("#advanceinvoicetodate").val();
            var completedinvoicefromdate = $("#completedinvoicefromdate").val();
            var completedinvoicetodate = $("#completedinvoicetodate").val();
            var invoicestatus = $("#invoicestatus").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ProjectReportAjax",
                type: "get", //send it through get method
                data: {
                    track: track,
                    talent: talent,
                    project: project,
                    task: task,
                    fromdate: fromdate,
                    todate: todate,
                    advanceinvoicefromdate: advanceinvoicefromdate,
                    advanceinvoicetodate: advanceinvoicetodate,
                    completedinvoicefromdate: completedinvoicefromdate,
                    completedinvoicetodate: completedinvoicetodate,
                    invoicestatus: invoicestatus
                },
                success: function (response) {
                    $("#show_data").html(response);
                    $.ajax({
                        type: "get",
                        url: base_url + "index.php/TimePredictionCntrl/ProjectReportpiechartCanvas",
                        data: {
                            talent: talent
                        },
                        success: function (response) {
                            $("#canvasShow").html(response);
                            $.ajax({
                                type: "get",
                                url: base_url + "index.php/TimePredictionCntrl/ProjectReportpiechart",
                                dataType: 'json',
                                data: {
                                    track: track,
                                    talent: talent,
                                    project: project,
                                    task: task,
                                    fromdate: fromdate,
                                    todate: todate,
                                    advanceinvoicefromdate: advanceinvoicefromdate,
                                    advanceinvoicetodate: advanceinvoicetodate,
                                    completedinvoicefromdate: completedinvoicefromdate,
                                    completedinvoicetodate: completedinvoicetodate,
                                    invoicestatus: invoicestatus
                                },
                                success: function (json) {
                                    //console.log(json);
                                    piechart(json);
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
                    // $('#table_responsive').DataTable();

                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        }
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

//            var PieData = [
//                {
//                    value: 700,
//                    color: "#f56954",
//                    highlight: "#f56954",
//                    label: "Chrome"
//                },
//                {
//                    value: 500,
//                    color: "#00a65a",
//                    highlight: "#00a65a",
//                    label: "IE"
//                },
//                {
//                    value: 400,
//                    color: "#f39c12",
//                    highlight: "#f39c12",
//                    label: "FireFox"
//                },
//                {
//                    value: 600,
//                    color: "#00c0ef",
//                    highlight: "#00c0ef",
//                    label: "Safari"
//                },
//                {
//                    value: 300,
//                    color: "#3c8dbc",
//                    highlight: "#3c8dbc",
//                    label: "Opera"
//                },
//                {
//                    value: 100,
//                    color: "#d2d6de",
//                    highlight: "#d2d6de",
//                    label: "Navigator"
//                }
//            ];
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

        $("body").on("click", ".excel", function (e) {
            var talent = $("#talent").val();
            var project = $("#project").val();
            var task = $("#task").val();
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            base_url = '<?php echo base_url(); ?>';
            window.location.href = base_url + "index.php/TimePredictionCntrl/ReportTimeExcel?talent=" + talent + "&project=" + project + "&task=" + task + "&fromdate=" + fromdate + "&todate=" + todate;
        });

        $("body").on("click", "#calculate", function (e) {
            change_table();

        });
        $("body").on("click", ".header", function (e) {
            $(this).find('span').text(function (_, value) {
                return value == '-' ? '+' : '-'
            });
            $(this).nextUntil('tr.header').slideToggle(100, function () {
            });
        });
        $("body").on("click", "#excel", function (e) {
            var track = $("#track").val();
            var fromdate = $("#fromdate").val();
            var toDate = $("#todate").val();
            var advanceinvoicefromdate = $("#advanceinvoicefromdate").val();
            var advanceinvoicetodate = $("#advanceinvoicetodate").val();
            var completedinvoicefromdate = $("#completedinvoicefromdate").val();
            var completedinvoicetodate = $("#completedinvoicetodate").val();
            var invoicestatus = $("#invoicestatus").val();
            //fetching array of projects
            var project = [];
            $('#project :selected').each(function (i, selected) {
                project[i] = $(selected).val();
            });
            //fetching array of tasks
            var task = [];
            $('#task :selected').each(function (i, selected) {
                task[i] = $(selected).val();
            });
            var talent = [];
            $('#talent :selected').each(function (i, selected) {
                talent[i] = $(selected).val();
            });
            base_url = '<?php echo base_url(); ?>';
            window.open(base_url + 'index.php/TimePredictionCntrl/excelDownload?fromDate=' + fromdate + '&toDate=' + toDate + '&talent=' + talent + "&project=" + project + "&task=" + task + "&track=" + track + "&advanceinvoicefromdate=" + advanceinvoicefromdate + "&advanceinvoicetodate=" + advanceinvoicetodate + "&completedinvoicefromdate=" + completedinvoicefromdate + "&completedinvoicetodate=" + completedinvoicetodate + "&invoicestatus=" + invoicestatus);
        });

        $("body").on("click", "#detailedExcel", function (e) {
            var track = $("#track").val();
            var fromdate = $("#fromdate").val();
            var toDate = $("#todate").val();
            var advanceinvoicefromdate = $("#advanceinvoicefromdate").val();
            var advanceinvoicetodate = $("#advanceinvoicetodate").val();
            var completedinvoicefromdate = $("#completedinvoicefromdate").val();
            var completedinvoicetodate = $("#completedinvoicetodate").val();
            var invoicestatus = $("#invoicestatus").val();
            //fetching array of projects
            var project = [];
            $('#project :selected').each(function (i, selected) {
                project[i] = $(selected).val();
            });
            //fetching array of tasks
            var task = [];
            $('#task :selected').each(function (i, selected) {
                task[i] = $(selected).val();
            });
            var talent = [];
            $('#talent :selected').each(function (i, selected) {
                talent[i] = $(selected).val();
            });
            base_url = '<?php echo base_url(); ?>';

            window.open(base_url + 'index.php/TimePredictionCntrl/excelDetailedDownload?fromDate=' + fromdate + '&toDate=' + toDate + '&talent=' + talent + "&project=" + project + "&task=" + task + "&track=" + track + "&advanceinvoicefromdate=" + advanceinvoicefromdate + "&advanceinvoicetodate=" + advanceinvoicetodate + "&completedinvoicefromdate=" + completedinvoicefromdate + "&completedinvoicetodate=" + completedinvoicetodate + "&invoicestatus=" + invoicestatus);
        });
    });

    project = [];
    selectedProject = '';
    $('#project').on("change", function () {
        project = [];
        selectedProject = '';
        $('#project :selected').each(function (i, selected) {
            project[i] = $(selected).val();
        });
        selectedProject = JSON.stringify(project);
        if (project.length === 0){
            $("#task").empty();
        }
    });
    $(document).ready(function () {
        var base_url = '<?php echo base_url(); ?>';
        $('#project').select2({
            ajax: {
                url: base_url + "index.php/TimePredictionCntrl/projectDropDown",
                type: "POST",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            allowClear: true
        });

        $('#task').select2({

            ajax: {
                url: base_url + "index.php/TimePredictionCntrl/taskDropDown",
                type: "POST",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        search: params.term,
                        project: selectedProject
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            allowClear: true
        });
        $('#talent').select2({
            ajax: {
                url: base_url + "index.php/TimePredictionCntrl/talentDropDown",
                type: "POST",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            allowClear: true
        });
    });

</script>