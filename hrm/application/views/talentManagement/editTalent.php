<?php
$select2 = 1;
$form_validator = 1;
$datepicker = 1;
$login_details = $this->session->userdata('logged_in');
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal',
                'id' => 'talentEdit'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Talent Details Modification</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <?php
                                        foreach ($talentDetailsById as $talentSpecific) {
                                            //  $id = $talentSpecific['id'];
                                            $firstName = $talentSpecific['first_name'];
                                            $middleName = $talentSpecific['middle_name'];
                                            $lastName = $talentSpecific['last_name'];
                                            $role = $talentSpecific['role'];
                                            $address = $talentSpecific['address'];
                                            $qualification = $talentSpecific['qualification'];
                                            $present_address = $talentSpecific['present_address'];
                                            $gender = $talentSpecific['gender'];
                                            $dob = date('d/m/Y', strtotime($talentSpecific['dob']));
                                            $mobile = $talentSpecific['mobile'];
                                            $email = $talentSpecific['email'];
                                            $dateOfJoining = date('d/m/Y', strtotime($talentSpecific['date_of_joining']));
                                            $emergencyContactName = $talentSpecific['emergency_contact_name'];
                                            $emergencyContactRelationship = $talentSpecific['emergency_contact_relationship'];
                                            $emergencyContactNumber = $talentSpecific['emergency_contact_number'];
                                            $reportingManager = $talentSpecific['reporting_manager'];
                                            $dateOfResignation = $talentSpecific['date_of_resignation'];
                                            $isResigned = $talentSpecific['is_resigned'];
                                            $autoOdApproval = $talentSpecific['od_approval_auto'];
                                            $autoTimeApproval = $talentSpecific['autoTimeApproval'];
                                            $track = $talentSpecific['track'];
                                        }
                                        foreach ($userDetailsByTalentId as $userSpecific) {
                                            $username = $userSpecific['username'];
                                            $isEnabled = $userSpecific['is_enabled'];
                                        }

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
                                                'value' => $firstName,
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
                                                'value' => $middleName,
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
                                                'value' => $lastName,
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
                                            echo form_dropdown('role', $roles_dropdown, $role, array(
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
                                        echo form_label('Address', 'address', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-5">  <?php
                                            $addressText = array(
                                                'name' => 'address',
                                                'id' => 'address',
                                                'class' => 'form-control',
                                                'value' => $address,
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
                                        echo form_label('Present Address', 'address', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-5">  <?php
                                            $addressText = array(
                                                'name' => 'present_address',
                                                'id' => 'present_address',
                                                'class' => 'form-control',
                                                'value' => $present_address,
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
                                                'value' => $qualification,
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
                                            $gender_is_checked = $gender === 'M';
                                            $genderRadio = array(
                                                'name' => 'gender',
                                                'id' => 'male',
                                                'checked' => $gender_is_checked,
                                                'value' => 'M'
                                            );
                                            ?> 
                                            <?php
                                            echo form_label(form_radio($genderRadio) . 'Male', 'male', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>

                                            <?php
                                            $gender_is_checked = $gender === 'F';
                                            $genderRadio = array(
                                                'name' => 'gender',
                                                'id' => 'female',
                                                'checked' => $gender_is_checked,
                                                'value' => 'F'
                                            );
                                            echo form_label(form_radio($genderRadio) . 'Female', 'female', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>
                                            <?php
                                            $gender_is_checked = $gender === 'O';
                                            $genderRadio = array(
                                                'name' => 'gender',
                                                'id' => 'other',
                                                'checked' => $gender_is_checked,
                                                'value' => 'O'
                                            );
                                            echo form_label(form_radio($genderRadio) . 'Other', 'other', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('gender'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group text-center">
                                        <?php $imagePath = (isset($profile_image->profile_image) && '' != $profile_image->profile_image ) ? 'uploads/' . $profile_image->profile_image : 'assets/dist/img/default.png'; ?>
                                        <img style="width: 300px;height: 300px;" alt="User Image" src="<?php echo base_url() . $imagePath; ?>">
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Profile Image', 'profile_image', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $upload = Array("name" => "profile_image", "id" => "profile_image");
                                            echo form_upload($upload);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('profile_image'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">

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
                                                'value' => $dob,
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
                                                'value' => $mobile,
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
                                                'value' => $email,
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
                                                'value' => $dateOfJoining,
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
                                                'value' => $emergencyContactName,
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
                                                'value' => $emergencyContactRelationship,
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
                                                'value' => $emergencyContactNumber,
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
                                            echo form_dropdown('reportingManager', $reportingManagers_dropdown, $reportingManager, array(
                                                'class' => 'form-control select2'
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
                                                'value' => $username,
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
                                            echo form_dropdown('isEnabled', $isEnabled_dropdown, $isEnabled, array(
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
                                        <?php
                                        echo form_label('Auto OD approval', 'autoOdApproval', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $autoOdApproval_dropdown = array(
                                                '1' => 'Yes',
                                                '0' => 'No'
                                            );
                                            echo form_dropdown('autoOdApproval', $autoOdApproval_dropdown, $autoOdApproval, array(
                                                'class' => 'form-control',
                                                'id' => 'autoOdApproval',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('autoOdApproval'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Auto Time approval', 'autoTimeApproval', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $autoTimeApproval_dropdown = array(
                                                '1' => 'Yes',
                                                '0' => 'No'
                                            );
                                            echo form_dropdown('autoTimeApproval', $autoTimeApproval_dropdown, $autoTimeApproval, array(
                                                'class' => 'form-control',
                                                'id' => 'autoTimeApproval',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('autoTimeApproval'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Resigned', 'isResigned', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $isResigned_dropdown = array(
                                                '1' => 'Yes',
                                                '0' => 'No'
                                            );
                                            echo form_dropdown('isResigned', $isResigned_dropdown, $isResigned, array(
                                                'class' => 'form-control',
                                                'id' => 'isResigned',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('isResigned'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group" id="resignationDiv">
                                        <?php
                                        echo form_label('Date of Resignation', 'dateOfResignation', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-10">  <?php
                                            $dateOfResignationText = array(
                                                'name' => 'dateOfResignation',
                                                'id' => 'dateOfResignation',
                                                'placeholder' => 'dd/mm/YYYY',
                                                'class' => 'form-control datepicker',
                                                'value' => (($dateOfResignation == '1970-01-01') || $dateOfResignation == '' || $dateOfResignation == '0000-00-00' || ($dateOfResignation == '30/11/-0001')) ? '' : date('d/m/Y', strtotime($dateOfResignation)),
                                                'data-validation' => 'required',
                                            );

                                            echo form_input($dateOfResignationText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('dateOfResignation'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            //provides update only if not resigned
                            if (0 == $isResigned) {
                                ?>
                                <div class="form-group">
                                    <div class="col-sm-2 control-label">
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-xs-10">
                                        <input type="hidden" id="talentId" name="talentId" value="<?php echo $talentId; ?>">
                                        <?php
                                        $submitButton = array(
                                            'type' => 'submit',
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
                                <?php
                            }
                            ?>
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
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    $(document).ready(function () {
        $(".select2").select2();
        isResigned = $("#isResigned").val();
        if (isResigned == 1) {
            $("#resignationDiv").show();
        } else {
            $("#resignationDiv").hide();
        }
        $("#save").on('click', function (e) {
            var resigned = $('#isResigned').val();
            var talentId = $('#talentId').val();
            var resignDate = $('#dateOfResignation').val();
            if (resigned == 1) {
                e.preventDefault();
                base_url = "<?php echo base_url() ?>";
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/talents/checkResignIssues",
                    type: "get", //send it through get method
                    data: {
                        talentId: talentId,
                        resignDate: resignDate,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == true) {
                            $('#talentEdit').submit();
                        } else {
                            alertify.alert(response);
                        }
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                }).complete(function () {
                    var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                    var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                    $('[name="' + csrf_name + '"]').val(cct);
                });
            }
        });
    });

    $.validate({
        lang: 'en'
    });
    $("body").on("change", "#isResigned", function (e) {
        isResigned = $("#isResigned").val();
        if (isResigned == 1) {
            $("#resignationDiv").show();
        } else {
            $("#resignationDiv").hide();
        }
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

