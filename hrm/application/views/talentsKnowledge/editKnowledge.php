
<style>

    .same_line
    {
        display: inline-block!important;
    }
</style>

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
            echo form_open_multipart('TalentKnowledge/editKnowledge', array(
                'class' => 'form-horizontal',
            ));


            foreach ($knowledge as $knowledge) {
                $knowledge_id = $knowledge['id'];
                $heading = $knowledge['heading'];
                $description = $knowledge['description'];
                $file_name = $knowledge['file_name'];
                $role = $knowledge['heading'];
                $note = $knowledge['note'];
                $verified=$knowledge['verified'];
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Update Knowledge Repository</h3>
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
                                        'placeholder' => 'Provide a Heading ',
                                        'data-validation' => 'required',
                                        'value' => $heading
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
                                        'rows' => '3',
                                        'data-validation' => 'required',
                                        'value' => $description
                                    );
                                    echo form_textarea($descriptionText);
                                    echo form_hidden('knowledgeId', $knowledge_id);
                                    echo form_hidden('old_file', $file_name);
                                    echo form_hidden('note', $note);
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
                                        "class" => 'same_line',
                                        'value' => $file_name
                                    );
                                    echo "<span  style='display:inline-block'>" . anchor(base_url() . 'index.php/docUrl/docUrlGenerator/' . $file_name, $file_name, 'target="_blank"') . "  &nbsp;&nbsp&nbsp;&nbsp;   Do you want to change the file?   " . form_upload($upload) . "</span>";
                                    ?>

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
                                    foreach ($knowledgeRoleStream as $roleStream) {

                                        $selectedRoles[] = $roleStream['id'];
                                    }

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
                                    foreach ($selectedTagArray as $selectedTag) {

                                        $selectedTags[] = $selectedTag['id'];
                                    }

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
                                        'type' => 'submit',
                                        'content' => 'Save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                     if (0 == $verified) {
                                    echo form_button($submitButton);
                                     }
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

