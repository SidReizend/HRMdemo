<?php
$select2 = 1;
$form_validator = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>

<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent"; } 
        td:nth-of-type(3):before { content: "Year"; } 
        td:nth-of-type(4):before { content: "Month"; } 
        td:nth-of-type(5):before { content: "Note"; } 
        td:nth-of-type(6):before { content: "Uploaded By"; } 
        td:nth-of-type(7):before { content: "Uploaded On"; } 
        td:nth-of-type(9):before { content: "File"; } 
        td:nth-of-type(10):before { content: "Action"; } 
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal',
                'id' => 'showSalarySlip'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Salary Slips</h3>
                        </div>
                        <?php
                        echo anchor('salarySlip/addNewSalarySlip', 'Add New Salary Slip', array(
                            'class' => 'btn btn-small btn-primary'
                        ));
                        ?>
                        <br/>
                        <br/>
                        <div class="col-sm-2">
                            <?php
                            $yearDropDown = $years;
                            echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                'class' => 'form-control select2 selectOptions talentChoose',
                                'data-validation' => 'required',
                                'id' => 'year',
                            ));
                            ?>
                            <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                        </div>
                        <div class="col-sm-2    ">
                            <?php
                            $monthDropDown = $months;
                            echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                'class' => 'form-control select2 selectOptions talentChoose',
                                'data-validation' => 'required',
                                'id' => 'month',
                            ));
                            ?>
                        </div>
                        <div class="material-switch pull-right">
                            <br/>
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
                        <br/>
                        <table id="table_responsive" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Talent</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Note</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded On</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                foreach ($salarySlips as $salarySlip) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['year'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['month'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['note'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['username'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('d/m/Y H:i:is', strtotime($salarySlip['date_of_upload']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo anchor(base_url() . 'index.php/docUrl/docUrlGenerator/' . $salarySlip['file_name'], $salarySlip['file_name'], 'target="_blank"');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo anchor('salarySlip/deleteSalarySlip/slipId/' . $salarySlip['id'], '<i class="fa fa-fw fa-times"></i>', array(
                                                'class' => "cancel_events"
                                            ));
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        echo form_close();
                        ?>
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $("#table_responsive").dataTable();
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
        $("#save").on('click', function (e) {
            var talent = $('#talent').val();
            var month = $('#month').val();
            var year = $('#year').val();
            if (talent != '' && month != '' && year != '') {
                e.preventDefault();
                base_url = "<?php echo base_url() ?>";
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/salarySlip/checkAlreadyExist",
                    type: "get", //send it through get method
                    data: {
                        talent: talent,
                        month: month,
                        year: year,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == true) {
                            $('#addSlip').submit();
                        } else {
                            alertify.alert(response);
                        }
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                }).complete(function () {
                    var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                    var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                    $('[name="' + csrf_name + '"]').val(cct);
                });
            }
        });
        $("body").on("change", ".selectOptions", function (e) {
            $("#showSalarySlip").submit();
        });
    });
    $.validate({
        lang: 'en'
    });
</script>

