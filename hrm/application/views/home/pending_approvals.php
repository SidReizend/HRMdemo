<?php
$wysihtml5 = 1;

include_once APPPATH . 'views/header.php';
?>

<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container">
        <section class="content">

            <div class="row">

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending Leave Approvals</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="overflow: scroll">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reporting Officer</th>
                                        <th>Requested By</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Request</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $slno = 1;
                                    foreach ($pending_leave_approvals as $pending_la) {
                                        ?>
                                        <tr>
                                            <td><?php echo $slno; ?></td>
                                            <td><?php echo $pending_la->tr_first_name . " " . $pending_la->tr_middle_name . " " . $pending_la->tr_last_name; ?></td>
                                            <td><?php echo $pending_la->first_name . " " . $pending_la->middle_name . " " . $pending_la->last_name; ?></td>
                                            <td><?php echo $pending_la->from_date_f; ?></td>
                                            <td><?php echo $pending_la->to_date_f; ?></td>
                                            <td><?php echo $pending_la->request; ?></td>
                                        </tr>

                                        <?php
                                        $slno++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending Compoff Approvals</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="overflow: scroll">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reporting Officer</th>
                                        <th>Talent</th>
                                        <th>Attendance Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $slno = 1;
                                    foreach ($pending_compoff_approvals as $pending_comp) {
                                        ?>
                                        <tr>
                                            <td><?php echo $slno; ?></td>
                                            <td><?php echo $pending_comp->tr_name; ?></td>
                                            <td><?php echo $pending_comp->name; ?></td>
                                            <td><?php echo $pending_comp->attendance_date_f; ?></td>
                                        </tr>

                                        <?php
                                        $slno++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Pending OD Approvals</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="overflow: scroll">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reporting Officer</th>
                                        <th>Requested By</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Request</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $slno = 1;
                                    foreach ($pending_od_approvals as $pending_od) {
                                        ?>
                                        <tr>
                                            <td><?php echo $slno; ?></td>
                                            <td><?php echo $pending_od->tr_first_name . " " . $pending_od->tr_middle_name . " " . $pending_od->tr_last_name; ?></td>
                                            <td><?php echo $pending_od->first_name . " " . $pending_od->middle_name . " " . $pending_od->last_name; ?></td>
                                            <td><?php echo $pending_od->from_date_f; ?></td>
                                            <td><?php echo $pending_od->to_date_f; ?></td>
                                            <td><?php echo $pending_od->reason; ?></td>
                                        </tr>

                                        <?php
                                        $slno++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Encashment Approvals</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="overflow: scroll">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Attendance Date</th>
                                        <th>Reporting Officer</th>
                                        <th>Talent</th>
                                        <th>Productive Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $slno = 1;
                                    foreach ($pending_encashment_approvals as $pending_od) {
                                        ?>
                                        <tr>
                                            <td><?php echo $slno; ?></td>
                                            <td><?php echo $pending_od->attendance_date_f; ?></td>
                                            <td><?php echo $pending_od->tr_name; ?></td>
                                            <td><?php echo $pending_od->name; ?></td>
                                            <td><?php echo $pending_od->productive_hrs; ?></td>
                                
                                        </tr>

                                        <?php
                                        $slno++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
            </div>
    </div>
</section>
<!-- /.content -->
</div>
<!-- /.container -->
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>