<?php
if (isset($dateWiseData)) {
    ?>
    <table id="table_responsive" class="table table-bordered table-hover" style="width: 100%;margin-top:15px;">
        <thead>
            <tr>
                <td colspan="7" style="text-align: center;"><label>MONTH WISE DATA</label></td>
            </tr>
            <tr>
                <th>Sl No</th>
                <th>Project Code</th>
                <th>Project Description</th>
                <th>Year</th>
                <th>Month</th>
                <th>m</th>
                <th>Total Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $slNo = 1;
            $projectCode = '';
            $projectTime = '';
            $time = "";
            $totalTime = '';
            $showTotal = false;
            $gTotal = '';
            $toEnd = count($dateWiseData);
            $taskSl = 1;
            foreach ($dateWiseData as $data) {
                if ($projectCode != $data['project_code'] && $slNo != 1) {
                    $taskSl = 1;
                    $showTotal = true;
                    $totalTime = $this->talentAttendenceSheet->secondsToMinitues($projectTime);

                    $projectTime = '';
                } else {
                    $showTotal = false;
                }
                $projectTime += $data['tot_time'];
                $gTotal += $data['tot_time'];
                $projectCode = $data['project_code'];
                if ($showTotal) {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;">TOTAL</td>
                        <td colspan=""> <?php echo $totalTime; ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td><?php echo $taskSl; ?></td>
                    <td><?php echo $data['project_code']; ?></td>
                    <td><?php echo $data['name']; ?></td>
                    <td><?php echo $data['Y']; ?></td>
                    <td><?php echo $data['MN']; ?></td>
                    <td><?php echo $data['M']; ?></td>
                    <td><?php echo $this->talentAttendenceSheet->secondsToMinitues($data['tot_time']); ?></td>
                </tr>
                <?php
                $slNo++;
                $taskSl++;
                if (0 === --$toEnd) {
                    $totalTime = $this->talentAttendenceSheet->secondsToMinitues($projectTime);
                    $grantTotal = $this->talentAttendenceSheet->secondsToMinitues($gTotal);
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;"> TOTAL</td>
                        <td colspan=""> <?php echo $totalTime; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;">GRAND TOTAL</td>
                        <td colspan=""> <?php echo $grantTotal; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}

if (isset($talentWiseData)) {
    ?>
    <table id="table_responsive" class="table table-condensed" style="width: 100%;margin-top:15px;">
        <thead>
            <tr>
                <td colspan="7" style="text-align: center;"><label>TALENT WISE DATA</label></td>
            </tr>
            <tr>
                <th>Sl No</th>
                <th>Talent Code</th>
                <th>Talent Name</th>
                <th>Year</th>
                <th>Month</th>
                <th>m</th>
                <th>Total Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $slNo = 1;
            $talentSl = 1;
            $talentCode = '';
            $talentTime = '';
            $gTotal = '';
            $time = "";
            $totalTime = '';
            $showTotal = false;
            $toEnd = count($talentWiseData);
            foreach ($talentWiseData as $data2) {
                if ($talentCode != $data2['talent_code'] && $slNo != 1) {
                    $talentSl = 1;
                    $showTotal = true;
                    $totalTime = $this->talentAttendenceSheet->secondsToMinitues($talentTime);
                    $talentTime = '';
                } else {
                    $showTotal = false;
                }
                $talentTime += $data2['tot_time'];
                $gTotal += $data2['tot_time'];
                $talentCode = $data2['talent_code'];
                if ($showTotal) {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;">TOTAL</td>
                        <td colspan=""> <?php echo $totalTime; ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td><?php echo $talentSl; ?></td>
                    <td><?php echo $data2['talent_code']; ?></td>
                    <td><?php echo $data2['talent_name']; ?></td>
                    <td><?php echo $data2['Y']; ?></td>
                    <td><?php echo $data2['MN']; ?></td>
                    <td><?php echo $data2['M']; ?></td>
                    <td><?php echo $this->talentAttendenceSheet->secondsToMinitues($data2['tot_time']); ?></td>
                </tr>
                <?php
                $slNo++;
                $talentSl++;
                if (0 === --$toEnd) {
                    $totalTime = $this->talentAttendenceSheet->secondsToMinitues($talentTime);
                    $grandTotal = $this->talentAttendenceSheet->secondsToMinitues($gTotal);
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;"> TOTAL</td>
                        <td colspan=""> <?php echo $totalTime; ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight: bold;">GRAND TOTAL</td>
                        <td colspan=""> <?php echo $grandTotal; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>
