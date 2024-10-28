<?php
$datatables = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">My Skills</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo form_open('MySkill/addNewSkill', array(
                                'class' => 'form-horizontal'
                            ));
                            ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('skills', $skills, '', array(
                                                'class' => 'form-control select2 selectOptions',
                                                'data-validation' => 'required',
                                                'id' => 'skills',
                                            ));
                                            ?>
                                            <span class="text-danger errorMsgSkill"></span>

                                        </div>
                                        <div class="col-sm-2 otherSkillsDisplay" style ='display:none;'>
                                            <?php
                                            $urlText = array(
                                                'name' => 'otherSkills',
                                                'id' => 'otherSkills',
                                                'class' => 'form-control',
                                                'value' => set_value('otherSkills'),
                                            );

                                            echo form_input($urlText);
                                            ?>
                                            <span class="text-danger errorMsgOthers"></span>
                                        </div>
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('yearOfExperience', $years, '', array(
                                                'class' => 'form-control  selectOptions select2',
                                                'data-validation' => 'required',
                                                'id' => 'yearOfExperience',
                                            ));
                                            ?>
                                            <span class="text-danger errorMsgYear"></span>
                                        </div>
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('monthOfExperience', $month, '', array(
                                                'class' => 'form-control  selectOptions select2',
                                                'data-validation' => 'required',
                                                'id' => 'monthOfExperience',
                                            ));
                                            ?>
                                            <span class="text-danger errorMsgMonth"></span>
                                        </div>
                                       
                                    </div>
                                    <div class="row">
                                         <div class="col-sm-8">
                                            <?php
                                            echo form_label('Skill Level', 'skillLevel', array(
                                                'class' => 'col-sm-2 control-label'
                                            ));
                                            ?>
                                            <?php
                                            $skillRadio = array(
                                                'name' => 'skillLevel',
                                                'id' => 'beginner',
                                                'value' => 'Beginner',
                                                'checked' => set_radio('beginner', 'Beginner', TRUE)
                                            );
                                            ?> 
                                            <?php
                                            echo form_label(form_radio($skillRadio) . 'Beginner', 'beginner', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>

                                            <?php
                                            $skillRadio = array(
                                                'name' => 'skillLevel',
                                                'id' => 'intermediate',
                                                'value' => 'Intermediate',
                                                'checked' => set_radio('intermediate', 'Intermediate')
                                            );
                                            echo form_label(form_radio($skillRadio) . 'Intermediate', 'intermediate', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>
                                            <?php
                                            $skillRadio = array(
                                                'name' => 'skillLevel',
                                                'id' => 'expert',
                                                'value' => 'Expert',
                                                'checked' => set_radio('expert', 'Expert')
                                            );
                                            echo form_label(form_radio($skillRadio) . 'Expert', 'expert', array(
                                                'class' => 'col-sm-3 control-label'
                                            ));
                                            ?>
                                            <span class="text-danger skillLevel"></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="submit" id="addSkill" class="btn btn-small btn-primary">
                                                Add Skill
                                            </button>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-12 ">
                                            <div class="panel" >
                                                <div class="panel-body" style="border: 1px groove">
                                                    <?php
                                                    if (!empty($userSkills)) {
                                                        foreach ($userSkills as $userSkill) {
                                                            ?>
                                                            <button type="button" class="btn btn-primary deleteSkill" data-id="<?php echo $userSkill['mySkillId']; ?>"><?php echo $userSkill['skill_name'] . '  '; ?>
                                                                <span class="badge"><?php echo $userSkill['year_of_experience'] ?> Yr exp</span><span class="" ></span>
                                                                <span class="badge"><?php echo $userSkill['skill_level'] ?></span><span class="" ><i class="glyphicon glyphicon-remove "></i></span>
                                                            </button>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo "No Skills Added";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                            <div>
                                <center>

                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<style>
    .btn-label {position: relative;left: -12px;display: inline-block;padding: 6px 12px;background: rgba(0,0,0,0.15);border-radius: 3px 0 0 3px;}
    .btn-labeled {padding-top: 0;padding-bottom: 0;}
    .btn { margin-bottom:10px; }

    .box-body {
        height: 700px;
    }
</style>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });
    $("body").on("click", "#addSkill", function (e) {
        $('.errorMsgSkill').html(' ');
        $('.errorMsgYear').html(' ');
        $('.errorMsgMonth').html(' ');
        if ($('#skills').val() == '') {
            $('.errorMsgSkill').html('Please select a skill!');
            return false;
        } else if ($('#yearOfExperience').val() == '') {
            $('.errorMsgYear').html('Please select a year!');
            return false;
        } else if ($('#monthOfExperience').val() == '') {
            $('.errorMsgMonth').html('Please select a month!');
            return false;
        } else if ($('#skills').val() == 'others' && $('#otherSkills').val() == '') {
            $('.errorMsgOthers').html('Please enter the skill!');
            return false;
        }
    });

    $("body").on("change", ".selectOptions", function (e) {
        $('.otherSkillsDisplay').css('display', 'none');
        if ($('#skills').val() == 'others') {

            $('.otherSkillsDisplay').css('display', 'block');
        }
    });
    $("body").on("click", ".deleteSkill", function (e) {
        var skillId = $(this).data("id");
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/removeSkill",
            type: "post", //send it through get method
            data: {
                skillId: skillId,
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }

        })
    });


</script>

