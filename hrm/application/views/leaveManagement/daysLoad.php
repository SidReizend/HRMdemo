<?php
$form_validator = 1;
?>
<?php
//echo $this->leaves->checkCompoffAgainstDate($dateCount);
//echo validation_errors();
echo form_open('', array(
    'class' => 'form-horizontal'
));
?>
<div class="col-xs-12" style="padding: 10px;">
    <?php if ($current_quarter == 1) { ?>
        <h3 style="text-align:center;padding-bottom: 15px;"><b>Your Leave Dates in Current Quarter</b></h3>
        <span class="col-xs-12" style="text-align: center;">Please Choose One of Your Leave Type</span>
        <input type="hidden" id="current_quarter" name="current_quarter" value="1">
    <?php } else { ?>
        <h3 style="text-align:center;padding-bottom: 15px;"><b>Your Leave Dates in Next Quarter</b></h3>
        <span class="col-xs-12" style="text-align: center;">You Cannot Choose any Leave Type Here</span>
        <input type="hidden" id="next_quarter" name="next_quarter" value="1">
    <?php } ?>

    <div class="col-xs-2" style="padding: 10px;">
    </div>
    <?php
    if ($current_quarter == 1) {
        // var_dump($getMyLeavesLeft);
        $leaves = array();
        foreach ($getMyLeavesLeft as $getmyleave) {
            $color = "bg-purple leave_type_apply";
            if ($getmyleave['id'] == 1) {
                //   var_dump($compoffTillDate);
                $getmyleave['leave_left'] = $compoffTillDate;
                echo '<input type="hidden" id="compoff_hid" value="' . $getmyleave['leave_left'] . '">';
            }
            if ($getmyleave['id'] == 2) {
                if ($leaves[1] > 0) {
                    $color = "bg-purple disabled el ";
                }
                echo '<input type="hidden" id="el_hid" value="' . $getmyleave['leave_left'] . '">';
            }
            if ($getmyleave['id'] == 3) {
                if (($leaves[1] + $leaves[2]) > 0) {
                    $color = "bg-purple disabled emergency ";
                }
                echo '<input type="hidden" id="emergency_hid" value="' . $getmyleave['leave_left'] . '">';
            }
            if ($getmyleave['leave_left'] < 1) {
                $color = "bg-red";
            }
            $leaves[$getmyleave['id']] = $getmyleave['leave_left'];
            $showLeaveLeft = TRUE;
            $paternityEnabled = $this->defaultValues->getValue(32);
            $emergencyEnabled = $this->defaultValues->getValue(33);
            if ($emergencyEnabled == 0 && $getmyleave['id'] == 3) {
                $showLeaveLeft = false;
            }
            if ($paternityEnabled == 0 && ($getmyleave['id'] == 4 || $getmyleave['id'] == 5)) {
                $showLeaveLeft = FALSE;
            }
            $leaveLeft = $getmyleave['id'] == 2 ? ($getmyleave['leave_left']) : round($getmyleave['leave_left']);
            $halfDayMechanism = $this->defaultValues->getValue(34);
            if ($halfDayMechanism != 1) {
                $leaveLeft = round($leaveLeft);
            }
            if ($showLeaveLeft && $getmyleave['id'] !=3) {
                ?>
                <div class="col-lg-2 col-sm-6" style="z-index: 1;">
                    <button class="btn btn-block margin <?php echo $color; ?>"
                            data-type="<?php echo $getmyleave['id']; ?>"
                            type="button" data-left="<?php echo $leaveLeft; ?>">
                        <?php echo $getmyleave['type']; ?>-<?php echo $leaveLeft; ?>
                    </button>
                </div>
                <?php
            }
        }
    }
    ?>
</div>
<div class="col-md-12" <?php if ($current_quarter == 1) { ?> id="change_needed" <?php } ?> >
    <div class="col-md-12">
        <?php
        $count = 0;
        $count_next = 0;
        while (strtotime($toDate) >= strtotime($dateCount)) {
            $ltype = '';
            $dateCountStandard = date('Y-m-d', strtotime($dateCount));
            $strToTimeDate = strtotime($dateCountStandard);
            $day = strtolower(date("l", $strToTimeDate));
            $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCount);
            if (in_array($dateCountStandard, $plannedLeaveArray)) {
                $ltype = '<button type="button" class="btn btn-flat bg-black margin">Planned</button>';
                $color = 'red';
            } else if (in_array($dateCountStandard, $talentWorking)) {
                $ltype = '<button type="button" class="btn btn-flat bg-black margin">Worked</button>';
                $color = 'red';
            } else if (in_array($dateCountStandard, $talentLeaves)) {
                $ltype = '<button type="button" class="btn btn-flat bg-black margin">Applied</button>';
                $color = 'red';
            } else if (in_array($dateCountStandard, $holidays)) {
                $ltype = '<button type="button" class="btn btn-flat bg-blue margin">Holiday</button>';
                $color = 'blue';
            } else if (in_array($day, $weekends) && ($checkDateInStrictlyWorking == 0)) {
                $ltype = '<button type="button" class="btn btn-flat bg-aqua margin">Weekend</button>';
                $color = 'blue';
                $count_next++;
            } else {
                $ltype = '<button type="button" class="btn btn-flat bg-green margin">Working</button>';
                $count++;
                $count_next++;
                $color = 'green';
            }
            ?>
            <span style="color: <?php echo $color; ?>">

                <?php
                $dateCountStandardday = date('D', strtotime($dateCountStandard));
                $dateCountStandard = date('d/m/Y', strtotime($dateCount));
                echo form_label($dateCountStandard . "[" . $dateCountStandardday . "]" . $ltype, 'Date', array(
                    'class' => 'col-sm-4 control-label'
                ));
                // echo $leave_left . "/" .$count ."<br>";
                ?>
            </span>
            <?php
            $dateCount = strtotime("+1 day", strtotime($dateCount));
            $dateCount = date("m/d/Y", $dateCount);
        }
        ?>
    </div>
    <div class="col-md-12" style="padding:15px;text-align: center;font-weight: bold;font-size: 20px;">
        <div class="col-md-2">
        </div>
        <div class="col-md-10">
            <center>
                <span style="color:green;">TOTAL <?php echo $count; ?> WORKING DAYS </span>
            </center>
        </div>
        <?php if ($current_quarter == 1) { ?>
            <input type="hidden" id="total" value="<?php echo $count; ?>">
        <?php } ?>
        <?php if ($current_quarter == 0) { ?>
            <input type="hidden" id="total_planned" value="<?php echo $count_next; ?>">
        <?php }
        ?>

    </div>
</div>
<br/>
<?php
echo form_close();
?>