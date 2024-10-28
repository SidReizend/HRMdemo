<?php
$form_validator = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
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
                            <h3 class="box-title">Head Addition</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Head Code', 'headCode', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $text = array(
                                        'name' => 'headCode',
                                        'id' => 'headCode',
                                        'class' => 'form-control',
                                        'placeholder' => 'code',
                                        'value' => set_value('headCode'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($text);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('headCode'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Name Of Head', 'headName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $text = array(
                                        'name' => 'headName',
                                        'id' => 'headName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('headName'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($text);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('headName'); ?> </span>
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
        $(".select2").select2();
    });

    $.validate({
        lang: 'en'
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true
    });
</script>

