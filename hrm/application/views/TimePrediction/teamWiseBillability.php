<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Date"; } 
        .notitle>td:nth-of-type(3):before { content: "Billable"; } 
        .notitle>td:nth-of-type(4):before { content: "Billable Productivity"; } 
        .notitle>td:nth-of-type(5):before { content: "Non Billable"; } 
        .notitle>td:nth-of-type(6):before { content: "Non Billable Productivity"; } 
        .notitle>td:nth-of-type(7):before { content: "Total"; } 
        .notitle>td:nth-of-type(8):before { content: "Productivity"; } 
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
                            <h3 class="box-title">Talent Date Wise Billability Report</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'searchForm'
                            ));
                            ?>
                            <div class="form-group">
                                <div class="col-sm-2">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $fromDate,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                </div>
                                <div class="col-sm-2">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $toDate,
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                </div>
                                <button type="button" class="btn btn-primary" id="calculate">
                                    Calculate
                                </button>
                            </div>

                            <div class="material-switch pull-right">
                                Sign On <input name="resigned" value="1" id="resigned" class="someSwitchOption001 selectOptions"
                                <?php
                                if ($isResigned == 1) {
                                    echo 'checked';
                                }
                                ?>
                                               type="checkbox" />
                                <label for="resigned" class="label-success"></label> Resigned
                            </div>

                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent</th>
                                        <th>Billable Time</th>
                                        <th>Billable Productivity</th>
                                        <th>Non Billable Time</th>
                                        <th>Non Billable Productivity</th>
                                        <th>Total Time</th>
                                        <th>Total Productivity</th>
                                        <th>Total Working Days</th>
                                        <th>Percentage of Billability</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    ;
                                    foreach ($teamBillabilityBetweenDates as $teamBillabilityBetweenDatesSpecific) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_billable_time'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_billable_productivity'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_non_billable_time'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_non_billable_productivity'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_total_time'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['talent_total_productivity'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['number_of_working_days'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $teamBillabilityBetweenDatesSpecific['percentage_billable_productivity'];
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
<?php
include_once APPPATH . 'views/footer.php';
?>


<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $('#odTypeTable').DataTable();
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });
    });
    $("body").on("change", ".selectOptions", function (e) {
        if ($('#resigned').prop("checked") == true) {
            $('#resigned').val('1');
        } else {
            $('#resigned').val('0');
        }
        $('#talent').val('');
        $("#searchForm").submit();
    });
    $("body").on("click", "#calculate", function (e) {
        if (($('#fromDate').val() != '') && ($('#toDate').val() != '')) {
            $("#searchForm").submit();
        }
    });
    $("body").on("click", "#excel", function (e) {
        if ($('#resigned').prop("checked") == true) {
            var resigned = 1;
        } else {
            var resigned = 0;
        }
        var fromDate = $("#fromDate").val();
        var toDate = $("#toDate").val();
        window.open('teamWiseBillabilityExcel?resigned=' + resigned + '&fromDate=' + fromDate + '&toDate=' + toDate);
    });
</script>