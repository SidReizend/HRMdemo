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
        td:nth-of-type(3):before { content: "Tot Present Days"; } 
        td:nth-of-type(4):before { content: "Hours Required"; } 
        td:nth-of-type(5):before { content: "Hours Earned"; } 
        td:nth-of-type(6):before { content: "Hours Shortage"; } 
        td:nth-of-type(9):before { content: "Extra Total Hours"; } 
        td:nth-of-type(10):before { content: "Encashment-Extra"; } 
        td:nth-of-type(11):before { content: "Total Lop"; } 
        td:nth-of-type(12):before { content: "Total Encashment"; } 
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
                            <h3 class="box-title">All Talent Monthly Report</h3>
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
                                    </div>
                                    <div class="col-sm-2    ">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'month',
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
                                        <th>Total Present Days</th>
                                        <th>Hours Required</th>
                                        <th>Hours Earned</th>
                                        <th>Hours Shortage</th>
                                        <th>Leave before & after holiday</th>
                                        <th>Lop By Monthly Shortage</th>
                                        <th>Lop By Absent</th>
                                        <th>Total Lop</th>
                                        <th>Total Encashment</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $serialNumber = 1;
                                    foreach ($monthYearReport as $monthYearReportSpecific) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $serialNumber++;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['total_present_days'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['hrs_required'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['hrs_earned'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['hrs_shortage'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (1 == $monthYearReportSpecific['leave_before_after_holiday']) {
                                                    echo 'Yes';
                                                } else {
                                                    echo 'No';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (0 == $monthYearReportSpecific['is_resigned'])
                                                    echo $monthYearReportSpecific['lop_by_shortage'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (0 == $monthYearReportSpecific['is_resigned'])
                                                    echo $monthYearReportSpecific['lop_by_process'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (0 == $monthYearReportSpecific['is_resigned'])
                                                    echo $monthYearReportSpecific['lop_by_shortage'] + $monthYearReportSpecific['lop_by_process'] + $monthYearReportSpecific['lop_due_to_lop'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (0 == $monthYearReportSpecific['is_resigned'])
                                                    echo $monthYearReportSpecific['encashment_for_extra'] + $monthYearReportSpecific['encashment'];
                                                else
                                                    echo 'Resigned';
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div>
                                <center>
                                    <button type="button" id="excel" class="btn btn-small btn-primary">
                                        Excel
                                    </button>
                                </center>
                            </div>
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
    });
    $("body").on("change", ".selectOptions", function (e) {
        month = $("#month").val();
        year = $("#year").val();

        if (month != '' && year != '') {
            $("#showReport").submit();

        }
    });
    $("body").on("click", "#excel", function (e) {
        var month = $("#month").val();
        var year = $("#year").val();
        if (month != '' && year != '') {
            window.open('allTalentsMonthlyReport/excelDownload/' + month + '/' + year);
        }
    });
</script>

