<?php
$form_validator = 1;
$datepicker = 1;
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
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Holiday Modification</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Date Of Holiday', 'dateOfHoliday', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    foreach ($holidayDetailsById as $holidaySpecific) {
                                        $nameOfHoliday = $holidaySpecific['name_of_holiday'];
                                        $dateOfHoliday = date('d/m/Y', strtotime($holidaySpecific['date_of_holiday']));
                                    }
                                    $dateOfHolidayText = array(
                                        'name' => 'dateOfHoliday',
                                        'id' => 'dateOfHoliday',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $dateOfHoliday,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($dateOfHolidayText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('dateOfHoliday'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Name Of Holiday', 'nameOfHoliday', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $nameOfHolidayText = array(
                                        'name' => 'nameOfHoliday',
                                        'id' => 'nameOfHoliday',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => $nameOfHoliday,
                                    );

                                    echo form_input($nameOfHolidayText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('nameOfHoliday'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    echo form_hidden('holidayId', $holidayId);
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
        $(".select2").select2();
    });

    $.validate({
        lang: 'en'
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
</script>

