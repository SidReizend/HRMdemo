<?php

date_default_timezone_set("Asia/Calcutta");
include 'database.php';
//$con = mysql_connect('localhost', 'hrm_123', 'hrm_pass');
//$con = mysql_connect('192.168.0.100', 'hrm', 'hrmpass');
//--------------connecting db-------------//
//$db = mysql_select_db("123_hrm");
//$db = mysql_select_db("hrm_14_02_2017");
//check monthly crone job already done
//if done do not run again else do the crone job 
//$month = date("m");
$year = date("Y");

$startDayOfMonth = getDefaultValue(12);
$endDayOfMonth = getDefaultValue(13);
$managementRolestream = getDefaultValue(28);

$dateFrom = date('Y-m-d', strtotime(getDefaultValue(3)));
$dateTo = date('Y-m-d', strtotime(getDefaultValue(4)));
if (date('Y-m-d') <= $dateTo) {
    die('Date not reached to run');
}

$lastDateOfAttendanceProcessing = getDefaultValue(2);
$lastDateOfAttendanceMonth = $month = date('m', strtotime($lastDateOfAttendanceProcessing));
$month = date('m', strtotime($dateTo));
$currentMonth = date('m');
if ($currentMonth == $lastDateOfAttendanceMonth) {
    die('Job already done');
}

/*
 * check the attendance sheet of endDayOfMonth is uploaded
 */
$result_attendance_uploaded_check = mysql_query("SELECT `attendance_date` "
        . "FROM `talents_attendance_sheet` "
        . "WHERE "
        . "`attendance_date`='" . $dateTo . "' ");
if (mysql_num_rows($result_attendance_uploaded_check) == 0) {
    $message = "Please upload $endDayOfMonth th attendance, monthly process is not done";
    create_notification($message);
    die('upload pending');
}

/*
 * find the od pending to approve in this month and 
 */
$result_od = mysql_query("SELECT `tos`.`talent_od` "
        . "FROM `talent_od_specific` tos "
        . "JOIN `talent_od` `to` ON `to`.`id`=`tos`.`talent_od` "
        . "WHERE `tos`.`date_of_od` BETWEEN '$dateFrom' AND '$dateTo' "
        . "AND `to`.`is_approved`='0' "
        . "GROUP BY `tos`.`talent_od` ");
$result_comp = mysql_query("SELECT t.id "
        . "FROM talent_leave_date tld "
        . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
        . "LEFT JOIN talents t ON t.id=tl.talent "
        . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
        . "LEFT JOIN role r ON (t.role=r.id) "
        . "WHERE tl.is_approved ='0' AND t.role_stream!='$managementRolestream' AND "
        . " `tld`.`date_of_leave` BETWEEN '$dateFrom' AND '$dateTo' ");
$result_leave = mysql_query("SELECT t.id "
        . "FROM talent_leave_date tld "
        . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
        . "LEFT JOIN talents t ON t.id=tl.talent "
        . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
        . "WHERE tl.is_approved ='0' AND "
        . " `tld`.`date_of_leave` BETWEEN '$dateFrom' AND '$dateTo' ");
if (getDefaultValue(26) == 1) {
    $result_enc = mysql_query("SELECT  ta.id FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             LEFT JOIN talents tr ON (tr.id=t.reporting_manager)
             WHERE ta.encashment=0 AND ta.productive_hrs >='13:30:00' AND t.reporting_manager>0 
             AND ta.attendance_date BETWEEN '$dateFrom' AND '$dateTo' 
             ORDER BY ta.attendance_date DESC");
} else {
    $result_enc = 0;
}
if (mysql_num_rows($result_od) > 0 || mysql_num_rows($result_leave) > 0 || mysql_num_rows($result_comp) > 0 || mysql_num_rows($result_enc) > 0) {
    $message = "Please refer pending apprvals, monthly process is not done";
    create_notification($message);
    die('Approvals Pending');
}

$sqlCheckCrone = "SELECT `id` "
        . "FROM `talent_monthly_report` "
        . "WHERE `month`='$currentMonth' AND `year`='$year' ";
$resultCheckCrone = mysql_query($sqlCheckCrone);
if (mysql_numrows($resultCheckCrone) > 0) {
    echo 'job already done';
} else {
    $sql_select_talents = "SELECT talents.`id`,`average_working_hour_per_day`,"
            . "`average_hour_calculation`,`role`.`role_stream`,rs.monthly_process,date_of_joining "
            . "FROM `talents` "
            . "JOIN `role` ON `role`.`id`=`talents`.`role` "
            . "JOIN `role_stream` rs ON `role`.`role_stream`=`rs`.`id` "
            . "WHERE `is_resigned`='0' AND `date_of_joining`<='$dateTo' ORDER BY talents.id asc";
    $result_select_talents = mysql_query($sql_select_talents);
    while ($row_select_talents = mysql_fetch_array($result_select_talents)) {

        $lop_deducted = false;

        $talentId = $row_select_talents['id'];
        $roleStream = $row_select_talents['role_stream'];
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
        $extraAttendanceSeconds = 0;
        $extraOdSeconds = 0;
        $hourShortage = 0;
        $secondsShortage = 0;
        $encashmentForExtra = 0;

        $lopCheckStatus = 0;
        $lop_count = 0;
        $first_time = true;
        $final_day_lop = FALSE;

        $extraAttendanceHoursInserting = 0;
        $extraOdHoursInserting = 0;
        $totalHourRequiredInserting = 0;
        $totalProductiveHoursInserting = 0;
        $hourShortageInserting = 0;
        $prevMonth = date('m', strtotime('-1 month'));
        $currentYear = date('Y');
        $prevMonthYear = $prevMonth == 12 ? ($currentYear - 1) : $currentYear;

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


//if date of join with in the month
        if (strtotime($dateFrom) <= strtotime($row_select_talents['date_of_joining'])) {
            $dateCount = $row_select_talents['date_of_joining'];
        } else {
            $dateCount = $dateFrom;
        }
        while (strtotime($dateTo) >= strtotime($dateCount)) {

            $sqlTalentAttendance = "SELECT * "
                    . "FROM `talent_attendance` "
                    . "WHERE `talent_id`='" . $talentId . "' AND "
                    . "attendance_date = '" . $dateCount . "' ";
            $resultTalentAttendance = mysql_query($sqlTalentAttendance);
            if ($rowTalentAttance = mysql_fetch_array($resultTalentAttendance)) {

                if ($rowTalentAttance['encashment'] == 1) {
                    $encashment++;
                }
                $totalProductiveSeconds += timeToSecondsConvertion($rowTalentAttance['actual_hours_spend']);
                $totalProductiveSeconds += timeToSecondsConvertion($rowTalentAttance['od_hours']);
                /* Condition for a working day is not worked */
                if ($rowTalentAttance['avg_consider'] == 0 && $rowTalentAttance['is_holiday'] == 0 && $rowTalentAttance['is_leave'] == 0) {
                    /* leave status get  0 && 1 exclude */

                    /**
                     * Get Compoff
                     */
                    $lop_insert_talent = 0;

                    $sql_get_compoff = findDateOfCompOffBeforeTheDate($talentId, $dateCount);
                    $res_get_compoff = mysql_fetch_array($sql_get_compoff);
                    $num_get_compoff = mysql_num_rows($sql_get_compoff);
                    if ($num_get_compoff > 0) {
                        $compOffAvailable = 1;
                    } else {
                        $compOffAvailable = 0;
                    }
                    $lop_deducted = true;
                    $message = 'System applied due to absent';
                    if ($row_select_talents['monthly_process'] == 1) {
                        if ($compOffAvailable > 0) {
                            leaveDeduction($talentId, 1, $dateCount, $message, 0, $res_get_compoff['id']);
//$compOffAvailable--;
                            mysql_query("UPDATE talent_compoff set used=1 where id=" . $res_get_compoff['id']);
                            $compOffDeductedProcess++;
                        } else if ($elAvailable > 0) {
                            leaveDeduction($talentId, 2, $dateCount, $message, 0, 0);
                            $elAvailable--;
                            $elDeductedProcess++;
                        } else {
                            leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                            $lop_insert_talent = 1;
                            $lopByProcess++;
                        }
                    }
                } else {
                    if ($rowTalentAttance['avg_consider'] == 1) {
                        $presentDay++;
                    }
                }
                if ($rowTalentAttance['is_holiday'] == 1 || $rowTalentAttance['is_leave'] == 1) {
                    $extraOdSeconds += timeToSecondsConvertion($rowTalentAttance['extra_od_hrs']);
                    $extraAttendanceSeconds += timeToSecondsConvertion($rowTalentAttance['extra_attendance_hrs']);
                    if ($rowTalentAttance['is_leave'] == 1 && (findLeaveTypeOfADate($talentId, $dateCount) == FALSE)) {
                        $lop_count++;
                    }
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
                    //if previous working day isn't lop then no need to consider next day is worked
                    if ($previous_working_day_lop) {
                        $next_working_day_worked = TRUE;
                    }
                    if (false == $is_current_day_lop && FALSE === $next_working_day_worked && FALSE == $previous_working_day_lop && FALSE == $lop_deducted) {

// Check if average consider is 1 or 0
// if 0 then increment lop_count,   $final_day_lop = true     
// if 1 then  $previous_working_day_lop = 0 
                        if ($rowTalentAttance['avg_consider'] == 0) {
                            $message = 'System applied due to lop';
                            leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                            $lop_count++;
                            $final_day_lop = true;
                        } else {
                            $previous_working_day_lop = FALSE;
                        }
                    }
                }
            }

//            if (true == $is_current_day_lop) {
//                $lopCheckStatus = 1;
//            }
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
        if ($roleStream == $managementRolestream || getDefaultValue(27) == 0) {
            $shortage = 0;
        }
//echo $shortage;
        for ($i = 1; $i <= $shortage; $i++) {

            $lop_insert_talent = 0;

            $sql_get_compoff = findDateOfCompOffBeforeTheDate($talentId, $dateCount);
            $res_get_compoff = mysql_fetch_array($sql_get_compoff);
            $num_get_compoff = mysql_num_rows($sql_get_compoff);
            if ($num_get_compoff > 0) {
                $compOffAvailable = 1;
            } else {
                $compOffAvailable = 0;
            }
            $message = 'System Applied due to attendance shortage';
            if ($row_select_talents['monthly_process'] == 1) {
                if ($compOffAvailable > 0) {
                    leaveDeduction($talentId, 1, '', $message, 0, $res_get_compoff['id']);
//$compOffAvailable--;
                    mysql_query("UPDATE talent_compoff set used=1 where id=" . $res_get_compoff['id']);
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
        }

//        $sqlTalentLop = "SELECT `lop` "
//                . "FROM `talent_lop` "
//                . "WHERE `talent`='$talentId'";
//        $resultTalentLop = mysql_query($sqlTalentLop);
//        $rowTalentLop = mysql_fetch_array($resultTalentLop);
//
//        $lop_count += $rowTalentLop['lop'];

        $sqlUpdateTalentLop = "UPDATE `talent_lop` SET `lop`='0' WHERE `talent`='$talentId'";
        mysql_query($sqlUpdateTalentLop);

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
        $sqlInsertData = "INSERT INTO `talent_monthly_report`(`talent_id`, `month`, `year`, "
                . "`lop_by_process`, `comp_off_deduction_by_process`, `el_deduction_by_process`, "
                . "`lop_by_shortage`, `extra_working_hrs`, `extra_od_hrs`, `total_present_days`, "
                . "`hrs_required`, `hrs_earned`, `hrs_shortage`, `encashment`, "
                . "`comp_off_deduction_by_shorage`, `el_deduction_by_shortage`,`encashment_for_extra`,"
                . "`last_day_lop`,`lop_due_to_lop`,`total_extra_hours`) VALUES "
                . "('$talentId','$month','$year','$lopByProcess','$compOffDeductedProcess','$elDeductedProcess','$lopByShortage',"
                . "'$extraAttendanceHoursInserting','$extraOdHoursInserting','$presentDay',"
                . "'$totalHourRequiredInserting','$totalProductiveHoursInserting','$hourShortageInserting',"
                . "'$encashment','$compOffDeductedShortage','$elDeductedShortage','$encashmentForExtra',"
                . "'$last_day_lop','$lop_count','$totalExtraHoursInserting')";
//die();
        $resultInsert = mysql_query($sqlInsertData);
    }
    insertResignedTalentsDetails($month, $year);
    $sqlMonthlyStatus = mysql_query("INSERT INTO monthly_process_status(`date_of_process`,`last_date`) values ('" . date('Y-m-d H:i:s') . "','$dateTo')");
    $sqlDefaultValue = mysql_query("UPDATE default_values SET `value`='" . $dateTo . "' WHERE `id`='2'");

    $sqlDefaultValue = mysql_query("UPDATE default_values SET `value`='" . date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $endDayOfMonth) . " +1 month")) . "' WHERE `id`='4'");
    $sqlDefaultValue = mysql_query("UPDATE default_values SET `value`='" . date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth))) . "' WHERE `id`='3'");

//    mysql_query("ROLLBACK");
}

function leaveDeduction($talentId, $leaveType, $dateCount, $message, $lopCount, $compOffId) {
    $sqlLeave = "UPDATE `talent_leave_left` SET `leave_left`=`leave_left`-1 "
            . "WHERE `talent`='$talentId' AND `leave_type`='$leaveType' ";
    mysql_query($sqlLeave);
    $sqlAttendance = "UPDATE `talent_attendance` "
            . "SET `is_leave`='1' WHERE `talent_id`='$talentId' "
            . "AND `attendance_date`='$dateCount'";
    mysql_query($sqlAttendance);
    $sqlTalentLeave = "INSERT INTO `talent_leave`(`talent`, `from_date`, `to_date`,"
            . "`considered`,`request`,`lop`,`is_approved`) "
            . "VALUES ('$talentId','$dateCount','$dateCount','1','$message','$lopCount','1')";
    mysql_query($sqlTalentLeave);
    $id = mysql_insert_id();
    $sqlTalentLeaveDate = "INSERT INTO `talent_leave_date`(`talent_leave`, `date_of_leave`, "
            . "`leave_type`, `is_approved`, `compoff_id`) VALUES ('$id','$dateCount',"
            . "'$leaveType','1','$compOffId')";
    mysql_query($sqlTalentLeaveDate);
}

function findLeaveTypeOfADate($talentId, $dateCount) {
    $sqlGetLeave = "SELECT * FROM `talent_leave` WHERE "
            . "`talent`='" . $talentId . "' AND `from_date`<='" . $dateCount . "' AND "
            . "`to_date`>='" . $dateCount . "' AND is_approved=1";
    $result = mysql_query($sqlGetLeave);
    if ($row = mysql_fetch_array($result)) {
        $sqlLeaveType = "SELECT * FROM `talent_leave_date` WHERE "
                . "`talent_leave`='" . $row['id'] . "' AND `date_of_leave`='$dateCount'";
        $resultLeaveType = mysql_query($sqlLeaveType);
        if ($rowLeaveType = mysql_fetch_array($resultLeaveType)) {
            if ($rowLeaveType['leave_type'] == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }
    return TRUE;
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

function create_notification($message) {
    return;
//    $sql_insert_notification_admin = mysql_query("INSERT INTO `notifications`(`user`,`message`,`type`,`message_time`) "
//            . "VALUES  ('1','$message','red','" . date('Y-m-d H:i:s') . "')  ");
//
//    $result_managements = mysql_query("SELECT `user` FROM `talents` "
//            . "JOIN `role` ON `talents`.`role`=`role`.`id`"
//            . "WHERE `role`.`role_stream`='4' ");
//    while ($row_managements = mysql_fetch_array($result_managements)) {
//        $sql_insert_notification_management = mysql_query("INSERT INTO `notifications`(`user`,`message`,`type`,`message_time`) "
//                . "VALUES  ('" . $row_managements['user'] . "','$message','red','" . date('Y-m-d H:i:s') . "')  ");
//    }
}

function insertResignedTalentsDetails($month, $year) {
    $sql_resigned = "SELECT * FROM talent_monthly_report_resigned "
            . "WHERE year='$year' AND month='$month'";
    $result_resigned = mysql_query($sql_resigned);
    while ($row_resigned = mysql_fetch_array($result_resigned)) {
        $sqlInsertData = "INSERT INTO `talent_monthly_report`(`talent_id`, `month`, `year`, "
                . "`lop_by_process`, `comp_off_deduction_by_process`, `el_deduction_by_process`, "
                . "`lop_by_shortage`, `extra_working_hrs`, `extra_od_hrs`, `total_present_days`, "
                . "`hrs_required`, `hrs_earned`, `hrs_shortage`, `encashment`, "
                . "`comp_off_deduction_by_shorage`, `el_deduction_by_shortage`,`encashment_for_extra`,"
                . "`last_day_lop`,`lop_due_to_lop`,`total_extra_hours`) "
                . "SELECT `talent_id`, `month`, `year`, "
                . "`lop_by_process`, `comp_off_deduction_by_process`, `el_deduction_by_process`, "
                . "`lop_by_shortage`, `extra_working_hrs`, `extra_od_hrs`, `total_present_days`, "
                . "`hrs_required`, `hrs_earned`, `hrs_shortage`, `encashment`, "
                . "`comp_off_deduction_by_shorage`, `el_deduction_by_shortage`,`encashment_for_extra`,"
                . "`last_day_lop`,`lop_due_to_lop`,`total_extra_hours` FROM talent_monthly_report_resigned "
                . "WHERE `talent_id`='" . $row_resigned['talent_id'] . "' AND year='$year' AND month='$month'";
//die();
        $resultInsert = mysql_query($sqlInsertData);
    }
    return;
}

function getDefaultValue($id) {
    $sqlDefaultValue = "SELECT `value` FROM `default_values` WHERE `id`='$id'";
    $resultDefaultValue = mysql_query($sqlDefaultValue);
    $rowDefaultValue = mysql_fetch_array($resultDefaultValue);
    return $rowDefaultValue['value'];
}

function findDateOfCompOffBeforeTheDate($talentId, $date) {
    return mysql_query("SELECT id from  talent_compoff "
            . "WHERE used=0 AND talent=$talentId "
            . "AND date_of_compoff <= '$date' "
            . "ORDER BY date_of_compoff ASC LIMIT 1");
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
            if ($rowTalentAttance['is_leave'] == 1 && findLeaveTypeOfADate($talentId, $dateCount)) {
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