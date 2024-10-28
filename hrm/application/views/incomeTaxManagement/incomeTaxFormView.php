<?php
$select2 = 1;
$form_validator = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('incomeTaxForm/incomeTaxFormUpdate', array(
                'class' => 'form-horizontal',
                'id' => 'taxForm'
            ));
            ?>
            <input type="hidden" id="formId" name="formId" value="<?php echo $taxDetails->id; ?>">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Employee Income Tax Declaration Form 
                                for the financial year <?php echo $taxDetails->fin_year; ?></h3>
                        </div>
                        <div class="box-header with-border">
                           Name : <h3 class="box-title"> <?php echo $taxDetails->name; ?></h3>
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
                                    </td>
                                </tr>
                                <?php
                                $countIncome = 1;
                                foreach ($taxOtherIncome as $otherIncome) {
                                    ?>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherIncomesName' . $countIncome,
                                                'id' => 'otherIncomesName',
                                                'class' => 'form-control',
                                                'value' => $otherIncome['income_name'],
                                                'readonly' => 'readonly',
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherIncome' . $countIncome,
                                                'id' => 'otherIncome',
                                                'class' => 'form-control numeric',
                                                'value' => $otherIncome['income'],
                                                'readonly' => 'readonly',
                                                "data-fv-integer-message" => "The value is not an integer"
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $countIncome++;
                                }
                                ?>
                                <input type="hidden" id="countNewIncome" 
                                       name="countNewIncome" value="<?php echo $countIncome; ?>">
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
                                        Address of house property
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'address',
                                                    'id' => 'address',
                                                    'class' => 'form-control',
                                                    'value' => $taxDetails->address,
                                                    'readonly' => 'readonly',
                                                );

                                                echo $taxDetails->address;
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
                                        Whether self occupied (Yes/No)
                                    </td>
                                    <td>
                                        <div class="col-sm-5">  <?php
                                            if ($taxDetails->self_occupied == 'y') {
                                                echo 'yes';
                                            } else {
                                                echo 'no';
                                            }
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('gender'); ?> </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        If no, Net annual income of house property
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'netAnualIncome',
                                                    'id' => 'netAnualIncome',
                                                    'class' => 'form-control numeric',
                                                    'readonly' => 'readonly',
                                                    'value' => $taxDetails->net_anual_income,
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
                                        Amount of housing loan interest for the F.Y <?php echo $taxDetails->fin_year; ?>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'houseLoanInterest',
                                                    'id' => 'houseLoanInterest',
                                                    'class' => 'form-control numeric',
                                                    'readonly' => 'readonly',
                                                    'value' => $taxDetails->house_loan_interest,
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
                                        Amount of Interest for 
                                        Pre-Contruction Period (as per Income Tax Rule)
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'preConstructionInterest',
                                                    'id' => 'preConstructionInterest',
                                                    'class' => 'form-control numeric',
                                                    'readonly' => 'readonly',
                                                    'value' => $taxDetails->pre_construction_interest,
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
                                                    'readonly' => 'readonly',
                                                    'value' => $taxDetails->hra_rent,
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
                                        Public Provident Fund
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'providentFund',
                                                    'id' => 'providentFund',
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->provident_fund,
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
                                        Contribution to Certain pension Funds
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'pensionFund',
                                                    'id' => 'pensionFund',
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->pension_fund,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->principal_repayment,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->insurance_premium,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->term_deposit,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->savings,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->mutual_fund,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric invest',
                                                    'value' => $taxDetails->children_tution_fee,
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
                                        Others
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <?php
                                $countInvestments = 1;
                                foreach ($taxOtherInvestments as $otherInvestments) {
                                    ?>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherInvestmentsName' . $countInvestments,
                                                'id' => 'otherInvestmentsName',
                                                'readonly' => 'readonly',
                                                'class' => 'form-control numeric invest',
                                                'value' => $otherInvestments['investment_name'],
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherInvestmentsIncome' . $countInvestments,
                                                'id' => 'otherInvestments',
                                                'readonly' => 'readonly',
                                                'class' => 'form-control numeric invest',
                                                'value' => $otherInvestments['income'],
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $countInvestments++;
                                }
                                ?>
                                <input type="hidden" id="countNewInvestments" 
                                       name="countNewInvestments" value="<?php echo $countInvestments; ?>">
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
                                                    'value' => $taxDetails->total_investments,
                                                    'readonly' => 'readonly'
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric',
                                                    'value' => $taxDetails->pension_scheme,
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
                                        80D- Medical Insurance Premium 
                                        (Maximum Rs.25,000 (Rs. 30,333 for senior citizens))
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-sm-10">  <?php
                                                $rollText = array(
                                                    'name' => 'insurancePremiumDeduction',
                                                    'id' => 'insurancePremiumDeduction',
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric',
                                                    'value' => $taxDetails->insurance_premium_deduction,
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
                                                    'readonly' => 'readonly',
                                                    'class' => 'form-control numeric',
                                                    'value' => $taxDetails->donations,
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
                                        Others
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <?php
                                $countPermitted = 1;
                                foreach ($taxOtherPermitted as $otherPermitted) {
                                    ?>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherPermittedName' . $countPermitted,
                                                'id' => 'otherPermittedName',
                                                'readonly' => 'readonly',
                                                'class' => 'form-control numeric',
                                                'value' => $otherPermitted['permitted_name'],
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $rollText = array(
                                                'name' => 'otherPermittedIncome' . $countPermitted,
                                                'id' => 'otherPermitted',
                                                'readonly' => 'readonly',
                                                'class' => 'form-control numeric',
                                                'value' => $otherPermitted['income'],
                                            );

                                            echo form_input($rollText);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $countPermitted++;
                                }
                                ?>
                                <input type="hidden" id="countNewPermitted" 
                                       name="countNewPermitted" value="<?php echo $countPermitted; ?>">
                                <tr id="newPermitted">
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
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
        var counterIncome = $('#countNewIncome').val();
        var counterPermitted = $('#countNewPermitted').val();
        var counterInvestments = $('#countNewInvestments').val();
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