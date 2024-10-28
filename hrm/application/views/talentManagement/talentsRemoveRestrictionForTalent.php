<?php
$datatables = 1;

include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent Code"; } 
        td:nth-of-type(3):before { content: "Name"; } 
        td:nth-of-type(4):before { content: "Role"; } 
        td:nth-of-type(5):before { content: "Mobile"; } 
        td:nth-of-type(6):before { content: "Email"; } 
        td:nth-of-type(7):before { content: "reporting_manager"; } 
        td:nth-of-type(8):before { content: "Action"; } 
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Remove talent restrictions</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabbable">

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="formcontrols">
                                                <div class="box-body">
                                                    <table id="table_responsive" class="table table-bordered table-hover table_responsive">
                                                        <thead>
                                                            <tr>
                                                                <th>Sl No.</th>
                                                                <th>Talent Code</th>
                                                                <th>Name</th>
                                                                <th>Role</th>
                                                                <th>Time Sheet Restrictions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $serialNumber = 0;
                                                            foreach ($talentDetails as $talent) {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        echo ++$serialNumber;
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        echo $talent['talent_code'];
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        echo $talent['name'];
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        echo $talent['role_name'];
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="material-switch">
                                                                            <br/>
                                                                            Enabled <input value="<?php echo $talent['id']; ?>" id="<?php echo 'timesheet' . $talent['id']; ?>" class="someSwitchOption001"
                                                                            <?php
                                                                            if ($talent['timesheet_restriction'] == 1) {
                                                                                echo 'checked';
                                                                            }
                                                                            ?>
                                                                                           type="checkbox" />
                                                                            <label for="<?php echo 'timesheet' . $talent['id']; ?>" class="label-danger"></label> Disabled
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box -->

            </div>
            <!--/.col (left) -->
            <!-- Content Header (Page header) -->


    </div>
</section>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>

    $("body").on("click", ".someSwitchOption001", function (e) {
        var talent = $(this).val();
        var checked = 0;
        if ($(this).prop("checked") == true) {
            checked = 1;
        }
        changeTimesheetRestrictions(talent, checked)
    });
    function changeTimesheetRestrictions(talent, checked) {
        $("#overlay1").show();
        $.ajax({
            url: base_url + "index.php/talents/removeTimesheetRestriction",
            type: "post", //send it through get method
            dataType: 'json',
            data: {
                talent: talent,
                checked: checked,
            },
            success: function (response) {
                alertify.success("Timesheet restriction changed");
                $("#overlay1").hide();
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }
    $('.table_responsive').DataTable();
</script>

