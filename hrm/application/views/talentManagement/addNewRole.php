<?php
$select2 = 1;
$form_validator = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open('role/addNewRole', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Role Addition</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Role', 'role', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $rollText = array(
                                        'name' => 'role',
                                        'id' => 'role',
                                        'class' => 'form-control',
                                        'placeholder' => 'role',
                                        'value' => set_value('role'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($rollText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Description', 'description', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10"> <?php
                                    $descriptionText = array(
                                        'name' => 'description',
                                        'id' => 'description',
                                        'class' => 'form-control',
                                        'placeholder' => 'Description',
                                        'options' => '1',
                                        'value' => set_value('description'),
                                        'data-validation' => 'required',
                                    );
                                    echo form_input($descriptionText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('description'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Role Stream', 'roleStream', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $roleStreamDropdown = $roleStreams;
                                    echo form_dropdown('roleStream', $roleStreamDropdown, $selectedRoleStream, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('roleStream'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Holidays', 'holidays[]', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $days_dropdown = $weekDays;

                                    echo form_multiselect('holidays[]', $days_dropdown, $selected_holidays, array(
                                        'class' => 'form-control select2',
//                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('holidays[]'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Quarterly Earned Leaves', 'quarterLeaves', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $quarterLeavesText = array(
                                        'name' => 'quarterLeaves',
                                        'id' => 'quarterLeaves',
                                        'class' => 'form-control',
                                        'placeholder' => 'Quarterly EL',
                                        'value' => set_value('quarterLeaves'),
                                        'data-validation' => 'required',
                                        'data-validation' => 'number',
                                        'data-validation-allowing' => 'float',
                                    );
                                    echo form_input($quarterLeavesText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('quarterLeaves'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Average Working Hr/Day', 'averageWorkingHour', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">
                                    <?php
                                    $averageWorkingHourText = array(
                                        'name' => 'averageWorkingHour',
                                        'id' => 'averageWorkingHour',
                                        'class' => 'form-control',
                                        'placeholder' => 'hour',
                                        'value' => set_value('averageWorkingHour'),
                                        'data-validation' => 'required',
                                        'type' => 'number',
                                        'max' => '23',
                                        'data-validation-allowing' => 'float',
                                    );
                                    echo form_input($averageWorkingHourText);
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $averageWorkingMinitueText = array(
                                        'name' => 'averageWorkingMinitue',
                                        'id' => 'averageWorkingMinitue',
                                        'class' => 'form-control',
                                        'placeholder' => 'min',
                                        'value' => set_value('averageWorkingMinitue'),
                                        'data-validation' => 'required',
                                        'type' => 'number',
                                        'max' => '59',
                                        'data-validation-allowing' => 'float',
                                    );
                                    echo form_input($averageWorkingMinitueText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('averageWorkingHour'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Average Hour Calculation', 'averageHourCalculation', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $averageHourCalculationDropdown = array(
                                        '1' => 'Monthly',
                                        '2' => 'Quarterly'
                                    );
                                    echo form_dropdown('averageHourCalculation', $averageHourCalculationDropdown, $selectedAverageHourCalculation, array(
                                        'class' => 'form-control'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('averageHourCalculation'); ?> </span>
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
<!-- /.container -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });

    $.validate({
        lang: 'en'
    });
</script>

