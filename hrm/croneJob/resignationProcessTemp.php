<?php

if (PHP_SAPI === 'cli') {
    $talentId = $argv[1];
    $dateFrom = date('Y-m-d', strtotime($argv[2]));
    $dateTo = date('Y-m-d', strtotime($argv[3]));
    $month = date('m', strtotime($argv[3]));
    $year = date('Y', strtotime($argv[3]));
} else {
    die('Not CLI');
}
include 'database.php';

date_default_timezone_set("Asia/Calcutta");
//$con = mysql_connect('localhost', 'hrm', 'hrmpass');
//$con = mysql_connect('localhost', 'nizam_ahmed', 'nizam1234');
//--------------connecting db-------------//
//$db = mysql_select_db("hrm");
//$db = mysql_select_db("reizend_hrm");
//check monthly crone job already done
//if done do not run again else do the crone job 
//finds the start date of a month
$startDayOfMonth = getDefaultValue(12);
$endDayOfMonth = getDefaultValue(13);
$managementRolestream = getDefaultValue(28);
/*
 * find the od pending to approve in this month and 
 */
$sql_select_talents = "SELECT talents.`id`,`average_working_hour_per_day`,"
        . "`average_hour_calculation`,`date_of_joining` "
        . "FROM `talents` "
        . "JOIN `role` ON `role`.`id`=`talents`.`role` "
        . "WHERE `talents`.`id`='$talentId' ";
$result_select_talents = mysql_query($sql_select_talents);
while ($row_select_talents = mysql_fetch_array($result_select_talents)) {

    $lop_deducted = false;

    $talentId = $row_select_talents['id'];
    $dateOfJoining = $row_select_talents['date_of_joining'];
    $compOffAvailable = 0;
    $elAvailable = 0;
    $shortage = 0;
    $compOffDeductedProcess = 0;
    $compOffDeductedShortage = 0;
    $elDeductedProcess = 0;
    $elDeductedShortage = 0;
    $encashment = 0;
    $lopByProcess = 0;
    $lopByShortage = 0;
    $totalProductiveSeconds = 0;
    $presentDay = 0;
    $days_pay = 0;
    $extraAttendanceSeconds = 0;
    $extraOdSeconds = 0;
    $hourShortage = 0;
    $secondsShortage = 0;
    $encashmentForExtra = 0;
    $compOffLeft = 0;
    $elLeft = 0;
    $no_days_pay = 0;
    $lopCheckStatus = 0;
    $lop_count = 0;
    $first_time = true;
    $lop_insert_talent = 0;
    $extraAttendanceHoursInserting = 0;
    $extraOdHoursInserting = 0;
    $totalHourRequiredInserting = 0;
    $totalProductiveHoursInserting = 0;
    $hourShortageInserting = 0;
    $idArray = array();
    $idArray[] = '';

    $averageHourRequired = $row_select_talents['average_working_hour_per_day'];
    $averageHourSeconds = timeToSecondsConvertion($averageHourRequired);

    $sqlTalentLeaveLeft = "SELECT * FROM talent_leave_left WHERE `talent`='$talentId'";
    $resultTalentLeaveLeft = mysql_query($sqlTalentLeaveLeft);
    while ($rowTalentLeaveLeft = mysql_fetch_array($resultTalentLeaveLeft)) {
//            if ($rowTalentLeaveLeft['leave_type'] == '1') {
//                $compOffAvailable = $rowTalentLeaveLeft['leave_left'];
//            }
        if ($rowTalentLeaveLeft['leave_type'] == '2') {
            $elAvailable = $rowTalentLeaveLeft['leave_left'];
        }
    }



    $dateCount = $dateFrom;
    while (strtotime($dateTo) >= strtotime($dateCount)) {

        $sqlTalentAttendance = "SELECT * "
                . "FROM `talent_attendance` "
                . "WHERE `talent_id`='" . $talentId . "' AND "
                . "attendance_date = '" . $dateCount . "' ";
        $resultTalentAttendance = mysql_query($sqlTalentAttendance);
        if ($rowTalentAttance = mysql_fetch_array($resultTalentAttendance)) {
            $days_pay++;
            if (date('m', strtotime($dateCount)) == date('m', strtotime($dateTo))) {
                $no_days_pay++;
            }
            if ($rowTalentAttance['encashment'] == 1) {
                $encashment++;
            }
            if ($rowTalentAttance['avg_consider'] == 0 && $rowTalentAttance['is_holiday'] == 0 && $rowTalentAttance['is_leave'] == 0) {
                /* leave status get  0 && 1 exclude */

                /**
                 * Get Compoff
                 */
                $lop_insert_talent = 0;

                //echo "SELECT min(id) as id from  talent_compoff  WHERE used=0 AND temp_used=0 AND talent=$talentId AND date_of_compoff <= '$dateCount'";

                $sql_get_compoff = mysql_query("SELECT min(id) as id from  talent_compoff  WHERE used=0 AND temp_used=0 AND talent=$talentId AND date_of_compoff <= '$dateCount'") or die(mysql_error());
                $res_get_compoff = mysql_fetch_array($sql_get_compoff);
                $num_get_compoff = mysql_num_rows($sql_get_compoff);
                if ($num_get_compoff > 0) {
                    //var_dump($res_get_compoff_temp);
                    if (in_array($res_get_compoff['id'], $idArray)) {
                        $compOffAvailable = 0;
                    } else {
                        $compOffAvailable = 1;
                        $idArray[] = $res_get_compoff['id'];
                    }
                } else {
                    $compOffAvailable = 0;
                }
                // $res_get_compoff['id'];
                //die($res_get_compoff['id']);
                $lop_deducted = true;

                if ($compOffAvailable > 0) {
                    leaveDeduction($talentId, 1, $dateCount, $message, 0, $res_get_compoff['id']);
                    $compOffAvailable--;
                    mysql_query("UPDATE talent_compoff set temp_used=1 where id='" . $res_get_compoff['id'] . "'") or die(mysql_error());
                    echo $res_get_compoff['id'] . ',';
                    $idArray[] = $res_get_compoff['id'];
                    $compOffDeductedProcess++;
                } else if ($elAvailable > 0) {
                    leaveDeduction($talentId, 2, $dateCount, $message, 0, 0);
                    $elAvailable--;
                    $elDeductedProcess++;
                } else {
                    echo $message = 'System Applied due to absent' . $dateCount . '<br/>';
                    leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                    $lop_insert_talent = 1;
                    $lopByProcess++;
                }
            } else {
                if ($rowTalentAttance['avg_consider'] == 1) {
                    $lop_insert_talent = 0;
                    $presentDay++;
                }
            }
            $totalProductiveSeconds += timeToSecondsConvertion($rowTalentAttance['productive_hrs']);
            if ($rowTalentAttance['is_holiday'] == 1 || $rowTalentAttance['is_leave'] == 1) {
                $extraOdSeconds += timeToSecondsConvertion($rowTalentAttance['extra_od_hrs']);
                $extraAttendanceSeconds += timeToSecondsConvertion($rowTalentAttance['extra_attendance_hrs']);
            }
        }
        if ($roleStream != $managementRolestream) {
            //finding the lop exists for the date
            $is_current_day_lop = false;

            $final_day_lop = false;

            $sqlLopFinding = "SELECT tld.`id` "
                    . "FROM `talent_leave_date` tld "
                    . "JOIN `talent_leave` tl ON tld.talent_leave=tl.id "
                    . "WHERE `tld`.`date_of_leave`='$dateCount' "
                    . "AND `tl`.`talent`='$talentId' "
                    . "AND tld.`is_approved`='1'";
            $resultLopFinding = mysql_query($sqlLopFinding);

            if (mysql_num_rows($resultLopFinding) > 0) {
                $is_current_day_lop = true;
            }
            $next_working_day_worked = find_next_working_day_worked($dateCount, $talentId, $dateTo);
            $previous_working_day_lop = previous_working_day_lop($dateCount, $talentId, $dateTo);
            if (true === $first_time) {
                $first_time = false;
                if (false === $is_current_day_lop && false === $lop_deducted && FALSE === $next_working_day_worked) {
                    // Check if average consider is 1 or 0
                    // if average consider is 0 then get last_day_lop of prev month 
                    // if  last_day_lop of prev month is 1  , then increment lop_count,   $final_day_lop = true
                    // else  $first_time = false;  
                    if ($rowTalentAttance['avg_consider'] == 0) {
                        $sqlLastDayPrev = "SELECT `last_day_lop` "
                                . "FROM `talent_monthly_report` "
                                . "WHERE `talent_id`='$talentId' AND `month`='$prevMonth' AND `year`='$prevMonthYear' ORDER BY `id` DESC";
                        $resultLastDayPrev = mysql_query($sqlLastDayPrev);
                        $rowLastDayPrev = mysql_fetch_array($resultLastDayPrev);
                        if (1 == $rowLastDayPrev['last_day_lop']) {
                            $lop_count++;
                            $final_day_lop = true;
                            $is_current_day_lop = true;
                        }
                    }
                }
            } else {
                if (false == $is_current_day_lop && FALSE === $next_working_day_worked && FALSE == $previous_working_day_lop && FALSE == $lop_deducted) {
                    // Check if average consider is 1 or 0
                    // if 0 then increment lop_count,   $final_day_lop = true     
                    // if 1 then  $previous_working_day_lop = 0 
                    if ($rowTalentAttance['avg_consider'] == 0) {
                        echo $message = 'System applied due to lop' . $dateCount . '<br/>';
                        leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                        $lop_count++;
                        $final_day_lop = true;
                    } else {
                        $previous_working_day_lop = FALSE;
                    }
                }
            }
        }

        // if the date is final day of month 
        //  $final_day_lop === true, then  last_day_lop of current month is 1 
        //  else  last_day_lop of current month is 0

        if ($dateCount == $dateTo) {
            if (true == $final_day_lop) {
                $last_day_lop = 1;
            } else {
                $last_day_lop = 0;
            }
        }
        $lop_deducted = false;

        $dateCount = strtotime("+1 day", strtotime($dateCount));
        $dateCount = date("Y-m-d", $dateCount);
    }
    //calculate the shortage based on the frequency
    $calculateShortage = 1;
    $presentDayTemp = $presentDay;
    if (2 == $row_select_talents['average_hour_calculation']) {
        if (0 == ($month % 3)) {
            /*
             * find the two previous months
             */
            $monthPrev1 = date('m', strtotime(date('Y-m-d') . " -1 month"));
            $yearPrev1 = date('Y', strtotime(date('Y-m-d') . " -1 month"));
            $monthPrev2 = date('m', strtotime(date('Y-m-d') . " -2 month"));
            $yearPrev2 = date('Y', strtotime(date('Y-m-d') . " -2 month"));
            /*
             * find the sum of present days and productive hours of previous month
             */
            $sqlPrev1 = "SELECT `total_present_days`,`hrs_earned` "
                    . "FROM `talent_monthly_report` "
                    . "WHERE `month`='$monthPrev1' AND `year`='$yearPrev1' AND `talent_id`='$talentId' ";
            $resultPrev1 = mysql_query($sqlPrev1);
            $rowPrev1 = mysql_fetch_array($resultPrev1);
            $totalProductiveSeconds += timeToSecondsConvertion($rowPrev1['hrs_earned']);
            $presentDayTemp += $rowPrev1['total_present_days'];
            $sqlPrev2 = "SELECT `total_present_days`,`hrs_earned` "
                    . "FROM `talent_monthly_report` "
                    . "WHERE `month`='$monthPrev2' AND `year`='$yearPrev2' AND `talent_id`='$talentId' ";
            $resultPrev2 = mysql_query($sqlPrev2);
            $rowPrev2 = mysql_fetch_array($resultPrev2);
            $totalProductiveSeconds += timeToSecondsConvertion($rowPrev2['hrs_earned']);
            $presentDayTemp += $rowPrev2['total_present_days'];
        } else if (2 == ($month % 3)) {
            /*
             * find the one previous months
             */
            $monthPrev1 = date('m', strtotime(date('Y-m-d') . " -1 month"));
            $yearPrev1 = date('Y', strtotime(date('Y-m-d') . " -1 month"));
            /*
             * find the sum of present days and productive hours of previous month
             */
            $sqlPrev1 = "SELECT `total_present_days`,`hrs_earned` "
                    . "FROM `talent_monthly_report` "
                    . "WHERE `month`='$monthPrev1' AND `year`='$yearPrev1' AND `talent_id`='$talentId' ";
            $resultPrev1 = mysql_query($sqlPrev1);
            $rowPrev1 = mysql_fetch_array($resultPrev1);
            $totalProductiveSeconds += timeToSecondsConvertion($rowPrev1['hrs_earned']);
            $presentDayTemp += $rowPrev1['total_present_days'];
        } else {
            $calculateShortage = 0;
        }
    }
    //leave due to attendance shortage
    //$totalHourRequired = ($averageHourRequired) * $presentDayTemp;

    if (1 == $calculateShortage) {
        $totalSecondRequired = ($averageHourSeconds) * $presentDayTemp;
        if ($totalSecondRequired > $totalProductiveSeconds) {
            //$hourShortage = $totalHourRequired - $totalProductiveHours;
            //$secondsShortage = ($averageHourRequired * $presentDay * 3600) - $totalProductiveSeconds;

            $secondsShortage = ($totalSecondRequired) - $totalProductiveSeconds;


            if ($secondsShortage >= (3 * 60 * 60)) {
//                    if ($secondsShortage % $averageHourSeconds == 0) {
//                        $shortage = ceil($secondsShortage / $averageHourSeconds) + 2;
//                    } else {
                $shortage = round($secondsShortage / $averageHourSeconds);
//                    }
            }
        }
    }
    if ($roleStream == $managementRolestream || getDefaultValue(27) == 0)
        $shortage = 0;
    for ($i = 1; $i <= $shortage; $i++) {

        $lop_insert_talent = 0;

        $sql_get_compoff = mysql_query("SELECT min(id) as id from  talent_compoff  WHERE used=0 AND temp_used=0 AND talent=$talentId AND date_of_compoff <= '$dateTo'");
        $res_get_compoff = mysql_fetch_row($sql_get_compoff);
        $num_get_compoff = mysql_num_rows($sql_get_compoff);
        if ($num_get_compoff > 0) {
            //var_dump($res_get_compoff_temp);
            if (in_array($res_get_compoff['id'], $idArray)) {
                $compOffAvailable = 0;
            } else {
                $compOffAvailable = 1;
            }
        } else {
            $compOffAvailable = 0;
        }
        $message = 'System Applied due to attendance shortage';
        if ($compOffAvailable > 0) {
            leaveDeduction($talentId, 1, '', $message, 0, $res_get_compoff['id']);
            $compOffAvailable--;
            mysql_query("UPDATE talent_compoff set temp_used=1 where id='" . $res_get_compoff['id'] . "'");
            $res_get_compoff['id'];
            $idArray[] = $res_get_compoff['id'];
            $compOffDeductedShortage++;
        } else if ($elAvailable > 0) {
            leaveDeduction($talentId, 2, '', $message, 0, 0);
            $elAvailable--;
            $elDeductedShortage++;
        } else {
            leaveDeduction($talentId, 0, '', $message, 1, 0);
            $lop_insert_talent = 1;
            $lopByShortage++;
        }
    }

    $sqlTalentLop = "SELECT `lop` "
            . "FROM `talent_lop` "
            . "WHERE `talent`='$talentId'";
    $resultTalentLop = mysql_query($sqlTalentLop);
    $rowTalentLop = mysql_fetch_array($resultTalentLop);

    $lop_count += $rowTalentLop['lop'];

    $sqlUpdateTalentLop = "UPDATE `talent_lop` SET `lop`='0' WHERE `talent`='$talentId'";
    ///mysql_query($sqlUpdateTalentLop);
    //$extraAttendanceHours = $extraAttendanceSeconds / 3600;
    //$extraOdHours = $extraOdSeconds / 3600;
    if ($roleStream == $managementRolestream || getDefaultValue(25) == 0) {
        $encashmentForExtra = 0;
    } else {
        if (($extraAttendanceSeconds + $extraOdSeconds) > 0) {
            $encashmentForExtra += floor(($extraAttendanceSeconds + $extraOdSeconds) / (8 * 60 * 60));
        }
    }
    $extraAttendanceHoursInserting = secondsToMinitues($extraAttendanceSeconds);
    $extraOdHoursInserting = secondsToMinitues($extraOdSeconds);
    $totalExtraHoursInserting = secondsToMinitues($extraAttendanceSeconds + $extraOdSeconds);
    $totalHourRequiredInserting = secondsToMinitues($averageHourSeconds * $presentDay);
    $totalProductiveHoursInserting = secondsToMinitues($totalProductiveSeconds);
    $hourShortageInserting = secondsToMinitues($secondsShortage);
    $sqlTalentLeaveLeft = "SELECT * FROM talent_leave_left WHERE `talent`='$talentId'";
    $resultTalentLeaveLeft = mysql_query($sqlTalentLeaveLeft);
    while ($rowTalentLeaveLeft = mysql_fetch_array($resultTalentLeaveLeft)) {
        if ($rowTalentLeaveLeft['leave_type'] == '1') {
            $compOffLeft = $rowTalentLeaveLeft['leave_left'] - $compOffDeductedProcess - $compOffDeductedShortage;
        }
        if ($rowTalentLeaveLeft['leave_type'] == '2') {
            $elLeft = $rowTalentLeaveLeft['leave_left'] - $elDeductedShortage - $elDeductedProcess;
        }
    }
    //after processing temp variable sets back
    mysql_query("UPDATE talent_compoff set temp_used=0");
    $sqlLeave = "UPDATE `talent_leave_left` SET `leave_left`='0' "
            . "WHERE `talent`='$talentId' ";
    //mysql_query($sqlLeave);
    $sqlInsertData = "INSERT INTO `talent_monthly_report_resigned_temp`(`talent_id`, `month`, `year`, "
            . "`lop_by_process`, `comp_off_deduction_by_process`, `el_deduction_by_process`, "
            . "`lop_by_shortage`, `extra_working_hrs`, `extra_od_hrs`, `total_present_days`, "
            . "`hrs_required`, `hrs_earned`, `hrs_shortage`, `encashment`, "
            . "`comp_off_deduction_by_shorage`, `el_deduction_by_shortage`,`encashment_for_extra`,"
            . "`last_day_lop`,`lop_due_to_lop`,`total_extra_hours`,`comp_off_left`,`el_left`,`days_pay`,`no_of_days_pay`) VALUES "
            . "('$talentId','$month','$year','$lopByProcess','$compOffDeductedProcess','$elDeductedProcess','$lopByShortage',"
            . "'$extraAttendanceHoursInserting','$extraOdHoursInserting','$presentDay',"
            . "'$totalHourRequiredInserting','$totalProductiveHoursInserting','$hourShortageInserting',"
            . "'$encashment','$compOffDeductedShortage','$elDeductedShortage','$encashmentForExtra',"
            . "'$last_day_lop','$lop_count','$totalExtraHoursInserting','$compOffLeft','$elLeft','$days_pay','$no_days_pay')";
    //die();
    $resultInsert = mysql_query($sqlInsertData);
    setEl($talentId, $dateTo, $dateOfJoining);
}

function leaveDeduction($talentId, $leaveType, $dateCount, $message, $lopCount, $compOffId) {
    $sqlLeave = "UPDATE `talent_leave_left` SET `leave_left`=`leave_left`-1 "
            . "WHERE `talent`='$talentId' AND `leave_type`='$leaveType' ";
    //mysql_query($sqlLeave);
    $sqlAttendance = "UPDATE `talent_attendance` "
            . "SET `is_leave`='1' WHERE `talent_id`='$talentId' "
            . "AND `attendance_date`='$dateCount'";
    //mysql_query($sqlAttendance);
    $sqlTalentLeave = "INSERT INTO `talent_leave`(`talent`, `from_date`, `to_date`,"
            . "`considered`,`request`,`lop`,`is_approved`) "
            . "VALUES ('$talentId','$dateCount','$dateCount','1','$message','$lopCount','1')";
    //mysql_query($sqlTalentLeave);
    $id = mysql_insert_id();
    $sqlTalentLeaveDate = "INSERT INTO `talent_leave_date`(`talent_leave`, `date_of_leave`, "
            . "`leave_type`, `is_approved`, `compoff_id`) VALUES ('$id','$dateCount',"
            . "'$leaveType','1','$compOffId')";
    //mysql_query($sqlTalentLeaveDate);
}

function timeToSecondsConvertion($hours) {
    sscanf($hours, "%d:%d:%d", $hours, $minutes, $seconds);
    $time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
    return($time_seconds);
}

function secondsToMinitues($inputSeconds) {
    $hours = floor($inputSeconds / 3600);
    $minutes = floor(($inputSeconds / 60) % 60);
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    $seconds = $inputSeconds % 60;
    if ($seconds < 10) {
        $seconds = '0' . $seconds;
    }
    $inhour = $hours . ':' . $minutes . ':' . $seconds;
    return $inhour;
}

function setEl($talentId, $resignDate, $dateOfJoining) {
    $quarterEl = elAvailableRole($talentId);
    $month_of_resiginig = date('m', strtotime($resignDate));
    $year_of_resiginig = date('Y', strtotime($resignDate));
    if (1 <= $month_of_resiginig && $month_of_resiginig <= 3) {
        $date1 = date_create($resignDate);
        //finds the start date of the talent in the current quarter
        $date1 = date_create($resignDate);
        $date2 = findStart($resignDate, $dateOfJoining, 1, 3);
        $difference = date_diff($date2, $date1);
        $dateDifference = $difference->format("%a");
        $EL = round(($dateDifference / 90) * $quarterEl);
        $dateQuarterEnd = date_create("$year_of_resiginig-03-31");
        $dateQuarterStart = findStart($resignDate, $dateOfJoining, 1, 3);
        $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
        $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
        $elExpected = round(($dateDifferenceToQuarterEnd / 90) * $quarterEl);
    } else if (4 <= $month_of_resiginig && $month_of_resiginig <= 6) {
        $date1 = date_create($resignDate);
        $date2 = findStart($resignDate, $dateOfJoining, 4, 6);
        $difference = date_diff($date2, $date1);
        $dateDifference = $difference->format("%a");
        $EL = round(($dateDifference / 91) * $quarterEl);
        $dateQuarterEnd = date_create("$year_of_resiginig-06-30");
        $dateQuarterStart = findStart($resignDate, $dateOfJoining, 4, 6);
        $dateQuarterStart->format("Y-m-d");
        $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
        $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
        $elExpected = round(($dateDifferenceToQuarterEnd / 91) * $quarterEl);
    } else if (7 <= $month_of_resiginig && $month_of_resiginig <= 9) {
        $date1 = date_create($resignDate);
        $date2 = findStart($resignDate, $dateOfJoining, 7, 9);
        $difference = date_diff($date2, $date1);
        $dateDifference = $difference->format("%a");
        $EL = round(($dateDifference / 92) * $quarterEl);
        $dateQuarterEnd = date_create("$year_of_resiginig-09-30");
        $dateQuarterStart = findStart($resignDate, $dateOfJoining, 7, 9);
        $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
        $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
        $elExpected = round(($dateDifferenceToQuarterEnd / 92) * $quarterEl);
    } else if (10 <= $month_of_resiginig) {
        $date1 = date_create($resignDate);
        $date2 = findStart($resignDate, $dateOfJoining, 10, 12);
        $difference = date_diff($date2, $date1);
        $dateDifference = $difference->format("%a");
        $EL = round(($dateDifference / 92) * $quarterEl);
        $dateQuarterEnd = date_create("$year_of_resiginig-12-31");
        $dateQuarterStart = findStart($resignDate, $dateOfJoining, 10, 12);
        $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
        $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
        $elExpected = round(($dateDifferenceToQuarterEnd / 92) * $quarterEl);
    }
    $updateElActual = mysql_query("UPDATE talent_monthly_report_resigned_temp "
            . "SET `el_actual`='$EL',`el_expected`='$elExpected' "
            . "WHERE `talent_id`='$talentId' ");
    return;
}

function elAvailableRole($talentId) {
    $sqlElQuarter = "SELECT `quarterly_earned_leaves` "
            . "FROM `talents` t "
            . "LEFT JOIN role r on t.role=r.id "
            . "WHERE t.`id`='$talentId'";
    $resultElQuarter = mysql_query($sqlElQuarter);
    $row_qurarter = mysql_fetch_array($resultElQuarter);
    return $row_qurarter['quarterly_earned_leaves'];
}

function findStart($resignDate, $dateOfJoining, $startMonth, $endMonth) {
    //checks is the talent joined in current quarter and resigned in current quarter
    $monthOfResiginig = date('m', strtotime($resignDate));
    $yearOfResiginig = date('Y', strtotime($resignDate));
    $monthOfJoining = date('m', strtotime($dateOfJoining));
    $yearOfJoining = date('Y', strtotime($dateOfJoining));
    if ($yearOfJoining == $yearOfResiginig && $startMonth <= $monthOfJoining && $monthOfJoining <= $endMonth) {
        $startDate = date_create($dateOfJoining);
    } else {
        $startDate = date_create("$yearOfResiginig-$startMonth-01");
    }
    return $startDate;
}

function getDefaultValue($id) {
    $sqlDefaultValue = "SELECT `value` FROM `default_values` WHERE `id`='$id'";
    $resultDefaultValue = mysql_query($sqlDefaultValue);
    $rowDefaultValue = mysql_fetch_array($resultDefaultValue);
    return $rowDefaultValue['value'];
}

function find_next_working_day_worked($dateCount, $talentId, $dateTo) {
    $next_working_day_flag = FALSE;
    //for checking till date after the 5 date of to date for avoiding weekend coming issue
    $dateTo = strtotime("+5 day", strtotime($dateTo));
    $dateTo = date("Y-m-d", $dateTo);
    while (strtotime($dateTo) >= strtotime($dateCount)) {
        $dateCount = strtotime("+1 day", strtotime($dateCount));
        $dateCount = date("Y-m-d", $dateCount);
        $sqlTalentAttendance = "SELECT * "
                . "FROM `talent_attendance` "
                . "WHERE `talent_id`='" . $talentId . "' AND "
                . "attendance_date = '" . $dateCount . "' ";
        $resultTalentAttendance = mysql_query($sqlTalentAttendance);
        if ($rowTalentAttance = mysql_fetch_array($resultTalentAttendance)) {
            //check is leave for the next date
            if ($rowTalentAttance['is_leave'] == 1) {
                return FALSE;
            }
            //check is absent for the next working day
            if ($rowTalentAttance['is_holiday'] == 0) {
                if ($rowTalentAttance['avg_consider'] == 0) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        }
    }
    return TRUE;
}

function previous_working_day_lop($dateCount, $talentId, $dateTo) {
    $next_working_day_flag = FALSE;
    //for checking till date after the 5 date of to date for avoiding weekend coming issue
    $dateTo = strtotime("-5 day", strtotime($dateCount));
    $dateTo = date("Y-m-d", $dateTo);
    while (strtotime($dateTo) <= strtotime($dateCount)) {
        $dateCount = strtotime("-1 day", strtotime($dateCount));
        $dateCount = date("Y-m-d", $dateCount);
        $sqlTalentAttendance = "SELECT is_leave,is_holiday,avg_consider "
                . "FROM `talent_attendance` "
                . "WHERE `talent_id`='" . $talentId . "' AND "
                . "attendance_date = '" . $dateCount . "' ";
        $resultTalentAttendance = mysql_query($sqlTalentAttendance);
        $sqlTalentLeaveType = "SELECT tld.leave_type "
                . "FROM `talent_leave_date` tld LEFT JOIN  talent_leave tl ON tld.talent_leave=tl.id "
                . "WHERE tl.`talent`='" . $talentId . "' AND "
                . "tld.date_of_leave  = '" . $dateCount . "' ";
        $resultTalentLeaveType = mysql_query($sqlTalentLeaveType);
        $rowTalentLeaveType = mysql_fetch_array($resultTalentLeaveType);
        if ($rowTalentAttance = mysql_fetch_array($resultTalentAttendance)) {
            if ($rowTalentAttance['avg_consider'] == 1) {
                return TRUE;
            }
            //check is leave for the next date
            if ($rowTalentAttance['is_leave'] == 1) {
                if ($rowTalentLeaveType['leave_type'] == 0 && $rowTalentAttance['avg_consider'] == 0) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        }
    }
    return TRUE;
}

?>