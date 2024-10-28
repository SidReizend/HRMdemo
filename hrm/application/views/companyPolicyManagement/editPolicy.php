<?php
$form_validator = 1;
$wysihtml5 = 1;
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
                            <h3 class="box-title">Policy Addition</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Name Of Policy', 'policyName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $text = array(
                                        'name' => 'policyName',
                                        'id' => 'policyName',
                                        'class' => 'form-control',
                                        'placeholder' => 'policy name',
                                        'value' => $policyDetails->policy_name,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($text);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('policyName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Policy', 'policy', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <textarea id="compose-textarea" class="form-control" name="policy" style="height: 300px"><?php echo $policyDetails->content; ?></textarea>
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

    $.validate({
        lang: 'en'
    });
    tinymce.init({
        selector: '#compose-textarea',
        plugins: ["image"],
        convert_urls: false,
        remove_script_host: true,
        file_browser_callback: function (field_name, url, type, win) {
            if (type == 'image')
                $('#my_form input').click();
        }
    });
</script>

