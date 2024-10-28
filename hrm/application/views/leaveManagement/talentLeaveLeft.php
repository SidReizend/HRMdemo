<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent Code"; } 
        td:nth-of-type(3):before { content: "Talent"; } 
        td:nth-of-type(4):before { content: "Comp-off"; } 
        td:nth-of-type(5):before { content: "EL"; } 
        td:nth-of-type(6):before { content: "Emergency"; } 
        td:nth-of-type(7):before { content: "Paternity"; } 
        td:nth-of-type(8):before { content: "Maternity"; } 
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
                            <h3 class="box-title">Talent's Leave Left</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            $paternityEnabled = $this->defaultValues->getValue(32);
                            $emergencyEnabled = $this->defaultValues->getValue(33);
                            ?>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent Code</th>
                                        <th>Talent</th>
                                        <th>Comp-off</th>
                                        <th>EL</th>
                                        <th>Comp-off Planned</th>
                                        <th>EL Planned</th>
                                        <?php
                                        if ($emergencyEnabled == 1) {
                                            ?>
                                            <th>Emergency</th>
                                            <?php
                                        }
                                        if ($paternityEnabled == 1) {
                                            ?>
                                            <th>Paternity</th>
                                            <th>Maternity</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    $halfDayMechanism = $this->defaultValues->getValue(34);
                                    foreach ($talentLeaveLeft as $talentLeave) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $talentLeave['talent_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $talentLeave['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo round($talentLeave['comp']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($halfDayMechanism == 1) {
                                                    echo $talentLeave['el'];
                                                } else {
                                                    echo round($talentLeave['el']);
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $talentLeave['comp_planned'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo round($talentLeave['el_planned']);
                                                ?>
                                            </td>
                                            <?php
                                            if ($emergencyEnabled == 1) {
                                                ?>
                                                <td>
                                                    <?php
                                                    echo round($talentLeave['emergency']);
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                            if ($paternityEnabled == 1) {
                                                ?>
                                                <td>
                                                    <?php
                                                    echo round($talentLeave['paternity']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo round($talentLeave['maternity']);
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                            ?>
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
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $('#leaveLeftTable').DataTable();
        $("body").on("click", ".cancel_events", function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            alertify.confirm("Are you sure", function (e) {
                if (e) {
                    alertify.success("You've clicked OK");
                    location.href = link;

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
    });
</script>

