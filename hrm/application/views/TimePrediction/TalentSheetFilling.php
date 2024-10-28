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
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Project"; } 
        td:nth-of-type(3):before { content: "Task"; } 
        td:nth-of-type(4):before { content: "Percentage"; } 
        td:nth-of-type(4):before { content: "description"; } 
        td:nth-of-type(4):before { content: "Added By"; } 
        td:nth-of-type(4):before { content: "Actions"; } 
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
                            <h3 class="box-title">Talent Time Filling</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-4">  <?php
                                        echo form_dropdown('talent', $talents, '', array(
                                            'class' => 'form-control select2 change_tb',
                                            'required' => 'required',
                                            'id' => 'talent_id'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-4">  <?php
                                        $selectedMonth = date('m');
                                        $selectedYear = date('Y');
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions talentChoose change_tb',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-4">   <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions talentChoose change_tb',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
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
                <button class="btn btn-primary save_project" type="button">Add Talent</button>
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
        $("#talent_id").select2();
        $("#year").select2();
        $("#month").select2();
        showInputElements(); //Show InputElement Based on Date
        showTableData();//Show Table Data
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
            var talent = $("#talent_id").val();
            var year = $("#year").val();
            var month = $("#month").val();
            e.preventDefault();
            $("#overlay1").show();
            var Dat = $("#Date").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/SendApprovalTalent",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    year: year,
                    month: month
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Approved");
                        showInputElements();
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
        $("body").on("change", ".change_tb", function (e) {
            showInputElements();
            showTableData();
        });
        $("body").on("click", ".delete_data", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var id = $(this).data('id');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/DeleteDataTalent",
                type: "get", //send it through get method
                data: {
                    id: id
                },
                success: function (response) {
                    if (response == 1) {
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
                url: base_url + "index.php/TimePredictionCntrl/EditDataTalent",
                type: "get", //send it through get method
                data: {
                    id: id
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

        });
        $("body").on("click", "#AddRecord", function (e) {
            e.preventDefault();
            var talent = $("#talent_id").val();
            var year = $("#year").val();
            var month = $("#month").val();
            var task_id = $("#task_id").val();
            var project_id = $("#project_id").val();
            var percentage = $("#percentage").val();
            var description = $("#description").val();
            base_url = '<?php echo base_url(); ?>';

            if (project_id == "") {
                alertify.alert("Please Enter Project");
                return false;
            }
            if (task_id == "") {
                alertify.alert("Please Enter Task");
                return false;
            }
            if ((percentage == "") || (percentage > 100)) {
                alertify.alert("Please Enter Valid Percentage");
                return false;
            }
            if (description == "") {
                alertify.alert("Please Enter Description");
                return false;
            }
            if (year == "") {
                alertify.alert("Please Select Year");
                return false;
            }
            if (month == "") {
                alertify.alert("Please Select Month");
                return false;
            }
            if (talent == "") {
                alertify.alert("Please Select Talent");
                return false;
            }
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/SaveInputElementsTalent",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    year: year,
                    month: month,
                    task_id: task_id,
                    project_id: project_id,
                    percentage: percentage,
                    description: description
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Added");
                        showInputElements();
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
        $("body").on("click", "#EditRecord", function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var talent = $("#talent_id").val();
            var year = $("#year").val();
            var month = $("#month").val();
            var task_id = $("#task_id").val();
            var project_id = $("#project_id").val();
            var percentage = $("#percentage").val();
            var description = $("#description").val();
            base_url = '<?php echo base_url(); ?>';

            if (project_id == "") {
                alertify.alert("Please Enter Project");
                return false;
            }
            if (task_id == "") {
                alertify.alert("Please Enter Task");
                return false;
            }
            if ((percentage == "") || (percentage > 100)) {
                alertify.alert("Please Enter Valid Percentage");
                return false;
            }
            if (description == "") {
                alertify.alert("Please Enter Description");
                return false;
            }
            if (year == "") {
                alertify.alert("Please Select Year");
                return false;
            }
            if (month == "") {
                alertify.alert("Please Select Month");
                return false;
            }
            if (talent == "") {
                alertify.alert("Please Select Talent");
                return false;
            }
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/UpdateInputElementsTalent",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    year: year,
                    month: month,
                    task_id: task_id,
                    project_id: project_id,
                    percentage: percentage,
                    description: description,
                    id: id
                },
                success: function (response) {
                    if (response == 0) {
                        alertify.success("Successfully Updated");
                        showInputElements();
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
        function showInputElements() {
            var talent = $("#talent_id").val();
            var year = $("#year").val();
            var month = $("#month").val();
            if ((talent != "") && (year != "") && (month != "")) {
                $("#overlay1").show();
                base_url = '<?php echo base_url(); ?>';
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/showInputElementsTalents",
                    type: "get", //send it through get method
                    data: {
                        talent: talent,
                        year: year,
                        month: month
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
        }
        function showTableData() {
            var talent = $("#talent_id").val();
            var year = $("#year").val();
            var month = $("#month").val();
            if ((talent != "") && (year != "") && (month != "")) {
                $("#overlay1").show();
                base_url = '<?php echo base_url(); ?>';
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/showTableDataTalent",
                    type: "get", //send it through get method
                    data: {
                        talent: talent,
                        year: year,
                        month: month
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
        }
        function selectAndTimepicker() {
            $("#project_id").select2({
                placeholder: "Project",
                dropdownAutoWidth: true,
                width: '100%'
            });
        }



    });
</script>