<?php
$select2 = 1;
$form_validator = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('Talents/addNewTalent', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Talent Details Addition</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Talent Code', 'talentCode', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $talentCodeText = array(
                                        'name' => 'talentCode',
                                        'id' => 'talentCode',
                                        'class' => 'form-control',
                                        'value' => $talentCode,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($talentCodeText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('firstName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('First Name', 'firstName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $firstNameText = array(
                                        'name' => 'firstName',
                                        'id' => 'firstName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('firstName'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($firstNameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('firstName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Middle Name', 'middleName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $middleNameText = array(
                                        'name' => 'middleName',
                                        'id' => 'middleName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('middleName'),
                                    );

                                    echo form_input($middleNameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('middleName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Last Name', 'lastName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $lastNameText = array(
                                        'name' => 'lastName',
                                        'id' => 'lastName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('lastName'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($lastNameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('lastName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Role', 'role', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $roles_dropdown = $roles;
                                    echo form_dropdown('role', $roles_dropdown, $selected_role, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                        'id' => 'role',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Permanent Address', 'address', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-5">  <?php
                                    $addressText = array(
                                        'name' => 'address',
                                        'id' => 'address',
                                        'class' => 'form-control',
                                        'value' => set_value('address'),
                                        'rows' => '5',
                                        'cols' => '5',
                                        'data-validation' => 'required',
                                    );

                                    echo form_textarea($addressText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('address'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Present Address', 'present_address', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-5">  <?php
                                    $addressText = array(
                                        'name' => 'present_address',
                                        'id' => 'present_address',
                                        'class' => 'form-control',
                                        'value' => set_value('present_address'),
                                        'rows' => '6',
                                        'cols' => '6',
                                        'data-validation' => 'required',
                                    );

                                    echo form_textarea($addressText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('address'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Qualification', 'qualification', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $qualificationText = array(
                                        'name' => 'qualification',
                                        'id' => 'qualification',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('qualification'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($qualificationText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('qualification'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Gender', 'gender', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-5">  <?php
                                    $genderRadio = array(
                                        'name' => 'gender',
                                        'id' => 'male',
                                        'value' => 'M',
                                        'checked' => set_radio('gender', 'M', TRUE)
                                    );
                                    ?> 
                                    <?php
                                    echo form_label(form_radio($genderRadio) . 'Male', 'male', array(
                                        'class' => 'col-sm-3 control-label'
                                    ));
                                    ?>

                                    <?php
                                    $genderRadio = array(
                                        'name' => 'gender',
                                        'id' => 'female',
                                        'value' => 'F',
                                        'checked' => set_radio('gender', 'F')
                                    );
                                    echo form_label(form_radio($genderRadio) . 'Female', 'female', array(
                                        'class' => 'col-sm-3 control-label'
                                    ));
                                    ?>
                                    <?php
                                    $genderRadio = array(
                                        'name' => 'gender',
                                        'id' => 'other',
                                        'value' => 'O',
                                        'checked' => set_radio('gender', 'O')
                                    );
                                    echo form_label(form_radio($genderRadio) . 'Other', 'other', array(
                                        'class' => 'col-sm-3 control-label'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('gender'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Date of Birth', 'dob', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $dobText = array(
                                        'name' => 'dob',
                                        'id' => 'dob',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('dob'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($dobText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('dob'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Mobile', 'mobile', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $mobileText = array(
                                        'name' => 'mobile',
                                        'id' => 'mobile',
                                        'class' => 'form-control',
                                        'placeholder' => '9876543210',
                                        'value' => set_value('mobile'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($mobileText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('mobile'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Email', 'email', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $emailText = array(
                                        'name' => 'email',
                                        'id' => 'email',
                                        'class' => 'form-control',
                                        'placeholder' => 'example@example.com',
                                        'value' => set_value('email'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($emailText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('email'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Date of Joining', 'dateOfJoining', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $dateOfJoiningText = array(
                                        'name' => 'dateOfJoining',
                                        'id' => 'dateOfJoining',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('dateOfJoining'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($dateOfJoiningText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('dateOfJoining'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Emergency Contact Name', 'emergencyContactName', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $emergencyContactNameText = array(
                                        'name' => 'emergencyContactName',
                                        'id' => 'emergencyContactName',
                                        'class' => 'form-control',
                                        'placeholder' => 'name',
                                        'value' => set_value('emergencyContactName'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($emergencyContactNameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('emergencyContactName'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Emergency Contact Relationship', 'emergencyContactRelationship', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $emergencyContactRelationshipText = array(
                                        'name' => 'emergencyContactRelationship',
                                        'id' => 'emergencyContactRelationship',
                                        'class' => 'form-control',
                                        'placeholder' => 'Relationship',
                                        'value' => set_value('emergencyContactRelationship'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($emergencyContactRelationshipText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('emergencyContactRelationship'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Emergency Contact Number', 'emergencyContactNumber', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $emergencyContactNumberText = array(
                                        'name' => 'emergencyContactNumber',
                                        'id' => 'emergencyContactNumber',
                                        'class' => 'form-control',
                                        'placeholder' => '9876543210',
                                        'value' => set_value('emergencyContactNumber'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($emergencyContactNumberText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('emergencyContactNumber'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Reporting Manager', 'reportingManager', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10" id="reporting">
                                    <?php
                                    $reportingManagers_dropdown = $reportingManagers;
                                    echo form_dropdown('reportingManager', $reportingManagers_dropdown, $selected_reportingManager, array(
                                        'class' => 'form-control select2',
//                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reportingManager'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Username', 'username', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $usernameText = array(
                                        'name' => 'username',
                                        'id' => 'username',
                                        'class' => 'form-control',
                                        'placeholder' => 'username',
                                        'value' => set_value('username'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($usernameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('username'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Status', 'isEnabled', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $isEnabled_dropdown = array(
                                        '1' => 'Enable',
                                        '0' => 'Disable'
                                    );
                                    echo form_dropdown('isEnabled', $isEnabled_dropdown, $selected_isEnabled, array(
                                        'class' => 'form-control'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('isEnabled'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Track', 'track', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $track_dropdown = array(
                                        'Accounts and Operations' => 'Accounts and Operations',
                                        'Web' => 'Web',
                                        'Mobile' => 'Mobile',
                                        'Test and Implementation' => 'Test and Implementation',
                                        'SCA' => 'SCA',
                                    );
                                    echo form_dropdown('track', $track_dropdown, $track, array(
                                        'class' => 'form-control'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('track'); ?> </span>
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
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    $("body").on("change", "#role", function (e) {
        role = $("#role").val();
        base_url = '<?php echo base_url() ?>';
        //console.log();
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        $.ajax({
            url: base_url + "index.php/talents/reportingManagerChange",
            type: "post", //send it through get method
            data: {role: role, '<?php echo $this->security->get_csrf_token_name(); ?>': cct},
            success: function (response) {
                $("#reporting").html(response);
                $(".select2").select2();
            },
            error: function (xhr) {
                //Do Something to handle error
                alert("some error found");
            }
        });
    });
</script>

