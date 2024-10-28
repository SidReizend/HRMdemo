<?php
$select2 = 1;
$alertify = 1;
$datepicker = 1;
$timepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */
        .notitle>td:nth-of-type(1):before {
            content: "Sl No";
        }
        .notitle>td:nth-of-type(2):before {
            content: "Project";
        }
        .notitle>td:nth-of-type(3):before {
            content: "Task";
        }
        .notitle>td:nth-of-type(4):before {
            content: "Time";
        }
        .notitle>td:nth-of-type(5):before {
            content: "description";
        }
        .notitle>td:nth-of-type(6):before {
            content: "Action";
        }
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('TaskManagementCntrl/AddTask', array(
                'class' => 'form-horizontal',
                'id' => 'eventForm'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Time Management</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <?php
                                    if (!isset($date)) {
                                        $date = date('d/m/Y');
                                    }
                                    echo form_label('Date', 'Date', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-10">  <?php
                                        $fromDateText = array(
                                            'name' => 'Date',
                                            'id' => 'Date',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => $date,
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>

                                </div>
                            </div>    
                            <div class="col-sm-12" id="showInputElements">
                            </div>
                            <div class="col-sm-12">
                                <table id="table_responsive" class="table table-bordered table-hover showTableData">

                                </table>
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
<div class="modal" id="modelshowbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="show_details">                                    
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

<?php
echo form_close();
?>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        showInputElements(); //Show InputElement Based on Date
        showTableData();//Show Table Data
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });
        $("body").on("change", "#project_id", function (e) {
            e.preventDefault();
            var project_id = $(this).val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ChangeTaskSingle",
                type: "get", //send it through get method
                data: {
                    project_id: project_id
                },
                success: function (response) {
                    $("#task_change").html(response);
                    $("#task_id").select2({
                        placeholder: "Task",
                        dropdownAutoWidth: true,
                        width: '100%'
                    });

                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            })
        });
        $("body").on("click", ".send_approval", function (e) {
            e.preventDefault();
            var date = $("#Date").val();
            var food_allowance = 0;
            var food_andtravel_allowance = 0;
            if ($('input[name="allowance"]:checked').val() == 1) {
                 food_allowance = 1;
                 food_andtravel_allowance = 0;
            } else if($('input[name="allowance"]:checked').val() == 2) {
                 food_allowance = 0;
                 food_andtravel_allowance = 1;

            }
            alertify.confirm("Are you sure", function (e) {
                if (e) {
                    sendForApproval(date, food_allowance, food_andtravel_allowance);

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });


        });

        function sendForApproval(date, food_allowance, food_andtravel_allowance) {
            $("#overlay1").show();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/SendApproval",
                type: "get", //send it through get method
                data: {
                    Dat: date,
                    food_allowance: food_allowance,
                    food_andtravel_allowance: food_andtravel_allowance
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Send For Approval");
                        showInputElements();
                        showTableData();
                    } else if (response == 0) {
                        alertify.error("Some Database Error Found");
                    } else {
                        alertify.error(response);
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            }).complete(function () {
                $("#overlay1").hide();
            });
        }

        $("body").on("click", ".logView", function (e) {
            e.preventDefault();
            var log = $(this).data('log');
            $("#show_details").html(log);
            $("#modelshowbody").modal('show');
        });
        $("body").on("changeDate", "#Date", function (e) {
            var Dat = $("#Date").val();
            if ((Dat == "") || (Dat == "0000-00-00")) {
                alertify.alert("Please Enter Valid Date");
                return false;
            }
            showInputElements();
            showTableData();
        });
        $("body").on("click", ".delete_data", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var id = $(this).data('id');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/DeleteData",
                type: "get", //send it through get method
                data: {
                    id: id
                },
                success: function (response) {
                    if (response.substring(0, 5) === 'error') {
                        alertify.error(response.substring(6));
                    } else if (response == 1) {
                        alertify.success("Successfully Delete");
                        showTableData();
                    } else {
                        alertify.error("Some Database Error Found");
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });

        });
        $("body").on("click", ".edit_data", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var id = $(this).data('id');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/EditData",
                type: "get", //send it through get method
                data: {
                    id: id
                },
                success: function (response) {
                    if (response.substring(0, 5) === 'error') {
                        alertify.error(response.substring(6));
                    } else {
                        $("#showInputElements").html(response);
                        selectAndTimepicker();//Select2 and Timepicker work
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });

        });
        $("body").on("click", "#AddRecord", function (e) {
            e.preventDefault();
            var Dat = $("#Date").val();
            var task_id = $("#task_id").val();
            var project_id = $("#project_id").val();
            var timepicker = $(".timepicker").val();
            var description = $("#description").val();
            var is_outside_time = $('#isOutsideTime').prop('checked') == true ? 1 : 0;
            base_url = '<?php echo base_url(); ?>';

            if (project_id == "") {
                alertify.alert("Please Enter Project");
                return false;
            }
            if (task_id == "") {
                alertify.alert("Please Enter Task");
                return false;
            }
            if (timepicker == "") {
                alertify.alert("Please Enter Time");
                return false;
            }
            if (description == "") {
                alertify.alert("Please Enter Description");
                return false;
            }
            if ((Dat == "") || (Dat == "0000-00-00")) {
                alertify.alert("Please Enter Valid Date");
                return false;
            }
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/SaveInputElements",
                type: "get", //send it through get method
                data: {
                    Dat: Dat,
                    task_id: task_id,
                    project_id: project_id,
                    timepicker: timepicker,
                    description: description,
                    is_outside_time: is_outside_time
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Added");
                        showInputElements();
                        showTableData();
                    } else if (response == 0) {
                        alertify.error("Some Database Error Found");
                    } else {
                        alertify.error(response);
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert(xhr.status + " " + xhr.statusText);
                }
            }).complete(function () {
                $("#overlay1").hide();
            });

        });
        $("body").on("click", "#EditRecord", function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var Dat = $("#Date").val();
            var task_id = $("#task_id").val();
            var project_id = $("#project_id").val();
            var timepicker = $(".timepicker").val();
            var description = $("#description").val();
            var is_outside_time = $('#isOutsideTime').prop('checked') == true ? 1 : 0;
            base_url = '<?php echo base_url(); ?>';

            if (project_id == "") {
                alertify.alert("Please Enter Project");
                return false;
            }
            if (task_id == "") {
                alertify.alert("Please Enter Task");
                return false;
            }
            if (timepicker == "") {
                alertify.alert("Please Enter Time");
                return false;
            }
            if (description == "") {
                alertify.alert("Please Enter Description");
                return false;
            }
            if ((Dat == "") || (Dat == "0000-00-00")) {
                alertify.alert("Please Enter Valid Date");
                return false;
            }
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/UpdateInputElements",
                type: "get", //send it through get method
                data: {
                    Dat: Dat,
                    task_id: task_id,
                    project_id: project_id,
                    timepicker: timepicker,
                    description: description,
                    id: id,
                    is_outside_time: is_outside_time
                },
                success: function (response) {
                    if (response == 0) {
                        alertify.success("Successfully Updated");
                        showInputElements();
                        showTableData();
                    } else if (response == 2) {
                        alertify.error("Timesheet not allowed to update for a date after deployment date");
                    } else {
                        alertify.error("Some Database Error Found");
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });

        });
        function showInputElements() {
            var Dat = $("#Date").val();
            $("#overlay1").show();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/showInputElements",
                type: "get", //send it through get method
                data: {
                    Dat: Dat
                },
                success: function (response) {
                    $("#showInputElements").html(response);
                    selectAndTimepicker();//Select2 and Timepicker work
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        }
        function showTableData() {
            $("#overlay1").show();
            var Dat = $("#Date").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/showTableData",
                type: "get", //send it through get method
                data: {
                    Dat: Dat
                },
                success: function (response) {
                    $(".showTableData").html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        }
        function selectAndTimepicker() {
            $("#project_id").select2({
                placeholder: "Project",
                dropdownAutoWidth: true,
                width: '100%'
            });
            $(".timepicker").timepicker({
                showInputs: false,
                showMeridian: false,
                defaultTime: '00:00'
            });
        }



    });
</script>
