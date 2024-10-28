<?php
$form_validator2 = 1;
$alertify = 1;
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
                'id' => 'eventForm'
            ));
            ?>
            <?php
            foreach ($projectTypeDetails as $task) {
                $targetedValue = $task['targeted_value'];
                $projectType = $task['project_type'];
                $scheduleId = $task['schedule_id'];
                $targetDate = $task['target_date'];
                $validDate = $task['valid_date'];
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Project Type Edit</h3>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <?php
                                echo form_label('Project Type', 'ProjectType', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'project_type',
                                        'id' => 'project_type',
                                        'placeholder' => '',
                                        'class' => 'form-control',
                                        'data-validation' => 'required',
                                        'value' => $projectType,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('project_type'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Schedule', 'schedule_id', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('schedule_id', $schedules, $selectedSchedule, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('schedule_id'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Targeted Value', 'targeted_value', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'targeted_value',
                                        'id' => 'targeted_value',
                                        'class' => 'form-control',
                                        'data-validation' => 'required',
                                        'value' => $targetedValue,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('targeted_value'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Target Date', 'target_date', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $targetDatetxt = array(
                                        'name' => 'target_date',
                                        'id' => 'target_date',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $targetDate,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($targetDatetxt);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('target_date'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Valid Till', 'valid_date', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $validDatetxt = array(
                                        'name' => 'valid_date',
                                        'id' => 'valid_date',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $validDate,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($validDatetxt);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('valid_date'); ?> </span>
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
                                        'class' => 'btn btn-small btn-primary'
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
                        </div>
                        <?php
                        echo form_close();
                        ?>
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $("#eventForm").validate();
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

</script>

