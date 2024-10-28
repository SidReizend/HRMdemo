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
        td:nth-of-type(3):before { content: "Lop By Absent"; } 
        td:nth-of-type(4):before { content: "Hours Required"; } 
        td:nth-of-type(5):before { content: "Hours Earned"; } 
        td:nth-of-type(6):before { content: "Hours Shortage"; } 
        td:nth-of-type(7):before { content: "Lop By Shortage"; } 
        td:nth-of-type(8):before { content: "Extra Total Hours"; } 
        td:nth-of-type(9):before { content: "Encashment-Extra"; } 
        td:nth-of-type(10):before { content: "Tot Present Days"; } 
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
                            <h3 class="box-title">All Talent Quarterly Report</h3>
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
                                    <div class="col-sm-2">
                                        <?php
                                        echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
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
                                    <div class="col-sm-2 ">
                                        <?php
                                        echo form_dropdown('quarter[]', $quarters, $selectedQuarter, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'quarter',
                                            'multiple' => '',
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
                                        <th>Lop By Absent</th>
                                        <th>Hours Required</th>
                                        <th>Hours Earned</th>
                                        <th>Hours Shortage</th>
                                        <th>Lop By Monthly Shortage</th>
                                        <th>Extra Total Hours</th>
                                        <th>Encashment for Extra</th>
                                        <th>Total Present Days</th>
                                        <th>Total Lop</th>
                                        <th>Total Encashment</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if ($quarterReport) {
                                        $serialNumber = 1;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $serialNumber++;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['lop_by_process'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['hrs_required'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['hrs_earned'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['hrs_shortage'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['lop_by_shortage'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['total_extra_hours'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['encashment_for_extra'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['total_present_days'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['total_lop'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $quarterReport['total_encashment'];
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
    $('#attendenceTable').DataTable();
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });
    $("body").on("change", ".selectOptions", function (e) {
        talent = $("#talent").val();
        year = $("#year").val();
        quarter = 0;
        $('#quarter :selected').each(function (i, sel) {
            quarter=1;

        });
        if (talent != '' && year != '' && quarter != '') {
            $("#showReport").submit();

        }
    });
    function deleteRow()
    {
        alertify.confirm("Do you want to Continue", function (e) {
            if (e) {
                alertify.success("Operation done successfully");
                return true;
            } else {
                return false;
            }
        });

    }
</script>

