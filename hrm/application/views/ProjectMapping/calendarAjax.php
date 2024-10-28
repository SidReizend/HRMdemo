<?php
if (isset($projectwise)) {
    $month = date('m', strtotime($date));
    $year = date('Y', strtotime($date));
    $monthname = date('M', strtotime($date));
    $time = strtotime($date . "-01");
    $forward = date("Y-m-d", strtotime("+1 month", $time));
    $prev = date("Y-m-d", strtotime("-1 month", $time));
    ?>

    <div class="month" >      
        <ul>
            <li class="prev cngDate" style="cursor: pointer;" data-date="<?php echo $prev; ?>">❮</li>
            <li class="next cngDate" style="cursor: pointer;" data-date="<?php echo $forward; ?>">❯</li>
            <li style="text-align:center">
                <?php echo $monthname; ?><br>
                <span style="font-size:18px"><?php echo $year; ?></span>
            </li>
        </ul>
    </div>    
    <?php
    /* sample usages */
    echo $this->ProjectMappingModel->draw_calendarProject($month, $year, $project_id, $mapping);
    ?>
    <?php
}
if (isset($talentwise)) {
    $month = date('m', strtotime($date));
    $year = date('Y', strtotime($date));
    $monthname = date('M', strtotime($date));
    $time = strtotime($date . "-01");
    $forward = date("Y-m-d", strtotime("+1 month", $time));
    $prev = date("Y-m-d", strtotime("-1 month", $time));
    ?>
    <div class="month">      
        <ul>
            <li class="prev cngDate" style="cursor: pointer;" data-date="<?php echo $prev; ?>">❮</li>
            <li class="next cngDate" style="cursor: pointer;" data-date="<?php echo $forward; ?>">❯</li>
            <li style="text-align:center">
                <?php echo $monthname; ?><br>
                <span style="font-size:18px"><?php echo $year; ?></span>
            </li>
        </ul>
    </div>    
    <?php
    /* sample usages */
    echo $this->ProjectMappingModel->draw_calendarTalent($month, $year, $talent_id, $mapping);
    ?>
    <?php
}


