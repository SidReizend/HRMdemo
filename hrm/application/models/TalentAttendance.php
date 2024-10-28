<?php

Class TalentAttendance extends CI_Model {

    function getTalentAttendanceDetails($from, $to, $talent_id) {
        $this->db->select('attendance_date, is_holiday, is_leave, avg_consider');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent_id);
        $this->db->where('attendance_date >= ', $from);
        $this->db->where('attendance_date <= ', $to);
        $this->db->order_by('attendance_date', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function statistics($monthYearReport, $talent_id) {

        $presentDay = 0;
        $totalAbsent = 0;
        $totalProductiveSeconds = "0";
        $averageSecondsAcheived = 0;
        $hourShortage = 0;
        $extraSeconds = 0;
        $travelingSeconds = 0;
        foreach ($monthYearReport as $reportSpecific) {
            $totalProductiveSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($reportSpecific['productive_hrs']);
            $travelingSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($reportSpecific['travel_time']);
            if ($reportSpecific['avg_consider'] > 0) {
                $presentDay++;
            } else {
                if ($reportSpecific['is_holiday'] != 1 && $reportSpecific['is_leave'] != 1) {
                    $totalAbsent++;
                }
            }
        }
        $averageHourObject = $this->talent->getAverageHour($talent_id);
        $averageHourInt = isset($averageHourObject->average_working_hour_per_day) ? $averageHourObject->average_working_hour_per_day : '';
        $averageHourRequired = $averageHourInt;
        $totalPrdouctiveHour = $this->talentAttendenceSheet->secondsToMinitues($totalProductiveSeconds);
        $averageHourSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($averageHourInt);
        $totalSecondsRequired = $averageHourSeconds * $presentDay;
        $totalHourRequired = $this->talentAttendenceSheet->secondsToMinitues($totalSecondsRequired);
        if ($totalProductiveSeconds < ($totalSecondsRequired)) {
            $secondsShortage = abs($totalProductiveSeconds - $totalSecondsRequired);
            $hourShortage = $this->talentAttendenceSheet->secondsToMinitues($secondsShortage);
        }
        if (($totalProductiveSeconds - $travelingSeconds) > ($totalSecondsRequired)) {
            $extraSeconds = $totalProductiveSeconds- $travelingSeconds - $totalSecondsRequired;
        }

        if ($totalProductiveSeconds) {
            $averageSecondsAcheived = $totalProductiveSeconds / $presentDay;
        }

        $extraHours = $this->talentAttendenceSheet->secondsToMinitues($extraSeconds);
        $data['travelHours'] = $this->talentAttendenceSheet->secondsToMinitues($travelingSeconds);
        $data['presentDay'] = $presentDay;
        $data['averageProductiveHours'] = gmdate("H:i:s", $averageSecondsAcheived);
        $data['monthYearReport'] = $monthYearReport;
        $data['totalAbsent'] = $totalAbsent;
        $data['averageHourRequired'] = $averageHourRequired;
        $data['hourShortage'] = $hourShortage;
        $data['extraHours'] = $extraHours;
        $data['totalPrdouctiveHour'] = $totalPrdouctiveHour;
        $data['totalHourRequired'] = $totalHourRequired;

        return $data;
    }

    public function getMonthlyReportResult($month, $year) {
        $this->db->select('id');
        $this->db->from('talent_monthly_report');
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return true;
        } else {
            return false;
        }
    }

    public function getMonthlyReportById($id) {
        $this->db->select('*');
        $this->db->from('talent_monthly_report');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getReportOfResignedTalents($month, $year) {
        $this->db->select('*');
        $this->db->from('talent_monthly_report_resigned');
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function generateMonthlyReportOfTalent($dateFrom, $dateTo, $talent) {
        die('Not using');
        $talentDetails = $this->talent->getTalentById($talent);
        $talentId = $talent;
        $averageHourRequired = $talentDetails->average_working_hour_per_day;
        $lop_deducted = false;

        $talentId = $row_select_talents['id'];
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

        $extraAttendanceHoursInserting = 0;
        $extraOdHoursInserting = 0;
        $totalHourRequiredInserting = 0;
        $totalProductiveHoursInserting = 0;
        $hourShortageInserting = 0;

        $averageHourRequired = $row_select_talents['average_working_hour_per_day'];
        $averageHourSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($averageHourRequired);
        //el left finding
        $talentLeaveLeftCalculation = $this->leaves->getMyLeavesLeft($talent);
        foreach ($talentLeaveLeftCalculation as $leaves) {
            if ($leaves['id'] == '2') {
                $elAvailable = $leaves['leave_left'];
            }
        }
        //starts report generation from start date to end date
        $month = date('m');
        $year = date('Y');
        $dateCount = $dateFrom;
        while (strtotime($dateTo) >= strtotime($dateCount)) {
            //check the entry exists for the talent for a date
            $rowTalentAttance = $this->talentAttendance->talentAttendanceForDateTalent($dateCount, $talent);
            if ($rowTalentAttance) {
                if ($rowTalentAttance->encashment == 1) {
                    $encashment++;
                }
                if ($rowTalentAttance->avg_consider == 0 &&
                        $rowTalentAttance->is_holiday == 0 &&
                        $rowTalentAttance->is_leave == 0) {
                    /* leave status get  0 && 1 exclude */

                    /**
                     * Get Compoff
                     */
                    $lop_insert_talent = 0;

                    $sql_get_compoff = mysql_query("SELECT min(id) as id from  talent_compoff  WHERE used=0 AND talent=$talentId AND date_of_compoff <= '$dateCount'");
                    $res_get_compoff = mysql_fetch_row($sql_get_compoff);
                    $num_get_compoff = mysql_num_rows($sql_get_compoff);
                    if ($num_get_compoff > 0) {
                        $compOffAvailable = 0;
                    } else {
                        $compOffAvailable = 1;
                    }
                    $lop_deducted = true;
                    $message = 'System Applied due to absent';
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
                } else {
                    if ($rowTalentAttance['avg_consider'] == 1) {
                        $presentDay++;
                        $totalProductiveSeconds += timeToSecondsConvertion($rowTalentAttance['productive_hrs']);
                    }
                }
                if ($rowTalentAttance['is_holiday'] == 1 || $rowTalentAttance['is_leave'] == 1) {
                    $extraOdSeconds += timeToSecondsConvertion($rowTalentAttance['extra_od_hrs']);
                    $extraAttendanceSeconds += timeToSecondsConvertion($rowTalentAttance['extra_attendance_hrs']);
                }
            }
        }
    }

    public function talentAttendanceForDateTalent($date, $talent) {
        $query = $this->db->query("SELECT * "
                . "FROM `talent_attendance` "
                . "WHERE `talent_id`='" . $talent . "' AND "
                . "attendance_date = '" . $date . "' ");
        return $query->row();
    }

    public function talentAttendanceForDateTalentReturnArray($date, $talent) {
        $query = $this->db->query("SELECT * "
                . "FROM `talent_attendance` "
                . "WHERE `talent_id`='" . $talent . "' AND "
                . "attendance_date = '" . $date . "' ");
        return $query->result_array();
    }

    public function checkAttendanceUploadedForDate($date) {
        $query = $this->db->query("SELECT `attendance_date` "
                . "FROM `talents_attendance_sheet` "
                . "WHERE "
                . "`attendance_date`='$date' ");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * controler : MonthlyCronJob
     */

    public function createNotificationInTable($message, $id) {
        $query = $this->db->query("INSERT INTO `notifications`(`user`,`message`,`type`,`message_time`) "
                . "VALUES  ($id,'$message','red','" . date('Y-m-d H:i:s') . "')  ");
        if ($query)
            return TRUE;
    }

    /*
     * controler : MonthlyCronJob
     */

//    public function getStatusCompWaitingForApprovalBetweenDates($startDate, $endDate) {
//        $roleStream = $this->defaultValues->getValue(28);
//        $query = $this->db->query("SELECT t.id "
//                . "FROM talent_leave_date tld "
//                . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
//                . "LEFT JOIN talents t ON t.id=tl.talent "
//                . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
//                . "LEFT JOIN role r ON (t.role=r.id) "
//                . "WHERE tl.is_approved ='0' AND avg_consider=1 AND r.role_stream!='$roleStream' AND "
//                . " `tld`.`date_of_leave` BETWEEN '$startDate' AND '$endDate' ");
//        if ($query->num_rows()) {
//            return TRUE;
//        } else {
//            return FALSE;
//        }
//    }
    
    
    public function getStatusCompWaitingForApprovalBetweenDates($startDate, $endDate) {
        $roleStream = $this->defaultValues->getValue(28);
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,concat(tr.first_name," ",tr.middle_name," ",tr.last_name) as tr_name,date_format(ta.attendance_date,"%d/%m/%Y") as attendance_date_f');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id=t.id', 'left');
        $this->db->join('role r', 't.role=r.id', 'left');
        $this->db->join('talents tr', 'tr.id=t.reporting_manager', 'left');
        $this->db->where("(ta.is_holiday='1' or ta.is_leave='1')");
        $this->db->where("(ta.actual_hours_spend !=0 or ta.od_hours !='0')");
        $this->db->where('ta.holiday_hr_status', 0);
        $this->db->where('ta.avg_consider', 1);
        $this->db->where('ta.attendance_date>=', $startDate);
        $this->db->where('ta.attendance_date<=', $endDate);
        $this->db->where("r.role_stream != '$roleStream'");
        $query = $this->db->get();
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function timesheetApprovalPending($startDate, $endDate) {
        $query = $this->db->query("SELECT t.id "
                . "FROM time_prediction t "
                . "WHERE t.send_approval ='1' AND (t.is_approved ='0' OR t.is_approved ='3') AND "
                . " `t`.`date` BETWEEN '$startDate' AND '$endDate' LIMIT 1");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * controler : MonthlyCronJob
     */

    public function getReportingCompWaitingForApprovalBetweenDates($startDate, $endDate) {
        $roleStream = $this->defaultValues->getValue(28);
        $query = $this->db->query("SELECT tr.email "
                . "FROM talent_leave_date tld "
                . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
                . "LEFT JOIN talents t ON t.id=tl.talent "
                . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
                . "LEFT JOIN role r ON (t.role=r.id) "
                . "WHERE tl.is_approved ='0' AND r.role_stream!='$roleStream' AND "
                . " `tld`.`date_of_leave` BETWEEN '$startDate' AND '$endDate' "
                . "GROUP BY tr.id");
        return $query->result_array();
    }

    /*
     * controler : MonthlyCronJob
     */

    public function getStatusEncashmentWaitingForApprovalBetweenDates($startDate, $endDate) {
        $roleStream = $this->defaultValues->getValue(28);
        $query = $this->db->query("SELECT  ta.id FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             LEFT JOIN talents tr ON (tr.id=t.reporting_manager)
             LEFT JOIN role r ON (t.role=r.id)
             WHERE ta.encashment=0 AND r.role_stream!='$roleStream' AND ta.productive_hrs >='13:30:00' AND t.reporting_manager>0 
             AND ta.attendance_date BETWEEN '$startDate' AND '$endDate' 
             ORDER BY ta.attendance_date DESC");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * controler : MonthlyCronJob
     */

    public function getReportingEncashmentWaitingForApprovalBetweenDates($startDate, $endDate) {
        $roleStream = $this->defaultValues->getValue(28);
        $query = $this->db->query("SELECT  tr.email FROM talent_attendance ta 
             LEFT JOIN talents t ON (ta.talent_id=t.id)
             LEFT JOIN talents tr ON (tr.id=t.reporting_manager)
             LEFT JOIN role r ON (t.role=r.id)
             WHERE ta.encashment=0 AND t.role_stream!='$roleStream'  AND ta.productive_hrs >='13:30:00' AND t.reporting_manager>0 
             AND ta.attendance_date BETWEEN '$startDate' AND '$endDate' 
             GROUP BY tr.id
             ORDER BY ta.attendance_date DESC ");
        return $query->result_array();
    }

    /*
     * controler : MonthlyCronJob
     */

    public function checkJobAlreadyDone($month, $year) {
        $query = $this->db->query("SELECT `id` "
                . "FROM `talent_monthly_report` "
                . "WHERE `month`='$month' AND `year`='$year' ");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * controler : MonthlyCronJob
     */

    public function hourEarnedForMonthYearTalent($month, $year, $talentId) {
        $query = $this->db->query("SELECT `total_present_days`,`hrs_earned` "
                . "FROM `talent_monthly_report` "
                . "WHERE `month`='$month' AND `year`='$year' AND `talent_id`='$talentId' ");
        if ($query->num_rows()) {
            $result = $query->row();
            return $result->hrs_earned;
        } else {
            return 0;
        }
    }

    /*
     * controler : MonthlyCronJob
     */

    public function presentDayForMonthYearTalent($month, $year, $talentId) {
        $query = $this->db->query("SELECT `total_present_days`,`hrs_earned` "
                . "FROM `talent_monthly_report` "
                . "WHERE `month`='$month' AND `year`='$year' AND `talent_id`='$talentId' ");
        if ($query->num_rows()) {
            $result = $query->row();
            return $result->total_present_days;
        } else {
            return 0;
        }
    }

}
