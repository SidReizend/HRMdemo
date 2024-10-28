<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$select2 = 1;
$form_validator = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            echo form_open_multipart('TalentKnowledge/addKnowledge', array(
                'class' => 'form-horizontal',
                'id' => 'knowledgeRepo'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Create Knowledge Repository</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Heading', 'heading', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $headingText = array(
                                        'name' => 'heading',
                                        'id' => 'heading',
                                        'class' => 'form-control',
                                        'value' => set_value('heading'),
                                        'placeholder' => 'Provide a Heading ',
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($headingText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('heading'); ?> </span>
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
                                        'class' => 'textarea form-control',
                                        'placeholder' => 'Description',
                                        'value' => set_value('description'),
                                        'rows' => '3',
                                        'data-validation' => 'required',
                                    );
                                    echo form_textarea($descriptionText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('description'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Upload File', 'file_name', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $upload = Array(
                                        "name" => "file_name",
                                        "id" => "file_name",
                                        'value' => set_value('file_name'),
                                        'data-validation' => 'required',
                                    );
                                    echo form_upload($upload);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('file_name'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Stream Allowed', 'role_stream[]', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $roles = $role_stream;
                                    $selectedRoles = array();

                                    echo form_multiselect('role_stream[]', $roles, $selectedRoles, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('role_stream[]'); ?> </span>
                                </div>
                            </div>
                            
                              <div class="form-group">
                                <?php
                                echo form_label('Tags', 'tag_array[]', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $tags = $tag;
                                    $selectedTags = array();

                                    echo form_multiselect('tag_array[]', $tags, $selectedTags, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('tag_array[]'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'button',
                                        'content' => 'Save',
                                        'id' => 'save',
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
    $("#save").click(function () {
        var heading = $('#heading').val();
        var description = $('#description').val();
        if (heading.length < 10) {
            alertify.alert("minimum heading length is 10 characters");
        } else if (heading.length > 100) {
            alertify.alert("maximum heading length is 100 characters");
        } else if (description.length < 160) {
            alertify.alert("minimum description length is 160 characters");
        } else {
            $("#knowledgeRepo").submit();
        }
    });
</script>

