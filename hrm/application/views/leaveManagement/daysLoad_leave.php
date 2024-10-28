<?php
$form_validator = 1;
?>
<?php
//echo validation_errors();
echo form_open('', array(
    'class' => 'form-horizontal'
));
?>
<div class="col-md-12" id="change_needed">
    <div class="col-md-12">
        <?php
        if (isset($typeArray)) {
            $i = 0;
            foreach ($typeArray as $type) {
                $leave[$type] = $leftArray[$i];
                echo '<input type="hidden" name="type[]" value="' . $type . '"/>';
                echo '<input type="hidden" name="left[]" value="' . $leftArray[$i] . '"/>';
                $i++;
            }
        }
        $count = 0;
        $leave_left = 0;
        $compoff = 0;
        $el = 0;
        $emergency = 0;
        $maternity = 0;
        $paternity = 0;

        $compoff_count = 0;
        $el_count = 0;
        $emergency_count = 0;

        $lop = 0;
        //var_dump($typeArray);
        if (count($typeArray) > 0) {//checks leave type clicked or not
            if (isset($leave['1'])) {
                $leave_left = $leave_left + $leave['1'];
                $compoff = $leave['1'];
            }
            if (isset($leave['2'])) {
                $leave_left = $leave_left + $leave['2'];
                $el = $leave['2'];
            }
            if (isset($leave['3'])) {
                $leave_left = $leave_left + $leave['3'];
                $emergency = $leave['3'];
            }
            if (isset($leave['4'])) {
                $leave_left = $leave_left + $leave['4'];
                $maternity = $leave['4'];
            }
            if (isset($leave['5'])) {
                $leave_left = $leave_left + $leave['5'];
                $paternity = $leave['5'];
            }
            while (strtotime($toDate) >= strtotime($dateCount)) {
                $ltype = "";
                $dateCountStandard = date('Y-m-d', strtotime($dateCount));
                $strToTimeDate = strtotime($dateCountStandard);
                $day = strtolower(date("l", $strToTimeDate));
                $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCount);
                if (in_array($dateCountStandard, $talentWorking)) {
                    $ltype = '<button type="button" class="btn btn-flat bg-black margin">Worked</button>';
                    $color = 'red';
                } else if (in_array($dateCountStandard, $talentLeaves)) {
                    $ltype = '<button type="button" class="btn btn-flat bg-black margin">Applied</button>';
                    $color = 'red';
                } else if (in_array($dateCountStandard, $holidays)) {
                    $ltype = '<button type="button" class="btn btn-flat bg-blue margin">Holiday</button>';
                    if ($leave_left < $count) {
                        $lop++;
                        $ltype = '<button type="button" class="btn btn-flat bg-red margin">LOP</button>';
                    }
                    $color = 'blue';
                } else if (in_array($day, $weekends) && ($checkDateInStrictlyWorking == 0)) {
                    $ltype = '<button type="button" class="btn btn-flat bg-aqua margin">Weekend</button>';
                    if ($leave_left < $count) {
                        $lop++;
                        $ltype = '<button type="button" class="btn btn-flat bg-red margin">LOP</button>';
                    }
                    $color = 'blue';
                } else {
                    $ltype = '<button type="button" class="btn btn-flat bg-green margin">Working</button>';
                    $count++;
                    $color = 'green';
                    if ($leave_left < $count) {
                        $lop++;
                        $ltype = '<button type="button" class="btn btn-flat bg-red margin">LOP</button>';
                    } else if ($compoff > 0) {
                        $ltype = '<button type="button" class="btn btn-flat bg-purple margin">Comp-off</button>';
                        $compoff--;
                        $compoff_count++;
                    } else if ($el > 0) {
                        $ltype = '<button type="button" class="btn btn-flat bg-purple margin">EL</button>';
                        $el--;
                        $el_count++;
                    } else if ($emergency > 0) {
                        $ltype = '<button type="button" class="btn btn-flat bg-purple margin">Emergency</button>';
                        $emergency--;
                        $emergency_count++;
                    } else if ($maternity > 0) {
                        $ltype = '<button type="button" class="btn btn-flat bg-purple margin">Maternity</button>';
                        $maternity--;
                    } else if ($paternity > 0) {
                        $ltype = '<button type="button" class="btn btn-flat bg-purple margin">Paternity</button>';
                        $paternity--;
                    }
                }
                ?>
                <span  style="color: <?php echo $color; ?>">
                    <?php
                    $dateCountStandardday = date('D', strtotime($dateCountStandard));
                    $dateCountStandard = date('d/m/Y', strtotime($dateCount));
                    echo form_label($dateCountStandard . "[" . $dateCountStandardday . "]" . $ltype, 'Date', array(
                        'class' => 'col-sm-4 control-label'
                    ));
                    ?>
                </span>
                <?php
                $dateCount = strtotime("+1 day", strtotime($dateCount));
                $dateCount = date("m/d/Y", $dateCount);
            }
        } else {
            while (strtotime($toDate) >= strtotime($dateCount)) {
                $ltype = '';
                $dateCountStandard = date('Y-m-d', strtotime($dateCount));
                $strToTimeDate = strtotime($dateCountStandard);
                $day = strtolower(date("l", $strToTimeDate));
                $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCount);
                if (in_array($dateCountStandard, $talentWorking)) {
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
                } else {
                    $ltype = '<button type="button" class="btn btn-flat bg-green margin">Working</button>';
                    $count++;
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
        }


        //use Hidden to check in jquery
        echo '<input type="hidden" id="compoff_after" value="' . $compoff_count . '"/>';
        echo '<input type="hidden" id="el_after" value="' . $el_count . '"/>';
        echo '<input type="hidden" id="emergency_after" value="' . $emergency_count . '"/>';
        ?>
    </div>
    <div class="col-md-12" style="padding:15px;text-align: center;font-weight: bold;font-size: 20px;">
        <div class="col-md-2">
        </div>
        <div class="col-md-10">
            <center>
                <span style="color:green;">TOTAL <?php echo $count; ?> WORKING DAYS </span>
                <span style="color:red;"><?php if ($lop > 0) { ?>[ <?php echo $lop; ?> LOP ] <?php } ?></span>
            </center>
            <!--Hidden For jquery-->
            <input type="hidden" id="lop_hid" value="<?php echo $lop; ?>">
        </div>
    </div>
    <input type="hidden"  id="total" value="<?php echo $count; ?>">
</div>

<?php
echo form_close();
?>