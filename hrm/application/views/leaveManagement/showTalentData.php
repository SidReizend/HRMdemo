<?php
$form_validator = 1;
?>
<?php
//echo validation_errors();
echo form_open('', array(
    'class' => 'form-horizontal'
));
?>

<div class="col-lg-2">
</div>
<div class="col-lg-10">
    <?php
    // var_dump($getMyLeavesLeft);
    $paternityEnabled = $this->defaultValues->getValue(32);
    $emergencyEnabled = $this->defaultValues->getValue(33);
    foreach ($getMyLeavesLeft as $getmyleave) {
        $showLeaveLeft = TRUE;
        if ($emergencyEnabled == 0 && $getmyleave['id'] == 3) {
            $showLeaveLeft = false;
        }
        if ($paternityEnabled == 0 && ($getmyleave['id'] == 4 || $getmyleave['id'] == 5)) {
            $showLeaveLeft = FALSE;
        }
        if (TRUE === $showLeaveLeft) {
            $leaveLeft = $getmyleave['id'] == 2 ? ($getmyleave['leave_left']) : round($getmyleave['leave_left']);
            $halfDayMechanism = $this->defaultValues->getValue(34);
            if ($halfDayMechanism != 1) {
                $leaveLeft = round($leaveLeft);
            }
            ?>
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box <?php
                if ($getmyleave['leave_left'] == 0) {
                    echo 'bg-red';
                } else {
                    echo 'bg-aqua';
                }
                ?>">
                    <div class="inner">
                        <h3><?php echo $leaveLeft; ?></h3>
                        <p><?php echo $getmyleave['type']; ?></p>
                    </div>
                    <div class="icon">
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    <div class="col-lg-2 col-xs-6">
        <!-- small box -->
        <div class="small-box <?php
        if ($getMylopLeft == 0) {
            echo 'bg-aqua';
        } else {
            echo 'bg-red';
        }
        ?>">
            <div class="inner">
                <h3><?php echo $getMylopLeft; ?></h3>
                <p>LOP</p>
            </div>
            <div class="icon">
            </div>
        </div>
    </div>

</div>
<div id="daysLoad">

</div>
<div class="form-group">
    <?php
    echo form_label('Request', 'request', array(
        'class' => 'col-sm-2 control-label'
    ));
    ?>
    <div class="col-sm-10">  <?php
        $requestText = array(
            'name' => 'request',
            'id' => 'request',
            'class' => 'form-control',
            'rows' => '3',
            'value' => set_value('request'),
            'data-validation' => 'required',
        );

        echo form_textarea($requestText);
        ?>
        <span class="text-danger"> <?php echo form_error('request'); ?> </span>
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
            'class' => 'btn btn-small btn-primary save_all'
        );
        echo form_button($submitButton)
        ?>
    </div>
    <!-- /.col -->
</div>
<?php
echo form_close();
?>