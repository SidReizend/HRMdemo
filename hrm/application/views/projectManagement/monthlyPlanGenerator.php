<?php
$select2 = 1;
$datepicker = 1;
$timepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('Project/monthlyPlanGenerator', array(
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
                            <h3 class="box-title">Monthly Plan Generator</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                </div>
                            </div>    
                            <div class="col-sm-12" id="showInputElements">
                                <div class="form-group" >
                                    <?php
                                    echo form_label('Year', 'year', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2">  <?php
                                        $toDateText = array(
                                            'name' => 'year',
                                            'id' => 'year',
                                            'autocomplete' => 'off',
                                            'placeholder' => 'YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => set_value('year'),
                                        );

                                        echo form_input($toDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('year'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>

                                    <?php
                                    echo form_label('Month', 'month', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2    ">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $month, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('month'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">  
                                        <?php
                                        $chartButton = array(
                                            'type' => 'button',
                                            'content' => 'Show Chart',
                                            'id' => 'show_chart',
                                            'class' => 'btn btn-small btn-info'
                                        );
                                        echo form_button($chartButton)
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <?php
                                    echo form_label('Project Name', 'name', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2" id="project_name">  <?php
                                        echo form_dropdown('name', $project, $selectedType, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'id' => 'name',
                                            'required' => 'required',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('name'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">
                                        <?php
                                        $groupButton = array(
                                            'type' => 'button',
                                            'content' => 'Add Group',
                                            'id' => 'add_group_popup',
                                            'class' => 'btn btn-small btn-info'
                                        );
                                        echo form_button($groupButton)
                                        ?>
                                    </div>
                                    <?php
                                    echo form_label(' Estimated Hours', 'hours', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2">  <?php
                                        $fromDateText = array(
                                            'name' => 'time',
                                            'id' => 'time',
                                            'placeholder' => '000:00',
                                            'class' => 'form-control',
                                            'value' => '000:00',
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('time'); ?> </span>
                                    </div>
                                    <!--                                    <div class="col-sm-2">  
                                                                        </div>-->
                                    <div class="col-sm-2">  
                                        <?php
                                        $submitButton = array(
                                            'type' => 'submit',
                                            'content' => 'Add Project',
                                            'id' => 'Add',
                                            'class' => 'btn btn-small btn-primary'
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table id="table_responsive" class="table table-bordered table-hover showTableData">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project </th>
                                            <th>Head</th>
                                            <th> Estimated Hours</th>
                                            <th> Actual Hours</th>
                                            <th> Consumed % </th>
                                            <th> Tends To deviate </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="showMonthlyPlan">
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-sm-12">
                                <table id="table_responsive" class="table table-bordered table-hover showUnAssignedProjects">


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
    <div class="modal-dialog" style="width:90% !important">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add Task</h4>
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
<div class="modal" id="modelshowchart">
    <div class="modal-dialog" style="width:90% !important">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Monthly Plan</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="show_chart_div">                                    
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
<script src="<?php echo base_url(); ?>assets/dist/js/highcharts.js"></script>
<script>
    $(document).ready(function () {
        var grouptaskArray = [];
        var totalgrouptime = 0;
        //time field enter hours format setting

        function handleTimeInput(id) {
            var time = $("#" + id).val();
            var acTest = new RegExp("^\\d{1,4}:\\d{2}$");
            var acTest1 = new RegExp("^[0-9:]+$");
            if (!acTest1.test(time)) {
                $('#' + id).val("000:00");
            } else if (!acTest.test(time)) {
                time = time.slice(0, 4) + ":00";
                $('#' + id).val(time);
            }
            var parts = time.split(":");
            var hours = parts[0];
            var minutes = parts[1];
            if (parseInt(minutes) > 59) {
                minutes = "59";
                time = hours + ":" + minutes;
                $('#' + id).val(time);
            }
        }

        $('#time').focusout(function () {
            handleTimeInput('time');  //hour format setting

        });
        $('body').on('focusout', '#popuptime', function () {
            handleTimeInput('popuptime'); //hour format setting
        });
        $('body').on('focusout', '#popup_grouptime', function () {
            handleTimeInput('popup_grouptime');  //hour format setting
        });
        $('body').on('focusout', '#popup_grouptasktime', function () {
            handleTimeInput('popup_grouptasktime');  //hour format setting
        });
        //status dropdown add select2 width
        function statusDropdownSelect() {
            $(".statustask").select2({
                dropdownAutoWidth: true,
                width: '100%'
            });
        }
        selectDropdownMonthlyPlan();
        projectNameDropdown();

        //select2  fuction in monthly plan page
        function selectDropdownMonthlyPlan() {
            $("#month").select2({
                placeholder: "Month",
                dropdownAutoWidth: false,
                width: '100%'
            });
        }
        function projectNameDropdown() {
            $("#name").select2({
                placeholder: "Project Name",
                dropdownAutoWidth: true,
                width: '100%'
            });
        }
        function projectNameDropdownPopup() {
            $("#project").select2({
                placeholder: "Project Name",
                dropdownAutoWidth: true,
                width: '100%'
            });
        }
        //select2 and timepicker fuction in mothly plan popup page
        function popupSelect2() {
            $("#task").select2({
                placeholder: "Task",
                dropdownAutoWidth: true,
                width: '100%'
            });

            $("#talents").select2({
                placeholder: "talents",
                dropdownAutoWidth: true,
                width: '100%'
            });

        }
        //year picker in monthly plan page
        $('#year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        }).on('change', function () {

        });
        //monthly plan submitting action 
        $("body").on("click", "#Add", function (e) {
            e.preventDefault();
            var year = $("#year").val();
            var month = $("#month").val();
            var project_name = $("#name").val();
            var hours = $("#time").val();
            base_url = '<?php echo base_url(); ?>';

            //validation in empty of input fields
            if (year == "") {
                alertify.alert("Please Select Year");
                return false;
            }
            if (month == "") {
                alertify.alert("Please Select Month");
                return false;
            }
            if (hours == "") {
                alertify.alert("Please Enter Time");
                return false;
            }
            if (project_name == "") {
                alertify.alert("Please Select Project");
                return false;
            }
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/Project/saveMonthlyPlan",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month,
                    project_name: project_name,
                    hours: hours
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Added");
                        showMonthlyPlan(); //show the monthly plan in current year and month
                        showUnAssignedProjects();
                        projectDropdownUnassignedProject();
                        $('#time').val('000:00'); //set timepicker default value after submission
                        $('#name').change(function () {
                        });
                        // Set the value of the select box to 1 and trigger the change event
                        $('#name').val('').trigger('change');
                        projectNameDropdown();
                    } else if (response == 0) {
                        alertify.error("Some Database Error Found");

                    } else if (response == 2) {
                        alertify.error("Selected Project Plan Already Added");

                    } else {
                        alertify.error(response);
                    }
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert(xhr.status + " " + xhr.statusText);
                }
            }).complete(function () {
                $("#overlay1").hide();

            });

        });
        $("body").on("change", "#month", function (e) {
            e.preventDefault();
            showMonthlyPlan();  //show monthly plan against selected month
            showUnAssignedProjects();
            projectDropdownUnassignedProject();
        });
        $("body").on("change", "#year", function (e) {
            e.preventDefault();
            showMonthlyPlan();  //show monthly plan against selected year
            showUnAssignedProjects();
            projectDropdownUnassignedProject();
        });

        //get the mothly plan against the corresponding year and month
        function showMonthlyPlan() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/getMonthlyPlan",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month
                },
                success: function (response) {
                    $("#showMonthlyPlan").html(response);
                    $(".select2").select2({
                        dropdownAutoWidth: false,
                        width: '100%'
                    });
                    projectNameDropdown();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
        }

        //delete the monthly plan
        $("body").on("click", ".delete_data", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var id = $(this).data('id');
            var del = $(this);
            //console.log(id);
            base_url = '<?php echo base_url(); ?>';
            alertify.confirm("Are you sure to delete  monthly plan", function (e) {
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/Project/deleteMonthlyPlan",
                        type: "get", //send it through get method
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response == 1) {
                                alertify.success("Successfully Delete");
                                showMonthlyPlan();  //show the monthly plan in current year and month
                                del.parents("tr").remove();
                                showUnAssignedProjects();
                                projectDropdownUnassignedProject();
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
                } else {
                    alertify.error("You've clicked Cancel");
                    $("#overlay1").hide();
                }
            });

        });
        // load the popup model

        $("body").on("click", ".add_task", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var id = $(this).data('id');
            var projectId = $(this).data('project');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/showTashAddModel",
                type: "get", //send it through get method
                data: {
                    id: id,
                    projectId: projectId
                },
                success: function (response) {
                    $("#show_details").html(response);
                    $("#modelshowbody").modal('show');
                    popupSelect2(); // popup page select2 and timepicker work

                    showTaskPlan();
                    showUnassignedTask(id, projectId);
                    statusDropdownSelect();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        });

        //add task against the monthly plan in popup page
        $("#show_details").on("click", "#Add_Task", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var task = $("#task").val();
            var talent = $("#talents").val();
            var time = $("#popuptime").val();
            var monthlyplan = $(this).data('id');
            var projectId = $(this).data('project');
            //validating the empty field
            if (task == "") {
                alertify.alert("Please Select Task");
                $("#overlay1").hide();
                return false;
            }
            if (talent == "") {
                alertify.alert("Please Select Talent");
                $("#overlay1").hide();
                return false;
            }
            if (time == "") {
                alertify.alert("Please Enter Time");
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/addTaskMonthlyPlan",
                type: "get", //send it through get method
                data: {
                    task: task,
                    talent: talent,
                    time: time,
                    monthlyplan: monthlyplan
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully inserted");
                        showTaskPlan();   //show  all task plan against the monthly plan
                        $("#taskForm")[0].reset();
                        popupSelect2(); //popup select2 work
                        showUnassignedTask(monthlyplan, projectId);
                        $("#popuptime").val('00:00'); //set timepicker default value
                    } else if (response == 2) {
                        alertify.success("Successfully updated");
                        showTaskPlan();  //show  all task plan against the monthly plan
                        $("#taskForm")[0].reset();
                        popupSelect2();  //popup select2 work
                        $("#popuptime").val('00:00'); //set timepicker default value
                    } else if (response == 3) {
                        alertify.error("Task Plan Already Added");
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
        //show  all task plan against the monthly plan
        function showTaskPlan() {
            var monthlyplan = $("#Add_Task").data('id');
            var projectId = $("#Add_Task").data('project');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/getMonthlyTaskPlan",
                type: "get", //send it through get method
                data: {
                    monthlyplan: monthlyplan,
                    projectId: projectId
                },
                success: function (response) {
                    $("#showTaskPlan").html(response);
                    statusDropdownSelect();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });

        }
        //show  unassigned task against the project
        function showUnassignedTask(monthlyplan, projectId) {
            $("#overlay").show();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/showUnassignedTask",
                type: "get", //send it through get method
                data: {
                    id: monthlyplan,
                    projectId: projectId
                },
                success: function (response) {
                    $(".showAllTalents").html(response);
                    statusDropdownSelect();
                    $("#overlay").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });

        }
        //delete the task plan
        $("body").on("click", ".delete_task_plan", function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var projectId = $(this).data('project');
            var monthlyplan = $(this).data('monthlyplan');
            console.log(projectId);
            base_url = '<?php echo base_url(); ?>';
            alertify.confirm("Are you sure to delete  monthly Task Plan", function (e) {
                if (e) {
                    $("#overlay").show();
                    $.ajax({
                        url: base_url + "index.php/Project/deleteMonthlyTaskPlan",
                        type: "get", //send it through get method
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response == 1) {
                                alertify.success("Successfully Delete");
                                showTaskPlan(); //show  all task plan against the monthly plan after deleting
                                showUnassignedTask(monthlyplan, projectId);
                            } else {
                                alertify.error("Some Database Error Found");
                            }
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    }).complete(function () {
                        $("#overlay").hide();
                    });
                } else {
                    alertify.error("You've clicked Cancel");
                    $("#overlay").hide();
                }
            });

        });
        //changing the dropdown of the task status
        $("body").on("change", "#statustask", function (e) {
            var status = $(this).val();
            var taskid = $(this).data('id');

            statusUpdate(taskid, status);
            showTaskPlan();
        });
        //Task status update changing the status dropdown value
        function statusUpdate(taskid, status) {
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/taskStatusUpdated",
                type: "get", //send it through get method
                data: {
                    status: status,
                    taskid: taskid
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Status Updated");
                    } else {
                        alertify.error("Some Database Error Found");
                    }

                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
        }
        //show unassigned project for corresponding year and month change
        function showUnAssignedProjects() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/showUnAssignedProjects",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month
                },
                success: function (response) {
                    if (response == 1) {
                        $(".showUnAssignedProjects").html('');
                    } else {

                        $(".showUnAssignedProjects").html(response);
                    }
                    $(".select2").select2({
                        dropdownAutoWidth: false,
                        width: '100%'
                    });
                    projectNameDropdown();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
            $("#overlay1").hide();
        }
        //get the project dropdown unselected project corresponding year and month
        function projectDropdownUnassignedProject() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/projectDropdownUnassignedProject",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month
                },
                success: function (response) {
                    $("#project_name").html(response);
                    $(".select2").select2({
                        dropdownAutoWidth: false,
                        width: '100%'
                    });
                    projectNameDropdown();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
        }




        // On click  show chart to display chart
        function chartDisplay() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/showChart",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month

                },
                success: function (response) {
                    $("#show_chart_div").html(response);
                    $("#modelshowchart").modal('show');
                }
            });
            $("#overlay1").hide();
        }
        // On click  show chart to display chart

        $("body").on("click", "#show_chart", function (e) {
            e.preventDefault();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                alertify.error("Select Year And Month");
            } else {
                chartDisplay();
            }

        });
        // On click  add group button add display popup module 

        $("body").on("click", "#add_group_popup", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                alertify.error("Select Year And Month");
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/monthlyPlanGroupGenerator",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month

                },
                success: function (response) {
                    $("#show_chart_div").html(response);
                    $("#modelshowchart").modal('show');
                    projectNameDropdownPopup();
                    projectGroupDropdownUnassignedProject();
                    grouptaskArray = [];
                }
            });
            $("#overlay1").hide();
        });
        //add group plan in the monthly plan in popup page
        $("body").on("click", "#add_group", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var project = $("#project").val();
            var group = $("#group_name").val();
            var time = $("#popup_grouptime").val();
            var year = $(this).data('year');
            var month = $(this).data('month');
            var groupproject = JSON.stringify(grouptaskArray);
            var grouptime = '';

            // check the group time total project estimation time group project adding field

            if (hoursToSeconds(time) < window.totalgrouptime) {
                grouptime = secondsToTime(window.totalgrouptime)
            } else {
                grouptime = time;
            }
            if (grouptaskArray.length == 0) {
                alertify.alert("Please Add Group Plan");
                $("#overlay1").hide();
                return false;
            }

            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/saveGroupMonthlyPlan",
                type: "get", //send it through get method
                data: {
                    groupproject: groupproject,
                    time: grouptime,
                    year: year,
                    month: month
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Successfully Added");

                    } else if (response == 0) {
                        alertify.error("Some Database Error Found");

                    } else if (response == 2) {
                        alertify.error("Selected Project Plan Already Added");

                    } else {
                        alertify.error(response);
                    }
                    grouptaskArray = [];
                    projectGroupDropdownUnassignedProject();
                    projectDropdownUnassignedProject();
                    $("#popup_grouptime").val('000:00');
                    $("#group_name").val('');
                    $('#sowgrouptaskdetails').html('');
                    window.totalgrouptime = 0;
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                showMonthlyPlan();
                showUnAssignedProjects();
                $("#overlay1").hide();
            });
        });
        //get the project  grop dropdown  in popup page unselected project corresponding year and month
        function projectGroupDropdownUnassignedProject() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/Project/projectGroupDropdownUnassignedProject",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month
                },
                success: function (response) {
                    $("#show_unaasigend_projects").html(response);
                    projectNameDropdownPopup();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
        }


        // add muliple project plan in group plan popup

        $("body").on("click", "#add_grouptask", function (e) {
            e.preventDefault();
            var project = $("#project").val();
            var project_name = $("#project option:selected").text();
            var group = $("#group_name").val();
            var task_time = $("#popup_grouptasktime").val();
            if (group == "") {
                alertify.alert("Please Enter Group Name");
                $("#overlay1").hide();
                return false;
            }
            if (project == "") {
                alertify.alert("Please Select Project");
                $("#overlay1").hide();
                return false;
            }
            if (grouptaskArray.length > 0) {
                if (grouptaskArray[0]['group'] != group) {
                    alertify.alert("Please Enter  Same Group Name");
                    $("#overlay1").hide();
                    return false;
                }
            }
            if (grouptaskArray.length > 0) {
                for (var i = 0; i < grouptaskArray.length; i++) {
                    if (grouptaskArray[i]['project'] === project) {
                        alertify.alert("Selected Project Already Added");
                        $("#overlay1").hide();
                        return false;
                    }
                }
            }
            if (task_time == "") {
                alertify.alert("Please Enter Time");
                $("#overlay1").hide();
                return false;
            }
            var grouptask = [];
            var grouptask = {
                project: project,
                project_name: project_name,
                group: group,
                task_time: task_time
            };


            grouptaskArray.push(grouptask);
            $("#project").val('');
            projectNameDropdownPopup();
            tableView();
        });
        function tableView() {
            var response = "";
            var sum = 0;
            for (var i = 0; i < grouptaskArray.length; i++) {
                response += "<tr><td>" + (i + 1) + "</td><td>" + grouptaskArray[i]['project_name'] + "</td><td>" + grouptaskArray[i]['group'] + "</td><td>" + grouptaskArray[i]['task_time'] + "</td><td> <button class='btn btn-default btn-sm' id = 'deleteGroupProject' data-id = '" + [i] + " '><span class='glyphicon glyphicon-remove'></span></button></td></tr>";
                sum += hoursToSeconds(grouptaskArray[i]['task_time']);
            }
            response += "<tr><td></td><td></td><td></td><td> <b> Total Time:  " + secondsToTime(sum) + " </b></td><td></td></tr>";
            window.totalgrouptime = sum;
            $('#sowgrouptaskdetails').html(response);
            $("#project").val("");
            $("#popup_grouptasktime").val('000:00');


        }

        //hours to second calculation

        function hoursToSeconds(time) {
            var parts = time.split(":");
            var hours = parts[0];
            var minutes = parts[1];
            return hours * 3600 + minutes * 60;
        }

        //second to hour calculation

        function secondsToTime(secs) {
            var h = Math.floor(secs / (60 * 60));

            var divisor_for_minutes = secs % (60 * 60);
            var m = Math.floor(divisor_for_minutes / 60);

            var divisor_for_seconds = divisor_for_minutes % 60;
            var s = Math.ceil(divisor_for_seconds);

            return `${h ? `${h}:` : "000:"}${m ? `${m}:${s}` : "00"}`
        }
        $("body").on("click", "#deleteGroupProject", function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            grouptaskArray.pop(id);
            $(this).parents("tr").remove();
            tableView();
        });

    });
</script>


