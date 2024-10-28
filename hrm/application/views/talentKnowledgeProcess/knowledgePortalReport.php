<?php
$datatables = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Heading"; }  
        .notitle>td:nth-of-type(3):before { content: "Created On"; } 
        .notitle>td:nth-of-type(6):before { content: "Approval Status"; } 
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
                            <h3 class="box-title">Knowledge Portal Contribution Report</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'showReport',
                            ));
                            ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2" id="talents">
                                        <?php
                                        echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2" id="talents">
                                        <?php
                                        echo form_dropdown('status', $statusDropdown, $selectedStatus, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">  <?php
                                        $fromDateText = array(
                                            'name' => 'startDate',
                                            'id' => 'startDate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => date('d/m/Y', strtotime($selectedStartDate)),
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">  <?php
                                        $fromDateText = array(
                                            'name' => 'endDate',
                                            'id' => 'endDate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => date('d/m/Y', strtotime($selectedEndDate)),
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent</th>
                                        <th>Heading</th>
                                        <th>Created ON</th>
                                        <th>Approval Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 1;
                                    $totalApproved = 0;
                                    $totalCreated = 0;
                                    $totalPending = 0;
                                    foreach ($report as $reportSpecific) {
                                        ?>
                                    <tr class="notitle">
                                            <td>
                                                <?php
                                                echo $serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $reportSpecific['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $reportSpecific['heading'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/y H:i', strtotime($reportSpecific['created_on']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $totalCreated++;
                                                if ($reportSpecific['verified'] == 1) {
                                                    $totalApproved++;
                                                    echo 'Approved';
                                                } else {
                                                    $totalPending++;
                                                    echo 'Pending';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $serialNumber++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <table class="table" style="background-color: #D9D6D6;">
                                <tbody>
                                    <tr>
                                        <td style="width: 60%">Total Contribution</td>
                                        <td><span class="badge bg-green"><?php echo $totalCreated; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%">Approved</td>
                                        <td><span class="badge bg-green"><?php echo $totalApproved; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%">Pending</td>
                                        <td><span class="badge bg-green"><?php echo $totalPending; ?></span></td>
                                    </tr>
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
    $('#attendenceTable').DataTable();
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) {
            $("#showReport").submit();
        });
    });
    $("body").on("change", ".selectOptions", function (e) {
        $("#showReport").submit();
    });
</script>

