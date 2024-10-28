<?php
$form_validator2 = 1;
$datepicker = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('', array(
                'class' => 'form-horizontal',
                'id' => 'editTransactionFrom',
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Transaction Edit</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Company', 'company', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('company', $companyDropdown, $selectedCompany, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Project', 'project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('project', $projectDropdown, $selectedProject, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                        'id' => 'project',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('project'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group" id="subProjectDiv" >
                                <?php
                                if (!empty($subProjects)) {
                                    ?>
                                    <?php
                                    echo form_label('Sub Projects', 'subProject', array(
                                        'class' => 'col-sm-2 control-label',
                                    ));
                                    ?>
                                    <div class="col-sm-10">

                                        <?php
                                        echo form_dropdown('subProject', $subProjectDropdown, $selectedSubProject, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'id' => 'subProjects',
                                        ));
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Credit Amount', 'credit_amount', array(
                                    'class' => 'col-sm-2 control-label',
                                    'required' => 'required',
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $amountText = array(
                                        'name' => 'credit_amount',
                                        'id' => 'credit_amount',
                                        'class' => 'form-control numeric',
                                        'value' => $credit_amount,
                                    );

                                    echo form_input($amountText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('credit_amount'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Debit Amount', 'debit_amount', array(
                                    'class' => 'col-sm-2 control-label',
                                    'required' => 'required',
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $amountText = array(
                                        'name' => 'debit_amount',
                                        'id' => 'debit_amount',
                                        'class' => 'form-control numeric',
                                        'value' => $debit_amount,
                                    );

                                    echo form_input($amountText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('debit_amount'); ?> </span>
                                </div>
                            </div>
                            <?php
                            if (!empty($claimsMapped)) {
                                $showClaimTable = "display:block;";
                            } else {
                                $showClaimTable = "display:none;";
                            }
                            ?>
                            <div class="form-group" id="claimDiv" style="<?php echo $showClaimTable; ?>">
                                <?php
                                echo form_label('Claim', 'claim', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('claim', $claimDropdown, '', array(
                                        'class' => ' select2 ',
                                        'id' => 'claim',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('project'); ?> </span>
                                </div>
                                <?php
                                echo form_label('Payment Option', 'paymentOption', array(
                                    'class' => 'col-sm-2 control-label',
                                    'id' => 'option',
                                ));
                                ?>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('paymentOption', $optionDropdown, '', array(
                                        'class' => 'form-control',
                                        'id' => 'paymentOption',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('project'); ?> </span>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $amountText = array(
                                        'name' => 'transactionAmount',
                                        'id' => 'transactionAmount',
                                        'placeholder' => 'Amount',
                                        'readonly' => 'readonly',
                                        'class' => 'form-control numeric',
                                        'value' => '',
                                    );

                                    echo form_input($amountText);
                                    ?>
                                    <input type="hidden" name="slNo" id="slNo" value="<?php echo 1 + count($claimsMapped); ?>">
                                    <span class="text-danger"> <?php echo form_error('transactionAmount'); ?> </span>
                                </div>
                                <div class="col-sm-2">
                                    <input type="button" value="add" class="btn btn" id="addNewClaim">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <table id="example" class="dis table table-bordered table-hover">
                                        <tr>
                                            <th>Sl No</th>
                                            <th>Claim</th>
                                            <th>Payment Option</th>
                                            <th>Amount</th>
                                            <th>Close</th>
                                        </tr>
                                        <?php
                                        $slno = 1;
                                        $mappedAmount = 0;
                                        foreach ($claimsMapped as $mapped) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="claimId<?php echo $slno; ?>" value="<?php echo $mapped['claim']; ?>">
                                                    <input type="hidden" name="option<?php echo $slno; ?>" value="<?php echo $mapped['option']; ?>">
                                                    <input type="hidden" name="claimAmount<?php echo $slno; ?>" value="<?php echo $mapped['transaction_amount']; ?>">
                                                    <?php
                                                    echo $slno++;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $mapped['claim_id'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $mapped['option'] == 1 ? 'Full' : 'Partial';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $mapped['transaction_amount'];
                                                    $mappedAmount+= $mapped['transaction_amount'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <input type="button" value="X" id="<?php echo $mapped['id']; ?>" style="width:30px;border:none;height:30px;" class="closeButton" >
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                            <input type="hidden" value="<?php echo $mappedAmount; ?>">
                            <div class="form-group">
                                <?php
                                echo form_label('Date of transaction', 'dateOfApproval', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $dateOfApprovalText = array(
                                        'name' => 'dateOfApproval',
                                        'id' => 'dateOfApproval',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $dateOfApproval,
                                        'required' => 'required',
                                    );

                                    echo form_input($dateOfApprovalText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('dateOfApproval'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Note', 'note', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $noteText = array(
                                        'name' => 'note',
                                        'id' => 'note',
                                        'class' => 'form-control',
                                        'value' => $note,
                                        'rows' => '2',
                                    );

                                    echo form_textarea($noteText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('note'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Remarks', 'remarks', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $noteText = array(
                                        'name' => 'remarks',
                                        'id' => 'remarks',
                                        'class' => 'form-control',
                                        'value' => $remarks,
                                        'rows' => '2',
                                    );

                                    echo form_textarea($noteText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('remarks'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Save',
                                        'id' => 'save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                    <a href="javascript:window.history.go(-1);">
                                        <?php
                                        $submitButton = array(
                                            'type' => 'button',
                                            'content' => 'Cancel',
                                            'id' => 'cancel',
                                            'class' => 'btn btn-small btn-primary'
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </a>
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
        $(".form-horizontal").validate();
        claimMapLoad();
    });
    $("body").on("keyup", "#debit_amount", function (event) {
        if (this.value != '' || (this.value != 0)) {
            $("#credit_amount").val('');
            $("#credit_amount").attr('readonly', true);
        } else {
            $("#credit_amount").attr('readonly', false);
        }
    });
    $("body").on("keyup", "#credit_amount", function (event) {
        if (this.value != '' || (this.value != 0)) {
            $("#debit_amount").val('');
            $("#debit_amount").attr('readonly', true);
        } else {
            $("#debit_amount").attr('readonly', false);
        }
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy"
    }).on('change', function () {
        $(this).valid();   // triggers the validation test
    });
    $("body").on("keyup", ".numeric", function (event) {
        if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        }
    });
    $("body").on("click", ".closeButton", function (event) {
        $(this).closest("tr").remove();
    });
    $("body").on("change", "#project", function (event) {
        var project = $(this).val();
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if (project != '') {
            $.ajax({
                url: base_url + "index.php/ClaimTrackingForms/subProjectMapping",
                type: "get", //send it through get method
                data: {
                    project: project,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                },
                success: function (response) {
                    $('#subProjectDiv').html(response);
                    $(".select2").select2();
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
        } else {
            $('#subProjectDiv').html('');
        }
    });

    $("body").on("click", "#addNewClaim", function (event) {
        add_values();
    });
    /*for dynamic addition of claim*/
    function add_values() {
        var claimId = $("#claim").val();
        var claimName = $("#claim option:selected").text();
        var claimAmount = $("#transactionAmount").val();
        var option = $("#paymentOption").val();
        var optionName = $("#paymentOption option:selected").text();
        var slNo = parseInt($("#slNo").val());
        if (claimId == "") {
            alertify.error("Please select the claim");
            return;
        }
        if ((claimAmount == "" || claimAmount < 0)) {
            alertify.error("Please enter valid amount");
            return;
        } else {
            //check claim amount already factored
            $.ajax({
                url: base_url + "index.php/tranTracking/claimAmountFactoredValidation",
                type: "get", //send it through get method
                dataType: 'json',
                data: {
                    claimId: claimId,
                    claimAmount: claimAmount,
                },
                success: function (response) {
                    if (response.status == 'failure') {
                        alertify.error(response.message);
                    } else {
                        //adding field to table
                        $('<tr id=' + slNo + '><td>' + slNo + '</td>' +
                                '<td><input type="text" class="form-control" name="claimName"' + slNo + ' readonly="readonly" value="' + claimName + '" id="claimName' + slNo + '"  style="text-align:right;" ></td>' +
                                '<input type="hidden" class="form-control" name="claimId' + slNo + '" value="' + claimId + '" id="claimId' + slNo + '" >' +
                                '<input type="hidden" class="form-control" name="option' + slNo + '" value="' + option + '" id="option' + slNo + '" >' +
                                '<td><input type="text" class="form-control" name="optionName' + slNo + '" readonly="readonly" value="' + optionName + '" id="optionName' + slNo + '"  style="text-align:right;" ></td>' +
                                '<td><input type="text" class="form-control claimAmount" name="claimAmount' + slNo + '" readonly="readonly" value="' + claimAmount + '" id="claimAmount' + slNo + '"  style="text-align:right;" ></td>' +
                                '<td><input type="button" value="x" id=' + slNo + ' style="width:30px;border:none;height:30px;"  onclick=$(this).closest("tr").remove(); ></td></tr>').fadeIn("slow").appendTo('.dis');
                        $("#claim").val('');
                        $("#transactionAmount").val('');
                        $("#slNo").val(slNo + 1);
                        $("#paymentOption").val('1');
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        }
    }

    function findClaimAmount(claimId) {
        $.ajax({
            url: base_url + "index.php/tranTracking/findClaimAmount",
            type: "get", //send it through get method
            dataType: 'json',
            data: {
                claimId: claimId,
            },
            success: function (response) {
                $("#transactionAmount").val(response.message);
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }
    $("body").on("change", "#claim", function (event) {
        $('#transactionAmount').prop('readonly', true);
        var claimId = $('#claim').val();
        if (claimId) {
            findClaimAmount(claimId);
        }
    });
    $("body").on("change", "#paymentOption", function (event) {
        var paymentOption = $(this).val();
        if (paymentOption == 2) {
            $('#transactionAmount').prop('readonly', false);
            $("#transactionAmount").val('');
        } else {
            $('#transactionAmount').prop('readonly', true);
            var claimId = $('#claim').val();
            if (claimId) {
                findClaimAmount(claimId);
            }
        }
    });
    //if project is claims noble, abin and talent then provide claim selecting option
    $("body").on("change", "#project", function (event) {
        claimMapLoad();
    });
    function claimMapLoad() {
        $.ajax({
            url: base_url + "index.php/tranTracking/findClaimHeads",
            type: "get", //send it through get method
            dataType: 'json',
            data: {},
            success: function (response) {
                var headIds = response.message;
                var project = $("#project").val();
                if (jQuery.inArray(project, headIds) != -1) {
                    $("#claimDiv").show();
                } else {
                    $("#claimDiv").hide();
                }
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }
    $("body").on("click", "#save", function (event) {
        event.preventDefault();
        var debitAmount = $("#debit_amount").val();
        var creditAmount = $("#credit_amount").val();
        if (debitAmount == '' && creditAmount == '') {
            alertify.error("Please enter debit or credit amount");
            return;
        }
        //find sum of claim amount
        var total = parseFloat(0);
        $(".claimAmount").each(function () {
            total = total + parseFloat($(this).val())
        });
        if (debitAmount < total) {
            alertify.error("Debit amount is less than sum of claim amount");
            return;
        }
        $("#editTransactionFrom").submit();
    });
</script>

