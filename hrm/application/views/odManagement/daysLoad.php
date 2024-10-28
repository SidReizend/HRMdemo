<?php
$form_validator = 1;
?>

<?php
//echo validation_errors();
echo form_open('MyOds/showDatesBetweenDates', array(
    'class' => 'form-horizontal'
));
?>
<?php
while (strtotime($toDate) >= strtotime($dateCount)) {
    $dateCountStandard = $dateCount;
    $strToTimeDate = strtotime($dateCountStandard);
    $day = strtolower(date("l", $strToTimeDate));
    $color = 'green';
    $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCount);
    if (in_array($dateCountStandard, $talentOds)) {
        $color = 'red';
    } else if (in_array($dateCountStandard, $leaves)) {
        $color = 'black';
    } else if (in_array($dateCountStandard, $holidays)) {
        $color = 'blue';
    } else if (in_array($day, $weekends) && ($checkDateInStrictlyWorking == 0)) {
        $color = 'blue';
    }
    ?>
    <div class="col-sm-4 od_details" id="<?php echo $dateCount; ?>" 
         style="color: <?php echo $color; ?>"
         data-original-title=""
         data-toggle="tooltip">
             <?php
             echo form_label(date("d/m/Y", strtotime($dateCount)), 'odTypeName', array(
                 'class' => 'col-sm-2 control-label'
             ));
             ?>
        <div class="col-sm-12">  <?php
            $hoursText = array(
                'name' => 'hour[' . $dateCount . ']',
                'id' => 'hour[' . $dateCount . ']',
                'class' => 'form-control',
                'style' => 'border-color: ' . $color,
                'value' => '08',
                'type' => 'number',
                'max' => '23',
            );

            echo form_input($hoursText) . 'hr';
            ?>

            <br/>
            <br/>
        </div>
        <div class="col-sm-12">  <?php
            $minText = array(
                'name' => 'min[' . $dateCount . ']',
                'id' => 'min[' . $dateCount . ']',
                'class' => 'form-control',
                'style' => 'border-color: ' . $color,
                'value' => '00',
                'type' => 'number',
                'max' => '59',
            );

            echo form_input($minText) . 'min';
            ?>

            <br/>
        </div>
    </div>
    <?php
    $dateCount = strtotime("+1 day", strtotime($dateCount));
    $dateCount = date("Y-m-d", $dateCount);
}
?>
<?php
$odOwnerText = array(
    'odOwner' => $talent,
);

echo form_hidden($odOwnerText);
?>
<br/>
<div class="col-sm-12">
    <center>
        <div class="form-group">
            <div class="col-sm-2 control-label">
            </div>
            <div class="col-sm-10">
                <?php
                $submitButton = array(
                    'type' => 'submit',
                    'content' => 'Save',
                    'class' => 'btn btn-small btn-primary'
                );
                echo form_button($submitButton)
                ?>
            </div>
            <!-- /.col -->
        </div>
    </center>
    <span style="color: red">
        * Already applied OD for the date
    </span>
    &nbsp;&nbsp;
    <span style="color: green">
        * Working Day
    </span>
    &nbsp;&nbsp;
    <span style="color: black">
        * Leave 
    </span>
    &nbsp;&nbsp;
    <span style="color: blue">
        * Holiday 
    </span>
</div>
<?php
echo form_close();
?>

