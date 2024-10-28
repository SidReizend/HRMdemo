<?php
$datatables = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent"; } 
        td:nth-of-type(3):before { content: "El-Encashment"; } 
        td:nth-of-type(4):before { content: "Comp-Off Encashment"; } 
        td:nth-of-type(5):before { content: "Total Encashment"; } 
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
                            <h3 class="box-title">Year Report</h3>
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
                                    <div class="col-sm-2    ">
                                        <?php
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>SL. No.</th>
                                        <th>Talent</th>
                                        <th>El-Encashment</th>
                                        <th>Comp-Off Encashment</th>
                                        <th>Total Encashment</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $serialNumber = 1;
                                    foreach ($YearReport as $YearReportSpecific) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $serialNumber++;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $YearReportSpecific['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $YearReportSpecific['el_encashment'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $YearReportSpecific['compoff_encashment'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $YearReportSpecific['compoff_encashment']+$YearReportSpecific['el_encashment'];
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
            </div>
        </section>
    </div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $('#reportTable').DataTable();
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });
    $("body").on("change", ".selectOptions", function (e) {
        month = $("#month").val();

        if (year != '') {
            $("#showReport").submit();

        }
    });
</script>

