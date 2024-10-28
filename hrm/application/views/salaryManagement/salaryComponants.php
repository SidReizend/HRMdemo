<?php
echo form_open();
?>

<div class="form-group">
    <?php
    echo form_label('Pan', 'pan', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'pan',
            'id' => '9',
            'class' => 'form-control numeric',
            'value' => isset($row['9']) ? $row['9'] : '',
        );

        echo form_input($amountText);
        ?>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Account Number', 'account_number', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'account_number',
            'id' => '10',
            'class' => 'form-control numeric',
            'value' => isset($row['10']) ? $row['10'] : '',
        );

        echo form_input($amountText);
        ?>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Basic Pay', 'basicPay', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'basicPay',
            'id' => '1',
            'class' => 'form-control numeric',
            'value' => isset($row['1']) ? $row['1'] : '',
        );

        echo form_input($amountText);
        ?>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('DA', 'da', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'da',
            'id' => '2',
            'class' => 'form-control numeric',
            'value' => isset($row['2']) ? $row['2'] : '',
        );

        echo form_input($amountText);
        ?>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Allowance', 'allowance', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'allowance',
            'id' => '3',
            'class' => 'form-control numeric',
            'value' => isset($row['3']) ? $row['3'] : '',
        );

        echo form_input($amountText);
        ?>
        <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Tax Deduction at Source', 'Tax Deduction at Source', array(
        'class' => 'col-sm-2 control-label',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'tax_deduction_at_source',
            'id' => '4',
            'class' => 'form-control numeric',
            'value' => isset($row['4']) ? $row['4'] : '',
        );

        echo form_input($amountText);
        ?>
        <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Tax on Employment', 'tax_on_employment', array(
        'class' => 'col-sm-2 control-label',
        'required' => 'required',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'tax_on_employment',
            'id' => '5',
            'class' => 'form-control numeric',
            'value' => isset($row['5']) ? $row['5'] : '',
        );

        echo form_input($amountText);
        ?>
        <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Provident Fund', 'provident_fund', array(
        'class' => 'col-sm-2 control-label',
        'required' => 'required',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'provident_fund',
            'id' => '6',
            'class' => 'form-control numeric',
            'value' => isset($row['6']) ? $row['6'] : '',
            'required' => 'required',
        );

        echo form_input($amountText);
        ?>
        <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
    </div>
</div>
<div class="form-group">
    <?php
    echo form_label('Advance recovery', 'advance_recovery', array(
        'class' => 'col-sm-2 control-label',
        'required' => 'required',
    ));
    ?>
    <div class="col-sm-10">  <?php
        $amountText = array(
            'name' => 'advance_recovery',
            'id' => '8',
            'class' => 'form-control numeric',
            'value' => isset($row['8']) ? $row['8'] : '',
            'required' => 'required',
        );

        echo form_input($amountText);
        ?>
        <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
    </div>
</div>
<?php
echo form_close();
?>

