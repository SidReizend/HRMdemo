<?php
$datatables = 1;
$form_validator2 = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('', array(
                'class' => 'form-horizontal',
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Timesheet Remapping</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('From Date', 'fromdate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('fromDate'),
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('from_date'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('To Date', 'todate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('toDate'),
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('toDate'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Talent', 'talent', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                        'class' => 'form-control select2',
                                        'id' => 'talent',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Previous project', 'previous_project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('previous_project', $projectDropdown, $selectedProject, array(
                                        'class' => 'form-control select2 ',
                                        'id' => 'previous_project',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('previous_project'); ?> </span>
                                </div>
                            </div>
                            <!-- Select task -->
                            <div class="form-group">
                                <?php
                                echo form_label('Preious Task', 'task_code', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10" id="prevTaskDiv">
                                    <?php
                                    echo form_dropdown('task_code', $taskDropdown, $selectedTask, array(
                                        'class' => 'form-control select2',
                                        'id' => 'task_code',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('task_code'); ?> </span>
                                </div>
                            </div>
                            <!-- Actual project -->
                            <div class="form-group">
                                <?php
                                echo form_label('Actual project', 'actual_project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('actual_project', $actualprojectDropdown, $selectedactualProject, array(
                                        'class' => 'form-control select2',
                                        'id' => 'actual_project',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('actual_project'); ?> </span>
                                </div>
                            </div>
                            <!-- Select task -->
                            <div class="form-group">
                                <?php
                                echo form_label('Actual Task', 'target_task_code', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10" id="targetTaskDiv">
                                    <?php
                                    echo form_dropdown('target_task_code', $taskDropdown, $selectedTaskTarget, array(
                                        'class' => 'form-control select2',
                                        'id' => 'target_task_code',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('target_task_code'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Save',
                                        'class' => 'btn btn-small btn-primary',
                                        'id' => 'submit'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                    <a href="javascript:window.history.go(-1);">
                                        <?php
                                        $submitButton = array(
                                            'type' => 'button',
                                            'content' => 'Cancel',
                                            'id' => 'cancel',
                                            'class' => 'btn btn-small btn-primary'
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </a>
                                </div>
                                <!-- /.col -->
                            </div>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent</th>
                                        <th>Previous project</th>
                                        <th>Task code</th>
                                        <th>Actual project</th>
                                        <th>Target Task code</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Created by</th>
                                        <th>Create Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($tabledata as $tabledata) {
                                        ?>
                                        <tr class="notitle" data-toggle="tooltip" 
                                            title="" data-original-title="<?php echo $tabledata['note'] ? 'Note : ' . $tabledata['note'] : ''; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['talent_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['previous_project_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['task_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['actual_project_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['target_task_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($tabledata['from_date'] != '0000-00-00') {
                                                    echo date('d/m/Y', strtotime($tabledata['from_date']));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($tabledata['to_date'] != '0000-00-00') {
                                                    echo date('d/m/Y', strtotime($tabledata['to_date']));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $tabledata['created_by'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($tabledata['created_at']));
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        echo form_close();
                        ?>
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
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    $("body").on("change", "#previous_project", function (e) {
        e.preventDefault();
        var project_id = $(this).val();
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/TimePredictionCntrl/getTaskByProject",
            type: "get", //send it through get method
            data: {
                project_id: project_id
            },
            success: function (response) {
                $("#prevTaskDiv").html(response);
                $("#task_code").select2({
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
    $("body").on("change", "#actual_project", function (e) {
        e.preventDefault();
        var project_id = $(this).val();
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/TimePredictionCntrl/getTaskByProjectActual",
            type: "get", //send it through get method
            data: {
                project_id: project_id
            },
            success: function (response) {
                $("#targetTaskDiv").html(response);
                $("#target_task_code").select2({
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $(".form-horizontal").validate();
        $("body").on("submit", function (event) {
            event.preventDefault();
            var base_url = '<?php echo base_url() ?>';
            var previous_project = $('#previous_project').val();
            var actual_project = $('#actual_project').val();
            var task_code = $('#task_code').val();
            var target_task_code = $('#target_task_code').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var talent = $('#talent').val();
            alertify.confirm("Are you sure to remap project timesheet?", function (e) {
                if (e) {
                    $("#overlay1").show();
                    $.ajax({
                        url: base_url + "index.php/TimesheetRemapping/databaseoperations",
                        type: "post", //send it through post method
                        data: {
                            previous_project: previous_project, 
                            actual_project: actual_project, 
                            task_code: task_code, 
                            target_task_code: target_task_code, 
                            fromDate: fromDate,
                            talent: talent,
                            toDate: toDate,
                        },
                        success: function (response) {
                            $(location).attr('href', base_url + "index.php/TimesheetRemapping");

                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    });
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
    });
    $("body").on("keyup", ".numeric", function (event) {
        if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        }
    });
    $('#table_responsive').DataTable();
</script>

