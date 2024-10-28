<?php
$datatables = 1;
$datepicker = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
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
                            <h3 class="box-title">Resignation Process Calculator</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'showCalculator',
                            ));
                            ?>
                            <div class="form-group">
                                <?php
                                echo form_label('Talent', '', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2" id="talents">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                        'class' => 'form-control select2 selectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'talent',
                                    ));
                                    ?>
                                </div>
                                <?php
                                echo form_label('Date of Resignation', '', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">
                                    <?php
                                    if (1 != $isResigned) {
                                        ?>  <?php
                                        $fromDateText = array(
                                            'name' => 'dateOfResignation',
                                            'id' => 'dateOfResignation',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker',
                                            'value' => set_value('dateOfResignation'),
                                            'data-validation' => 'required',
                                        );
                                    } else {
                                        $fromDateText = array(
                                            'name' => 'dateOfResignation',
                                            'id' => 'dateOfResignation',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => $permenantlyResigned == 1 ? 'form-control' : 'form-control datepicker',
                                            'value' => date('d/m/Y', strtotime($dateOfResignation)),
                                            'data-validation' => 'required',
                                        );
                                    }
                                    echo form_input($fromDateText);
                                    ?>                                    
                                </div>
                                <?php
                                echo form_label('Last Month Salary', '', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2" id="talents">
                                    <?php
                                    echo form_dropdown('previousMonthSalary', $preMonthSalaryDropdown, $selectedPrevMonthSalary, array(
                                        'class' => 'form-control',
                                        'data-validation' => 'required',
                                        'id' => 'previousMonthSalary',
                                    ));
                                    ?>
                                </div>

                                <?php
                                echo form_label('Monthly Salary', '', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'salary',
                                        'id' => 'salary',
                                        'class' => 'form-control totalConsider',
                                        'value' => '',
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Current EL balance ', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'elLeft',
                                        'id' => 'elLeft',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => $elLeft,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('Excess Credited – EL', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'elActual',
                                        'id' => 'elActual',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => $elExcessCredicted,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('EL settlement', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'settlementEl',
                                        'id' => 'settlementEl',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control',
                                        'value' => $settlementEl,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">

                                <?php
                                echo form_label('Comp off', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'compOff',
                                        'id' => 'compOff',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => 0,
                                    );

                                    echo form_input($salaryText) . 'washed off-' . $compOffLeft;
                                    ?>
                                </div>
                                <?php
                                $shortageEnabled = $this->defaultValues->getValue(27);
                                if (1 == $shortageEnabled) {
                                    echo form_label('Lop By Hr. Shortage', '', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-2">  
                                        <?php
                                        $salaryText = array(
                                            'name' => 'lopByShortage',
                                            'id' => 'lopByShortage',
                                            'class' => 'form-control totalConsider',
                                            'value' => $lopByShortage,
                                        );

                                        echo form_input($salaryText);
                                        ?>
                                    </div>
                                    <?php
                                }
                                echo form_label('Lop By Absent', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'lopByAbsent',
                                        'id' => 'lopByAbsent',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => $lopByAbsent,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-8"></div>
                                <?php
                                echo form_label('Total Lop', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'talentLop',
                                        'id' => 'talentLop',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => $totalLop,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">

                                <?php
                                echo form_label('No. of days Pay', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'daysPay',
                                        'id' => 'daysPay',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => $noOfDaysPay,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('Daily Pay', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'dailyPay',
                                        'id' => 'dailyPay',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => '',
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('El Settlement Pay', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'elPay',
                                        'id' => 'elPay',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control totalConsider',
                                        'value' => '',
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">


                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Claim Pending', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'claimPending',
                                        'id' => 'claimPending',
                                        'class' => 'form-control totalConsider',
                                        'value' => $claim,
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('Incentive', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'incentive',
                                        'id' => 'incentive',
                                        'class' => 'form-control totalConsider',
                                        'value' => '',
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Note', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-6">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'note',
                                        'id' => 'note',
                                        'class' => 'form-control',
                                        'value' => '',
                                        'rows' => '5',
                                    );

                                    echo form_textarea($salaryText);
                                    ?>
                                </div>
                                <?php
                                echo form_label('Total Settlement', '', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  
                                    <?php
                                    $salaryText = array(
                                        'name' => 'totalSettlement',
                                        'id' => 'totalSettlement',
                                        'class' => 'form-control totalConsider',
                                        'value' => '',
                                    );

                                    echo form_input($salaryText);
                                    ?>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10 control-label">
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-xs-2">
                                        <br/>
                                        <br/>
                                        <center>
                                            <?php
                                            $submitButton = array(
                                                'type' => 'button',
                                                'content' => 'Print',
                                                'id' => 'print',
                                                'class' => 'btn btn-small btn-primary'
                                            );
                                            echo form_button($submitButton)
                                            ?>
                                        </center>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </div>
                            <?php
                            echo form_close();
                            ?>


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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) {
            dataLoading();
        });
    });
    $("body").on("change", ".selectOptions", function (e) {
        var talent = $(this).val();
        $('#dateOfResignation').val('');
        if (talent != '') {
            $("#showCalculator").submit();

        }
    });
    $("body").on("change", "#previousMonthSalary", function (e) {
        var talent = $(this).val();
        if (talent != '') {
            $("#showCalculator").submit();

        }
    });
    $("body").on("keyup", ".totalConsider", function (e) {
        if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        }
        var lopByAbsent = parseInt($("#lopByAbsent").val()) || 0;
        var lopByShortage = parseInt($("#lopByShortage").val()) || 0;
        var settlementEl = parseInt($("#settlementEl").val()) || 0;
        if (settlementEl < 0) {
            settlementEl = settlementEl * -1;
        } else {
            settlementEl = 0;
        }
        var lopSum = lopByAbsent + lopByShortage + settlementEl;

        $("#talentLop").val(lopSum);

        var salary = parseFloat($("#salary").val()) || 0;
        var talentLop = parseFloat($("#talentLop").val()) || 0;
        var elPay = 0;
        var settlementEl = parseFloat($("#settlementEl").val()) || 0;
        var dailyPay = parseFloat(salary / 30);

        $('#dailyPay').val(dailyPay.toFixed(2));

        var dailyPay = parseFloat($("#dailyPay").val()) || 0;
        if (settlementEl > 0) {
            elPay = parseFloat(dailyPay * settlementEl);
        }
        var talentPay = parseFloat(dailyPay * talentLop);

        $('#elPay').val(elPay.toFixed(2));
        var claimPending = parseFloat($('#claimPending').val()) || 0;
        var incentive = parseFloat($('#incentive').val()) || 0;
        var daysPay = parseFloat($('#daysPay').val()) || 0;
        var totalSettlement = (daysPay * dailyPay) + elPay + claimPending + incentive - talentPay;
//        if (totalSettlement < 0) {
//            totalSettlement = 0;
//        }
        $('#totalSettlement').val(totalSettlement.toFixed(2));
    });
    $("body").on("click", "#print", function (e) {
        var talent = $("#talent").val();
        var salary = $("#salary").val();
        var elLeft = $("#elLeft").val();
        var compOff = $("#compOff").val();
        var elActual = $("#elActual").val();
        var settlementEl = $("#settlementEl").val();
        var talentLop = $("#talentLop").val();
        var daysPay = $("#daysPay").val();
        var dailyPay = $("#dailyPay").val();
        var elPay = $("#elPay").val();
        var claimPending = $("#claimPending").val();
        var incentive = $("#incentive").val();
        var totalSettlement = $("#totalSettlement").val();
        var tillDate = $("#dateOfResignation").val();
        var note = $("#note").val();
        var lopByShortage = $("#lopByShortage").val();
        var lopByAbsent = $("#lopByAbsent").val();
        var base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        $.ajax({
            url: base_url + "index.php/resignationProcessCalculator/addData",
            type: "post", //send it through get method
            data: {
                talent: talent,
                salary: salary,
                elLeft: elLeft,
                compOff: compOff,
                elActual: elActual,
                settlementEl: settlementEl,
                talentLop: talentLop,
                daysPay: daysPay,
                elPay: elPay,
                dailyPay: dailyPay,
                claimPending: claimPending,
                incentive: incentive,
                totalSettlement: totalSettlement,
                tillDate: tillDate,
                note: note,
                lopByShortage: lopByShortage,
                lopByAbsent: lopByAbsent,
                '<?php echo $this->security->get_csrf_token_name(); ?>': cct
            },
            success: function (response) {
                window.open(base_url + "index.php/resignationProcessCalculator/printData");
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
    });
    function dataLoading() {
        var talent = $('#talent').val();
        if (talent != '') {
            var resignDate = $('#dateOfResignation').val();
            base_url = "<?php echo base_url() ?>";
            var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
            if (resignDate != '') {
                $.ajax({
                    url: base_url + "index.php/talents/checkResignIssues",
                    type: "get", //send it through get method
                    data: {
                        talentId: talent,
                        resignDate: resignDate,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == true) {
                            $("#showCalculator").submit();
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

        }
    }
</script>

