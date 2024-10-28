<div class="col-md-12 col-sm-12" id="change_needed">
    <div class="col-md-12">
        <?php
        while (strtotime($toDate) >= strtotime($dateCount)) {
            $ltype="";
            $dateCountStandard = date('Y-m-d', strtotime($dateCount));
            $strToTimeDate = strtotime($dateCountStandard);
            $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCountStandard);
            $day = strtolower(date("l", $strToTimeDate));
            if (in_array($dateCountStandard, $talentWorking)) {
                $ltype = '<button type="button" class="btn btn-flat bg-black margin">Worked</button>';
                $color = 'red';
            }else if (in_array($dateCountStandard, $talentLeaves)) {
                $color = 'red';
                $position=array_search($dateCountStandard,$talentLeaves);
                $ltype = '<button type="button" class="btn btn-flat bg-purple margin">'.$LeaveTypes[$position].'</button>';
            } else if (in_array($dateCountStandard, $holidays)) {
                $color = 'blue';
                $ltype = '<button type="button" class="btn btn-flat bg-blue margin">Holiday</button>';
            } else if (in_array($day, $weekends)  && ($checkDateInStrictlyWorking == 0)) {
                $color = 'blue';
                $ltype = '<button type="button" class="btn btn-flat bg-aqua margin">Weekend</button>';
            }else{
                $color = 'green';
                $ltype = '<button type="button" class="btn btn-flat bg-green margin">Working</button>';
            }
            ?>
            <span style="color: <?php echo $color; ?>">

        <?php
        
        $dateCountStandardday = date('D', strtotime($dateCountStandard));
        $dateCountStandard = date('d/m/Y', strtotime($dateCount));
        ?>
                <label class="col-sm-12 col-md-4 control-label"><?php echo $dateCountStandard . "[" . $dateCountStandardday . "]" . $ltype; ?></label>
    </span>
            <?php
            $dateCount = strtotime("+1 day", strtotime($dateCount));
            $dateCount = date("m/d/Y", $dateCount);
        }

        ?>
    </div>
</div>