<?php
$form_validator = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            echo form_open('', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <?php
            //var_dump($myOdDetailsById);
            foreach ($myLeaveDetailsById as $myLeaveSpecific) {
                $selectedLeaveType = $myLeaveSpecific['leave_type'];
                $request = $myLeaveSpecific['request'];
                $fromDate = $myLeaveSpecific['from_date'];
                $toDate = $myLeaveSpecific['to_date'];
                $isApproved = $myLeaveSpecific['is_approved'];
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Leave Addition</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Leave Type', 'leaveType', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $leaveTypeDropdown = $leaveTypes;
                                    echo form_dropdown('leaveType', $leaveTypeDropdown, $selectedLeaveType, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('leaveType'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Request', 'request', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $requestText = array(
                                        'name' => 'request',
                                        'id' => 'request',
                                        'class' => 'form-control',
                                        'rows' => '3',
                                        'value' => $request,
                                        'data-validation' => 'required',
                                    );

                                    echo form_textarea($requestText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('request'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('From Date', 'fromDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control',
                                        'placeholder' => 'reason',
                                        'value' => date('d-m-Y',  strtotime($fromDate)),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('To Date', 'toDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control',
                                        'placeholder' => 'reason',
                                        'value' => date('d-m-Y',  strtotime($toDate)),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                    <div class="col-xs-5">&nbsp;
                                    </div>
                                    <div class="col-xs-5">
                                        Leaves Applied For
                                    </div>
                                </div>
                            <?php
                            foreach ($myLeaveSpecificByMyLeaveId as $eachDays) {
                                ?>
                                <div class="form-group">
                                    <div class="col-xs-5">&nbsp;
                                    </div>
                                    <div class="col-xs-5">
                                        <?php
                                        echo form_label(date('d-m-Y',  strtotime($eachDays['date_of_leave'])), 'dateOfLeave', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
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

    $.validate({
        lang: 'en'
    });
//Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });
</script>

