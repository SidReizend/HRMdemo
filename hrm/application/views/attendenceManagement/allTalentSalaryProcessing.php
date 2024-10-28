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
        td:nth-of-type(3):before { content: "Total Days"; } 
        td:nth-of-type(4):before { content: "Total Lop"; } 
        td:nth-of-type(5):before { content: "Total Encashment"; } 
        td:nth-of-type(6):before { content: "Total Salary"; } 
        td:nth-of-type(7):before { content: "Total Deductions"; } 
        td:nth-of-type(8):before { content: "Actual Salary"; } 
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
                            <h3 class="box-title">Salary Calculation</h3>
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
                            <table id="table_responsive" class="table table-bordered table-hover" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th>SL. No.</th>
                                        <th>Talent</th>
                                        <th>Total Days</th>
                                        <th>LOP Due To Shortage</th>
                                        <th>Total LOP</th>
                                        <th>Total Encashment</th>
                                        <th>Total Salary</th>
                                        <th>Total Addition</th>
                                        <th>Total Claims</th>
                                        <th>Total Deductions</th>
                                        <th>Actual Salary</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $serialNumber = 1;
                                    foreach ($monthYearReport as $monthYearReportSpecific) {
                                        $totalLop = $monthYearReportSpecific['lop_by_shortage'] + $monthYearReportSpecific['lop_by_process'] + $monthYearReportSpecific['lop_due_to_lop'];
                                        $totalEncashment = $monthYearReportSpecific['encashment_for_extra'] + $monthYearReportSpecific['encashment'];
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
                                            <td id="totalDays<?php echo $monthYearReportSpecific['id']; ?>" data-val="<?php echo $monthYearReportSpecific['total_days']; ?>">
                                                <?php
                                                echo $monthYearReportSpecific['total_days'];
                                                ?>
                                            </td >
                                            <td>
                                                <?php
                                                echo $monthYearReportSpecific['lop_by_shortage'];
                                                ?>
                                            </td>
                                            <td id="totalLop<?php echo $monthYearReportSpecific['id']; ?>" data-val="<?php echo $totalLop ?>">
                                                <?php
                                                echo $totalLop;
                                                ;
                                                ?>
                                            </td>
                                            <td id="totalEncashment<?php echo $monthYearReportSpecific['id']; ?>" data-val="<?php echo $totalEncashment ?>">
                                                <?php
                                                echo $totalEncashment;
                                                ?>
                                            </td>
                                            <td>
                                                <input style="width: 100px;" type="number" value="<?php echo $monthYearReportSpecific['total_salary']; ?>" 
                                                       data-id="<?php echo $monthYearReportSpecific['id']; ?>" class="salaryCalculation"
                                                       id="totalSalary<?php echo $monthYearReportSpecific['id']; ?>"
                                                       data-validation="required">
                                            </td>
                                            <td>
                                                <input style="width: 100px;" type="number" value="<?php echo $monthYearReportSpecific['total_additions']; ?>" 
                                                       data-id="<?php echo $monthYearReportSpecific['id']; ?>" class="salaryCalculation"
                                                       id="totalAddition<?php echo $monthYearReportSpecific['id']; ?>"
                                                       data-validation="required">
                                            </td>
                                            <td>
                                                <input style="width: 100px;" type="number" value="<?php echo $monthYearReportSpecific['total_claims']; ?>" 
                                                       data-id="<?php echo $monthYearReportSpecific['id']; ?>" class="salaryCalculation"
                                                       id="totalClaims<?php echo $monthYearReportSpecific['id']; ?>"
                                                       data-validation="required">
                                            </td>
                                            <td>
                                                <input style="width: 100px;" type="number" value="<?php echo $monthYearReportSpecific['total_deductions']; ?>" 
                                                       data-id="<?php echo $monthYearReportSpecific['id']; ?>" 
                                                       class="salaryCalculation"  id="totalDeduction<?php echo $monthYearReportSpecific['id']; ?>"
                                                       data-validation="required">
                                            </td>
                                            <td id="actualSalary<?php echo $monthYearReportSpecific['id']; ?>">
                                                <?php
                                                echo $monthYearReportSpecific['actual_salary'];
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
                                    <button type="button" id="excel" class="btn btn-small btn-default">
                                        Download Excel
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
    $("body").on("keyup change", ".salaryCalculation", function (e) {
        var id = $(this).attr('data-id');
        var totalSalary = $('#totalSalary' + id).val();
        var totalDays = $('#totalDays' + id).attr('data-val');
        var totalLop = $('#totalLop' + id).attr('data-val');
        var totalEncashment = $('#totalEncashment' + id).attr('data-val');
        var totalDeduction = $('#totalDeduction' + id).val();
        var totalAddition = $('#totalAddition' + id).val() ? parseFloat($('#totalAddition' + id).val()) : 0;
        var totalClaims = $('#totalClaims' + id).val() ? parseFloat($('#totalClaims' + id).val()) : 0;
        var dayPay = (totalSalary / totalDays);
        var actualSalary = Math.round(totalSalary - (dayPay * totalLop) + (dayPay * totalEncashment) - totalDeduction + totalAddition + totalClaims);
        $('#actualSalary' + id).text(actualSalary);
        if (event.which === 13) {
            if (actualSalary < 0) {
                alertify.error("Invalid Salary");
                return;
            }
            saveAmount(id, totalSalary, totalDeduction, totalAddition, totalClaims);
        }
    });

    function saveAmount(id, totalSalary, totalDeduction, totalAddition, totalClaims) {
        $.ajax({
            url: base_url + "index.php/allTalentsMonthlyReport/saveSalaryAmount",
            type: "post", //send it through get method
            dataType: 'json',
            data: {
                id: id,
                totalSalary: totalSalary,
                totalDeduction: totalDeduction,
                totalAddition: totalAddition,
                totalClaims: totalClaims,
            },
            success: function (response) {
                if (response.status === 'success') {
                    alertify.success("Saved successfully")
                }
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }

    $("body").on("click", "#excel", function (e) {
        var month = $("#month").val();
        var year = $("#year").val();
        if (month != '' && year != '') {
            window.open('excelSalaryDownload/' + month + '/' + year);
        }
    });
</script>

