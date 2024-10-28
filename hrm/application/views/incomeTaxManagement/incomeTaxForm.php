<?php
$select2 = 1;
$form_validator = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    .subheading {
        display: inline-block;
        margin-bottom: 5px;
        max-width: 100%;
    }
</style>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('incomeTaxForm/addNewTaxForm', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Employee Income Tax Declaration Form for 
                                the financial year <?php echo $currentFinancialYear->financial_year; ?></h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-hover" id="taxTable">
                                <tr>
                                    <th>
                                        Sl.No
                                    </th>
                                    <th>
                                        Description
                                    </th>
                                    <th>
                                        Processed Investment
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        $companyHrm = $this->defaultValues->getValue(21);
                                        echo form_label('A', $companyHrm, array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label("Particulars of Income Other than salary from $companyHrm (if specify)", 'vrl', array(
                                                'class' => 'control-label'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" id="addNewIncome" >Add new income</a>
                                        <input type="hidden" id="countNewIncome" name="countNewIncome" value="0">
                                    </td>
                                </tr>
                                <tr id="newIncomeRow">
                                    <td>
                                        <?php
                                        echo form_label('B', 'vrl', array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('DETAILS OF HOUSE PROPERTY FOR '
                                                    . 'CLAIMING REBATE UNDER SECTION 24 OF '
                                                    . 'THE IT ACT ', 'houseProperty', array(
                                                'class' => 'control-label'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                        echo 'Address of house property';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'address',
                                            'id' => 'address',
                                            'class' => 'form-control',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('address'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                        echo 'Whether self occupied (Yes/No)';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="col-sm-5">  <?php
                                        $selfOccupiedRadio = array(
                                            'name' => 'selfOccupied',
                                            'id' => 'y',
                                            'value' => 'y',
                                            'checked' => set_radio('selfOccupied', 'y', TRUE)
                                        );
                                        ?> 
                                            <?php
                                            echo form_label(form_radio($selfOccupiedRadio) . 'Yes', 'y', array(
                                            ));
                                            ?>

                                            <?php
                                            $selfOccupiedRadio = array(
                                                'name' => 'selfOccupied',
                                                'id' => 'n',
                                                'value' => 'n',
                                                'checked' => set_radio('selfOccupied', 'n')
                                            );
                                            echo form_label(form_radio($selfOccupiedRadio) . 'No', 'n', array(
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('gender'); ?> </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                        echo 'If no, Net annual income of house property';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'netAnualIncome',
                                            'id' => 'netAnualIncome',
                                            'class' => 'form-control numeric',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('netAnualIncome'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                        echo 'Amount of housing loan interest for the F.Y ' . $currentFinancialYear->financial_year;
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'houseLoanInterest',
                                            'id' => 'houseLoanInterest',
                                            'class' => 'form-control numeric',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('houseLoanInterest'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                        echo 'Amount of Interest for '
                                        . 'Pre-Contruction Period (as per Income Tax Rule)';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'preConstructionInterest',
                                            'id' => 'preConstructionInterest',
                                            'class' => 'form-control numeric',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('preConstructionInterest'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        echo form_label('C', 'vrl', array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('HRA: RENT PAID PER MONTH', 'hraRent', array(
                                                'class' => 'control-label'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                            $rollText = array(
                                                'name' => 'hraRent',
                                                'id' => 'hraRent',
                                                'class' => 'form-control numeric',
                                                'value' => '',
                                            );

                                            echo form_input($rollText);
                                            ?>
                                                <span class="text-danger"> <?php echo form_error('hraRent'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        echo form_label('D', 'vrl', array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('INVESTMENTS U/S 80C,80CCC,80CCD', 'investment', array(
                                                'class' => 'control-label'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        <?php
                                        echo 'Public Provident Fund';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'providentFund',
                                            'id' => 'providentFund',
                                            'class' => 'form-control numeric invest',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('providentFund'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2
                                    </td>
                                    <td>
                                        <?php
                                        echo 'Contribution to Certain pension Funds';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                        $rollText = array(
                                            'name' => 'pensionFund',
                                            'id' => 'pensionFund',
                                            'class' => 'form-control numeric invest',
                                            'value' => '',
                                        );

                                        echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('pensionFund'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        3
                                    </td>
                                    <td>
                                        Housing Loan Principal Repayment
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'principalRepayment',
                                                    'id' => 'principalRepayment',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('principalRepayment'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        4
                                    </td>
                                    <td>
                                        Insurance Premium
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'inserancePremium',
                                                    'id' => 'inserancePremium',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('inserancePremium'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        5
                                    </td>
                                    <td>
                                        Term Deposit With Schedule Bank
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'termDeposit',
                                                    'id' => 'termDeposit',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('termDeposit'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        6
                                    </td>
                                    <td>
                                        National Saving Scheme/Certificate
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'savings',
                                                    'id' => 'savings',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('savings'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        7
                                    </td>
                                    <td>
                                        Mutual Fund
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'mutualFund',
                                                    'id' => 'mutualFund',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('mutualFund'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        8
                                    </td>
                                    <td>
                                        Children Tuition Fees
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'childrenTutionFee',
                                                    'id' => 'childrenTutionFee',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                        ?>
                                                <span class="text-danger"> <?php echo form_error('childrenTutionFee'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        9
                                    </td>
                                    <td>
                                        Others, (Please specify if any)
                                    </td>
                                    <td>
                                        <a href="#" id="addNewInvestments" >Add new</a>
                                        <input type="hidden" id="countNewInvestments" name="countNewInvestments" value="0">
                                    </td>
                                </tr>
                                <tr id="newInvestments">
                                    <td>

                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('Total Investments (U/S 80C - limited to Rs 1,50,000/-only)'
                                                    , 'totalInvestments', array(
                                                'class' => 'col-sm-4'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                            $rollText = array(
                                                'name' => 'totalInvestments',
                                                'id' => 'totalInvestments',
                                                'class' => 'form-control numeric',
                                                'value' => '',
                                                'readonly' => '',
                                            );

                                            echo form_input($rollText);
                                            ?>
                                                <span class="text-danger"> <?php echo form_error('totalInvestments'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        echo form_label('E', 'vrl', array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('ADDITIONAL BENEFIT -Section 80CCD - National Pension '
                                                    . 'Scheme (NPS) Rs 50000'
                                                    , 'pensionScheme', array(
                                                'class' => 'col-sm-4'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                            $rollText = array(
                                                'name' => 'pensionScheme',
                                                'id' => 'pensionScheme',
                                                'class' => 'form-control numeric',
                                                'value' => '',
                                            );

                                            echo form_input($rollText);
                                            ?>
                                                <span class="text-danger"> <?php echo form_error('pensionScheme'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        echo form_label('F', 'vrl', array(
                                            'class' => 'control-label'
                                        ));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?php
                                            echo form_label('OTHER PERMITTED DEDUCTIONS'
                                                    , 'permittedDeductions', array(
                                                'class' => 'col-sm-4'
                                            ));
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        80D- Medical Insurance Premium (Maximum Rs.25,000 
                                        (Rs. 30,333 for senior citizens))
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                            $rollText = array(
                                                'name' => 'insurancePremiumDeduction',
                                                'id' => 'insurancePremiumDeduction',
                                                'class' => 'form-control numeric',
                                                'value' => '',
                                            );

                                            echo form_input($rollText);
                                            ?>
                                                <span class="text-danger"> <?php echo form_error('insurancePremiumDeduction'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2
                                    </td>
                                    <td>
                                        80G-Donations
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'donations',
                                                    'id' => 'donations',
                                                    'class' => 'form-control numeric',
                                                    'value' => '',
                                                );

                                                echo form_input($rollText);
                                            ?>
                                                <span class="text-danger"> <?php echo form_error('donations'); ?> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        3
                                    </td>
                                    <td>
                                        Others-(Please specify if any)
                                    </td>
                                    <td>
                                        <a href="#" id="addNewPermitted" >Add new</a>
                                        <input type="hidden" id="countNewPermitted" name="countNewPermitted" value="0">
                                    </td>
                                </tr>
                                <tr id="newPermitted">
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-2 control-label">
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-xs-10">
                                                <?php
                                                $submitButton = array(
                                                    'type' => 'submit',
                                                    'content' => 'Save',
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
                                    </td>
                                </tr>
                            </table>
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
    jQuery(function () {
        var counterIncome = 1;
        var counterPermitted = 1;
        var counterInvestments = 1;
        $('#addNewIncome').click(function (event) {
            event.preventDefault();
            var newRow = jQuery('<tr><td></td><td><input type="text" class="form-control col-sm-7" name="otherIncomesName' +
                    counterIncome + '"/></td><td><input type="text" class="form-control numeric" name="otherIncome' +
                    counterIncome + '"/><button type="button" class="btn-primary rowDelete" >delete</button></td></tr>');
            $('#countNewIncome').val(counterIncome);
            counterIncome++;
            $('tr#newIncomeRow').before(newRow);
        });

        $('#addNewPermitted').click(function (event) {
            event.preventDefault();
            var newRow = jQuery('<tr><td></td><td><input type="text" class="form-control col-sm-7" name="otherPermittedName' +
                    counterPermitted + '"/></td><td><input type="text" class="form-control numeric" name="otherPermittedIncome' +
                    counterPermitted + '"/><button type="button" class="btn-primary rowDelete" >delete</button></td></tr>');
            $('#countNewPermitted').val(counterPermitted);
            counterPermitted++;
            $('tr#newPermitted').before(newRow);
        });

        $('#addNewInvestments').click(function (event) {
            event.preventDefault();
            var newRow = jQuery('<tr><td></td><td><input type="text" class="form-control" name="otherInvestmentsName' +
                    counterInvestments + '"/></td><td><input type="text" class="form-control numeric invest" name="otherInvestmentsIncome' +
                    counterInvestments + '"/><button type="button" class="btn-primary rowDelete" >delete</button></td></tr>');
            $('#countNewInvestments').val(counterInvestments);
            counterInvestments++;
            $('tr#newInvestments').before(newRow);
        });
        $("body").on("click", ".rowDelete", function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
            findsum();
        });
        $("body").on("keyup", ".numeric", function (event) {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                this.value = this.value.replace(/[^0-9\.]/g, '');
            }
        });
        $("body").on("keyup", ".invest", function (event) {
            findsum();
        });
        $("body").on("blur", ".numeric", function (event) {
            findsum();
        });

        function findsum() {
            sum = 0;
            $('.invest').each(function () {
                if ($(this).val()) {
                    sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
                }
            });
            sum = sum.toFixed(2);
            $('#totalInvestments').val(sum);
        }

    });
</script>
