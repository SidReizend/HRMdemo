<?php
$form_validator = 1;
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
                            <h3 class="box-title">Change Password</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Old password', 'oldPassword', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $oldPasswordText = array(
                                        'name' => 'oldPassword',
                                        'id' => 'oldPassword',
                                        'class' => 'form-control',
                                        'placeholder' => 'old password',
                                        'data-validation' => 'required',
                                    );

                                    echo form_password($oldPasswordText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('oldPassword'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('New password', 'newPassword', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $newPasswordText = array(
                                        'name' => 'newPassword',
                                        'id' => 'newPassword',
                                        'class' => 'form-control',
                                        'placeholder' => 'new password',
                                        'data-validation' => 'required',
                                    );

                                    echo form_password($newPasswordText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('newPassword'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Confirm password', 'confirmPassword', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $confirmPasswordText = array(
                                        'name' => 'confirmPassword',
                                        'id' => 'confirmPassword',
                                        'class' => 'form-control',
                                        'placeholder' => 'confirm password',
                                        'data-validation' => 'required',
                                    );

                                    echo form_password($confirmPasswordText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('confirmPassword'); ?> </span>
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
                                        'content' => 'Update',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
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

    $.validate({
        lang: 'en'
    });
</script>

