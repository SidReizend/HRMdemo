<?php
$datatables = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
    </div>
        <!-- Modal Approve skill -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Skill Approve</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            <input name="skillName" value="" id="skillName" class="form-control" type="text">
                            <span class="text-danger skillNameErrMsg"></span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success approveSubmit" data-id=''>Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal approve skill end -->
    <!-- Modal replace skill -->
    <div class="modal fade" id="modalReplaceSkill" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Skills Replace</h4>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <label> Skill</label>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            $urlText = array(
                                'name' => 'otherSkill',
                                'id' => 'otherSkill',
                                'class' => 'form-control',
                                'readonly' => 'true',
                                'value' => set_value('otherSkill'),
                            );

                            echo form_input($urlText);
                            ?>
                            <span class="text-danger errorMsgOthersSkill"></span>
                        </div>
                        <div class="col-sm-2">
                            <label> Replace with</label>
                        </div>
                        <div class="col-sm-4">
                            <select id="replaceSkill" class="select2">
                                <option value="">Select Skill</option>
                            </select>
                            <span class="text-danger errorMsgSkill"></span>
                        </div>
                    </div>
                    <br>
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success replacedData" >Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal replace skill end -->
    <!-- Modal delete confirm box  -->
    <div class="modal fade" id="modalDeleteSkill" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Confirm Box</h4>
                </div>
                <div class="modal-body">
                    <p>Do you wants to delete!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info deleteButton" data-id=''>Yes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
<!-- Modal delete confirm box end -->
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Skills View</h3>
                </div>
                <div class="box-body">
                    <?php
                    echo form_open('', array(
                        'class' => 'form-horizontal',
                        'id' => 'skillsView',
                    ));
                    ?>
                    <?php
                    echo form_close();
                    ?>
                    <table id="table_responsive" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Talent Name</th>
                                <th>Skill</th>
                                <th>Year Of Experience</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $serialNumber = 1;
                            if (isset($userSkills)) {
                                foreach ($userSkills as $userSkill) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo $serialNumber++;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $userSkill['first_name'] . ' ' . $userSkill['middle_name'] . ' ' . $userSkill['first_name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $userSkill['skill_name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $userSkill['year_of_experience'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($userSkill['is_approved'] == 1) {
                                                ?>
                                                <button class="btn btn-default">Approved</button> 
                                            <?php } else { ?>
                                                <button class="btn btn-default approveBtn" data-toggle="modal"  data-text='<?php echo $userSkill['skill_name']; ?>' data-target="#myModal" data-id='<?php echo $userSkill['skillId']; ?>'>Approve</button>
                                                <button class="btn btn-default replaceBtn" data-toggle="modal" data-user='<?php echo $userSkill['user_id']; ?>' data-text='<?php echo $userSkill['skill_name']; ?>' data-target="#modalReplaceSkill" data-id='<?php echo $userSkill['skillId']; ?>'>Replace</button>
                                                <button class="btn btn-default deleteBtn" data-toggle="modal" data-myskill='<?php echo $userSkill['mySkillId']; ?>' data-skill="<?php echo $userSkill['skillId']; ?>" data-target="#modalDeleteSkill">Remove</button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        border: 1px solid #d2d6de;
        border-radius: 0;
        height: 34px;
        padding: 6px 100px !important;
    }
</style>
</div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();

    });
    $("body").on("click", ".deleteBtn", function (e) {
        $('.deleteButton').attr('data-myskill',$(this).data('myskill'));
        $('.deleteButton').attr('data-skill',$(this).data('skill'));
    });
    $("body").on("click", ".approveBtn", function (e) {
        $('.approveSubmit').attr('data-id', $(this).data('id'));
        $('#skillName').val($(this).data('text'));
        $('.skillNameErrMsg').html('');
    });
    $("body").on("click", ".approveSubmit", function (e) {
        var skillId = $(this).data("id");
        $('.skillNameErrMsg').html('');
        var skillName = $('#skillName').val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/approveSkills",
            type: "post", //send it through post method
            dataType: 'json',
            data: {
                skillId: skillId,
                skillName: skillName
            },
            success: function (response) {
                if (response.status == "error") {
                    $('.skillNameErrMsg').html('Skill already exist!');
                    return false;
                } else {
                    location.reload();
                }
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    });
    $("body").on("click", ".replaceBtn", function (e) {
        $('#replaceSkill').html('<option value="">Select Skill</option>');
        var skillName = $(this).data("text");
        var user = $(this).data("user");
        var skillsId = $(this).data("id");
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/replcaeSkillModel",
            type: "post", //send it through post method
            dataType: 'json',
            data: {
                skillName: skillName,
                user: user
            },
            success: function (response) {
                var option;
                $.each(response, function (key, value) {
                    option += '<option  value=' + key + '>' + value + '</option>';
                });
                $('#replaceSkill').html(option);
                $('#otherSkill').val(skillName);
                $('.replacedData').attr('data-id', skillsId);
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }

        });
    });
    $("body").on("click", ".replacedData", function (e) {
        var replacedWithSkill = $('#replaceSkill').val();
        var replaceSkill = $(this).data('id');
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/replaceSkill",
            type: "post", //send it through post method
            dataType: 'json',
            data: {
                replacedWithSkill: replacedWithSkill,
                replaceSkill: replaceSkill
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }

        });
    });
    $("body").on("click", ".deleteButton", function (e) {
        var myskill = $(this).data('myskill');
        var skill = $(this).data('skill');
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/deleteSkills",
            type: "post", //send it through post method
            dataType: 'json',
            data: {
                myskill: myskill,
                skill: skill
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }

        });
    });
</script>

