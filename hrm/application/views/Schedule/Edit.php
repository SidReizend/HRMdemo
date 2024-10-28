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
            foreach ($scheduleDetails as $task) {
                $editschedule = $task['schedule'];
                $from = $task['from'];
                $to = $task['to'];
                $comments = $task['comments'];
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Schedule Edit</h3>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <?php
                                echo form_label('Schedule', 'schedule', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $schedule = array(
                                        'name' => 'schedule',
                                        'id' => 'schedule',
                                        'placeholder' => '',
                                        'class' => 'form-control',
                                        'data-validation' => 'required',
                                        'value' => $editschedule,
                                    );

                                    echo form_input($schedule);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('schedule'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('From Date', 'from', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $targetDatetxt = array(
                                        'name' => 'from',
                                        'id' => 'from',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $from,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($targetDatetxt);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('from'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('To Date', 'to', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $validDatetxt = array(
                                        'name' => 'to',
                                        'id' => 'to',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $to,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($validDatetxt);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('to'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Comments', 'comments', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'comments',
                                        'id' => 'comments',
                                        'class' => 'form-control',
                                        'data-validation' => 'required',
                                        'value' => $comments,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('comments'); ?> </span>
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

