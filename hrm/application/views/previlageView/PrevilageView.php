<?php
$alertify = 1;
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
                                    echo form_label('Roles', 'role', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-8">
                                        <?php
                                        $talents_dropdown = $roles;
                                        echo form_dropdown('roles', $talents_dropdown, $selected_role, array(
                                            'class' => 'form-control select2 rolesvalue',
                                            'data-validation' => 'required',
                                            'id' => 'roles'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('roles'); ?> </span>
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <!-- List group -->
                                <ul class="list-group">
                                    <?php
                                    $count = 0;
                                    foreach ($first_part_of_menu_details as $first_part_of_menu) {
                                        ?>
                                        <div class="col-sm-12 list-group-item">
                                            <div class="col-sm-10"><?php echo $first_part_of_menu['menu_name']; ?></div>
                                            <div  class="col-sm-2 material-switch pull-right">
                                                <input id="<?php echo $first_part_of_menu['id']; ?>" class="someSwitchOption001" type="checkbox"/>
                                                <label for="<?php echo $first_part_of_menu['id']; ?>" class="label-success"></label>
                                            </div>
                                        </div>
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
                                    foreach ($second_part_of_menu_details as $second_part_of_menu) {
                                        ?>
                                        <div class="col-sm-12 list-group-item">
                                            <div class="col-sm-10"><?php echo $second_part_of_menu['menu_name']; ?></div>
                                            <div class="col-sm-2 material-switch pull-right">
                                                <input id="<?php echo $second_part_of_menu['id']; ?>" class="someSwitchOption001"  type="checkbox"/>
                                                <label for="<?php echo $second_part_of_menu['id']; ?>" class="label-success"></label>
                                            </div>
                                        </div>
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
        $("body").on("change", "#roles", function (e) {

            var selected_seats = $("body .someSwitchOption001");
            $.each(selected_seats, function (key, value) {
                $(value).prop('checked', false);
            });

            var request = this.value;
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/PrevilageCntrler/allprevilages",
                dataType: 'json',
                data: {
                    request: request,
                },
                success: function (data) {
                    $.each(data, function (key, value) {
                        console.log("#" + value.menu);
                        $("#" + value.menu).prop('checked', true);
                    });

                }
            });
        });
        $("body").on("change", ".someSwitchOption001", function (e) {
            check = 0;
            role = $("#roles").val();
            if (role == "") {
                alertify.error("Select any role");
                return false;
            }

            menu = this.id;
            if ($(this).prop("checked") == true) {
                check = 1;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/PrevilageCntrler/saveprevilages",
                dataType: 'json',
                data: {
                    role: role,
                    menu: menu,
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
