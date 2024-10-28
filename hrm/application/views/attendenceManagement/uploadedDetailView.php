<?php
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Detailed View</h3>
                        </div>
                        <table id="attendanceTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Talent Code</th>
                                    <th>Talent</th>
                                    <th>In Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                foreach ($attendanceDetalils as $attendanceDetalilsSpecific) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $attendanceDetalilsSpecific['talent_code'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $attendanceDetalilsSpecific['name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $attendanceDetalilsSpecific['actual_hours_spend'];
                                            ?>
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
        </section>
    </div>
</div>
<!-- /.container -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
//Date picker
    $(document).ready(function () {
        $('#attendanceTable').DataTable();
    });
</script>

