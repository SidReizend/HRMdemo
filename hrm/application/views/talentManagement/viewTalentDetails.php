<?php
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
                                            $role = $talentSpecific['role_name'];
                                            $address = $talentSpecific['address'];
                                            $qualification = $talentSpecific['qualification'];
                                            $gender = $talentSpecific['gender'];
                                            $dob = date('d/m/Y', strtotime($talentSpecific['dob']));
                                            $mobile = $talentSpecific['mobile'];
                                            $email = $talentSpecific['email'];
                                            $dateOfJoining = date('d/m/Y', strtotime($talentSpecific['date_of_joining']));
                                            $emergencyContactName = $talentSpecific['emergency_contact_name'];
                                            $emergencyContactRelationship = $talentSpecific['emergency_contact_relationship'];
                                            $emergencyContactNumber = $talentSpecific['emergency_contact_number'];
                                            $reportingManager = $talentSpecific['reporting_manager'];
                                            $dateOfResignation = date('d/m/Y', strtotime($talentSpecific['date_of_resignation']));
                                            $isResigned = $talentSpecific['is_resigned'];
                                        }
                                        foreach ($userDetailsByTalentId as $userSpecific) {
                                            $username = $userSpecific['username'];
                                            $isEnabled = $userSpecific['is_enabled'];
                                        }

                                        echo form_label('First Name', 'firstName', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($firstName, 'firstName', array(
                                            'class' => 'col-sm-2 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Middle Name', 'middleName', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($middleName, 'middleName', array(
                                            'class' => 'col-sm-2 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Last Name', 'lastName', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($lastName, 'lastName', array(
                                            'class' => 'col-sm-2 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Role', 'role', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        echo form_label($role, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Address', 'address', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($address, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Qualification', 'qualification', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($qualification, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>

                                    <div class="form-group">
                                        <?php
                                        echo form_label('Gender', 'gender', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        if ($gender == 'M') {
                                            $genderName = 'Male';
                                        }
                                        if ($gender == 'F') {
                                            $genderName = 'Female';
                                        }
                                        if ($gender == 'O') {
                                            $genderName = 'Other';
                                        }
                                        echo form_label($genderName, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group text-center">
                                        <?php $imagePath = (isset($profile_image->profile_image) && '' != $profile_image->profile_image ) ? 'uploads/' . $profile_image->profile_image : 'assets/dist/img/default.png'; ?>
                                        <img alt="User Image" src="<?php echo base_url() . $imagePath; ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">

                                    <div class="form-group">
                                        <?php
                                        echo form_label('Date of Birth', 'dob', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($dob, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Mobile', 'mobile', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($mobile, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Email', 'email', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($email, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Date of Joining', 'dateOfJoining', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($dateOfJoining, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Emergency Contact Name', 'emergencyContactName', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($emergencyContactName, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Emergency Contact Relationship', 'emergencyContactRelationship', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($emergencyContactRelationship, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Emergency Contact Number', 'emergencyContactNumber', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($emergencyContactNumber, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Reporting Manager', 'reportingManager', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        echo form_label($reportingManagers, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Username', 'username', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        echo form_label($username, 'lastName', array(
                                            'class' => 'col-sm-5 control-label',
                                            'style' => 'text-align: left'
                                        ));
                                        ?>
                                    </div>
                                </div>
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
        $('#talentEdit input').attr('readonly', 'readonly');
        $(".select2").select2();
    });

    $.validate({
        lang: 'en'
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true
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

