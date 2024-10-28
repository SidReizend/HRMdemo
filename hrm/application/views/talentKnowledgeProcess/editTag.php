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
                            <h3 class="box-title">Tag Modification</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Name Of Tag', 'tagName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $tagNameText = array(
                                        'name' => 'tagName',
                                        'id' => 'tagName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => $tagDetails->tag_name,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($tagNameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('tagName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    echo form_hidden('tagId', $tagId);
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Save',
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

