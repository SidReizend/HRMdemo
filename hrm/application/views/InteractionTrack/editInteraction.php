<?php
$select2 = 1;
$datepicker = 1;
$form_validator2 = 1;
$wysihtml5 = 1;
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
                            <h3 class="box-title">Talent Interaction</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Date', 'date', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'date',
                                        'id' => 'date',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $interactionDetails->from_date != '0000-00-00' ? date('d/m/Y', strtotime($interactionDetails->from_date)) : '',
                                        'required' => 'required'
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('date'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Talents', 'stable_talents', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">   <?php
                                    echo form_dropdown('stable_talents', $talents, $interactionDetails->talent, array(
                                        'class' => 'form-control select2',
                                        'id' => 'stable_talents',
                                        'required' => 'required'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('stable_talents'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Type', 'type', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('type', $types, $interactionDetails->interaction_type, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('type'); ?> </span>
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
                                        'value' => $interactionDetails->description,
                                        'required' => 'required',
                                    );
                                    echo form_textarea($descriptionText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('description'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Reported By', 'managers', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">   <?php
                                    echo form_dropdown('managers', $managers, $interactionDetails->reporting_manager, array(
                                        'class' => 'form-control select2',
                                        'id' => 'managers',
                                        'required' => 'required'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('managers'); ?> </span>
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
        $(".form-horizontal").validate();
        tinymce.init({
            selector: '#description',
            convert_urls: false,
            remove_script_host: true,
            menubar:false,
            statusbar: false,
        });
    
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    }).on('change', function () {
        $(this).valid();   // triggers the validation test
    });
</script>
