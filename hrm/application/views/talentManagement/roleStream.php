<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Role Stream"; } 
        td:nth-of-type(3):before { content: "Show In Reports"; } 
        td:nth-of-type(4):before { content: "Monthly Process"; } 
    }
</style>
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
                            <h3 class="box-title">Role Streams</h3>
                        </div>
                        <div class="box-body">
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Role Streams</th>
                                        <th>Show In Reports</th>
                                        <th>Monthly Process</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($roelStreamDetails as $roelStream) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roelStream['role_stream'];
                                                ?>
                                            </td>
                                            <td style="height:20px;">
                                                <span class="material-switch pull-right">

                                                    <input id="<?php echo $roelStream['id']; ?>a" value="<?php echo $roelStream['id']; ?>" class="someSwitchOption001" 
                                                    <?php
                                                    echo $roelStream['show_in_reports'] == 1 ? 'checked' : '';
                                                    ?>
                                                           type="checkbox"/>
                                                    <label for="<?php echo $roelStream['id']; ?>a" class="label-success"></label>
                                                </span>
                                            </td>
                                            <td style="height:20px;">
                                                <span class="material-switch pull-right">
                                                    <input id="<?php echo $roelStream['id']; ?>b" value="<?php echo $roelStream['id']; ?>" class="someSwitchOption002" 
                                                    <?php
                                                    echo $roelStream['monthly_process'] == 1 ? 'checked' : '';
                                                    ?>
                                                           type="checkbox"/>
                                                    <label for="<?php echo $roelStream['id']; ?>b" class="label-success"></label>
                                                </span>
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
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>


<script>
    $(document).ready(function () {
        $("body").on("change", ".someSwitchOption001", function (e) {
            check = 0;
            menu = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            if ($(this).prop("checked") == true) {
                check = 1;
            }
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/roleStream/saveShowReports",
                dataType: 'json',
                data: {
                    menu: menu,
                    check: check,
                },
                success: function (data) {
                    if (data == 1) {
                        alertify.success("Success");
                    } else {
                        alertify.error("Failed");
                    }
                }
            });
        });
        $("body").on("change", ".someSwitchOption002", function (e) {
            check = 0;
            menu = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            if ($(this).prop("checked") == true) {
                check = 1;
            }
            $.ajax({
                type: 'GET',
                url: base_url + "index.php/roleStream/saveMonthlyProcess",
                dataType: 'json',
                data: {
                    menu: menu,
                    check: check,
                },
                success: function (data) {
                    if (data == 1) {
                        alertify.success("Success");
                    } else {
                        alertify.error("Failed");
                    }
                }
            });
        });

    });
</script>