<?php
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('PrevilageCntrler/addTalentLeave', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Privilege Controller</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php
                                    echo form_label('User', 'user', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-8">
                                        <?php
                                        echo form_dropdown('user', $user, $selected_user, array(
                                            'class' => 'form-control select2 uservalue',
                                            'data-validation' => 'required',
                                            'id' => 'user'
                                        ));
                                        ?>
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <!-- List group -->
                                <ul class="list-group">
                                    <?php
                                    $count = 0;
                                    foreach ($first_part_of_role_details as $first_part_of_role) {
                                        ?>
                                        <li class="list-group-item">
                                            <?php echo $first_part_of_role['role_name']; ?>
                                            <div class="material-switch pull-right">
                                                <input id="<?php echo $first_part_of_role['id']; ?>" class="someSwitchOption001" type="checkbox"/>
                                                <label for="<?php echo $first_part_of_role['id']; ?>" class="label-success"></label>
                                            </div>
                                        </li>
                                        <?php
                                        $count ++;
                                        if ($count > 5) {
                                            $count = 0;
                                        }
                                    }
                                    ?>
                                </ul>     
                            </div>
                            <div class="col-xs-6">
                                <!-- List group -->
                                <ul class="list-group">
                                    <?php
                                    $count = 0;
                                    foreach ($second_part_of_role_details as $second_part_of_role) {
                                        ?>
                                        <li class="list-group-item">
                                            <?php echo $second_part_of_role['role_name']; ?>
                                            <div class="material-switch pull-right">
                                                <input id="<?php echo $second_part_of_role['id']; ?>" class="someSwitchOption001"  type="checkbox"/>
                                                <label for="<?php echo $second_part_of_role['id']; ?>" class="label-success"></label>
                                            </div>
                                        </li>
                                        <?php
                                        $count ++;
                                        if ($count > 5) {
                                            $count = 0;
                                        }
                                    }
                                    ?>
                                </ul>     
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo form_close();
            ?>
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $(".select2").select2();
        $("body").on("change", "#user", function (e) {

            var selected_user = $("body .someSwitchOption001");
            $.each(selected_user, function (key, value) {
                $(value).prop('checked', false);
            });

            var request = this.value;
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/additionalRoleMapping/allAdditionalRoles",
                dataType: 'json',
                data: {
                    request: request,
                },
                success: function (data) {
                    $.each(data, function (key, value) {
                        if (value != '') {
                            $("#" + value).prop('checked', true);
                        }
                    });

                }
            });
        });
        $("body").on("change", ".someSwitchOption001", function (e) {
            check = 0;
            var user = $("#user").val();
            if (user == "") {
                alertify.error("Select any user");
                return false;
            }

            var role = this.id;
            if ($(this).prop("checked") == true) {
                check = 1;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/additionalRoleMapping/saveAdditionalRoles",
                dataType: 'json',
                data: {
                    user: user,
                    role: role,
                    check: check,
                },
                success: function (data) {
                    if (data.status == 1) {
                        alertify.success(data.message);
                    } else {
                        alertify.error(data.message);
                    }
                }
            });
        });

    });
</script>