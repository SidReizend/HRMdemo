<?php

Class TimePredictionModel extends CI_Model {

    function HomeGetTotalData($talent, $fromdate, $todate) {
        $this->db->select('*');
        $this->db->from('time_prediction');
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->group_by('date,is_approved');
        $query = $this->db->get();
        return $query->result_array();
    }

    function HomeGetApprovalData($talent) {
        $this->db->select('*');
        $this->db->from('time_prediction');
        $this->db->where('talent', $talent);
        //$this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->where('send_approval', 1);
        $this->db->where('is_approved', 0);
        $this->db->group_by('date');
        $query = $this->db->get();
        return $query->result_array();
    }

    function HomeGetApprovedData($talent, $year, $month) {
        $this->db->select('*');
        $this->db->from('time_prediction');
        $this->db->where('talent', $talent);
        //$this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->where('is_approved', 1);
        if ($month) {
            $this->db->where('MONTH(date)', $month);
        }
        if ($year) {
            $this->db->where('YEAR(date)', $year);
        }
        $this->db->group_by('date');
        $query = $this->db->get();

        //$this->db->last_query();
        return $query->result_array();
    }

    function HomeGetApprovalDataTalents($talent) {
        $this->db->select('tp.*,concat(t.first_name," ",middle_name," ",last_name) as name');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->where_in('tp.talent', $talent);
        $this->db->where('send_approval', 1);
        $this->db->where('is_approved', 0);
        $this->db->group_by('date,tp.talent');
        $query = $this->db->get();
//        echo $this->db->last_query();
        return $query->result_array();
    }

    function HomeGetApprovedDataTalents($talent, $year, $month) {
        $this->db->select('tp.*,t.first_name as name');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->where_in('tp.talent', $talent);
        $this->db->where('is_approved', 1);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->group_by('date,tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentNameById($id) {

        $this->db->select('t.id,t.first_name,t.middle_name,t.last_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,');
        $this->db->from('talents t');

        $this->db->where('t.id', $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data->name;
    }

    function getTalentDetails($id) {

        $this->db->select('t.*,concat(tr.first_name," ",tr.middle_name," ",tr.last_name) as reporting_manager_name');
        $this->db->from('talents t');
        $this->db->join('talents tr', 't.timesheet_manager = tr.id', 'inner');
        $this->db->where('t.id', $id);
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    function pendingTimesheetToSubmit($talents, $datesBetweenDates, $avoidSendToApproval = 0) {

        $dataWithoutList = [];
        $i = 0;
        foreach ($talents as $talent => $name) {
            $talentDetails = $this->getTalentDetails($talent);
            //check talents resign date
            foreach ($datesBetweenDates as $dateAttendance) {
                if (strtotime($talentDetails->date_of_joining) > strtotime($dateAttendance)) {
                    continue;
                }
                if ($talentDetails->is_resigned == 1) {
                    if (strtotime($talentDetails->date_of_resignation) < strtotime($dateAttendance)) {
                        break;
                    }
                }
                $query = $this->db->query("SELECT ta.talent_id,ta.attendance_date,ta.od_hours,ta.actual_hours_spend"
                        . " FROM talent_attendance as ta"
                        . " WHERE ta.attendance_date= '" . $dateAttendance . "' AND talent_id='" . $talent . "' AND"
                        . "(TIME_TO_SEC(`od_hours`) > 0 OR ta.is_leave=1 OR ta.is_holiday=1)");
//                    echo $this->db->last_query();
                $list = $query->row();
                $isHoliday = $this->checkADayIsHoliday($dateAttendance, $talent);
                if (!$list && !$isHoliday) {
                    $timeApplied = $this->getTimeAppliedByDate($dateAttendance, $talent);
                    if ($timeApplied->send_approval == 1 && $avoidSendToApproval) {
                        continue;
                    }
                    $dataWithoutList[$i]['name'] = $name;
                    $dataWithoutList[$i]['date'] = $dateAttendance;
                    $dataWithoutList[$i]['reporting_manager_name'] = $talentDetails->reporting_manager_name;
                    if ($timeApplied) {
                        $dataWithoutList[$i]['apply_status'] = $timeApplied->send_approval == 1 ? 'Send to approval' : 'Apply Pending';
                    } else {
                        $dataWithoutList[$i]['apply_status'] = 'Pending';
                    }
                    $i++;
                }
            }
        }
        return $dataWithoutList;
    }

    public function checkADayIsHoliday($dateOfAttendence, $talentId) {
        $publicHolidayStatus = $this->getDayHolidayStatus($dateOfAttendence);
        $regularHoliday = false;
        $weekends = $this->getHolidayTalent($talentId);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $day = strtolower(date("l", strtotime($dateOfAttendence)));
        $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateOfAttendence);
        if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
            $regularHoliday = TRUE;
        }
        $isHoliday = ($regularHoliday || $publicHolidayStatus) ? TRUE : FALSE;
        return $isHoliday;
    }

    function getHolidayTalent($talent) {
        $this->db->select('m.week_day');
        $this->db->from('talents t');
        $this->db->Join('role_holiday r', 't.role=r.role', 'left');
        $this->db->Join('master_week_days m', 'm.id=r.week_day', 'left');
        $this->db->where('t.id', $talent);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getDayHolidayStatus($dateOfAttendence, $talentId) {

        $this->db->select('*');
        $this->db->from('holidays');
        $this->db->where('is_deleted', '0');
        $this->db->where('date_of_holiday = "' . $dateOfAttendence . '"');
        $query = $this->db->get();

        $holidayStatus = $query->num_rows();
        if ($holidayStatus >= 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function PendingSubmitReport($talent, $fromdate, $todate) {
        if (!empty($talent)) {
            $this->db->select("ta.talent_id,ta.attendance_date,ta.od_hours,ta.actual_hours_spend,concat(t.first_name,' ',t.last_name) as name");
            $this->db->from('talent_attendance ta');
            $this->db->join('talents t', 'ta.talent_id = t.id', 'inner');
            $this->db->where_in('ta.talent_id', $talent);
            $this->db->where("TIME_TO_SEC(ADDTIME(`actual_hours_spend`, `od_hours`)) > 0", NULL, FALSE);
            $this->db->where("ta.attendance_date BETWEEN '$fromdate' AND '$todate'");
            $this->db->order_by('ta.talent_id');
            $query = $this->db->get();
            //echo $this->db->last_query();
            return $query->result_array();
        }
        return array();
    }

    function PendingSubmitReportTalent($talent, $fromdate, $todate) {
        $this->db->select("ta.talent_id,ta.attendance_date,ta.od_hours,ta.actual_hours_spend,concat(t.first_name,' ',t.last_name) as name");
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id = t.id', 'inner');
        $this->db->where('ta.talent_id', $talent);
        $this->db->where("TIME_TO_SEC(ADDTIME(`actual_hours_spend`, `od_hours`)) > 0", NULL, FALSE);
        $this->db->where("ta.attendance_date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    function PendingTalentsheetnumber($talent, $fromdate, $todate) {
        $this->db->select('ta.talent_id');
        $this->db->from('talent_attendance ta');
        $this->db->join('talents t', 'ta.talent_id = t.id', 'inner');
        $this->db->where('ta.talent_id', $talent);
        $this->db->where("TIME_TO_SEC(ADDTIME(`actual_hours_spend`, `od_hours`)) > 0", NULL, FALSE);
        $this->db->where("ta.attendance_date BETWEEN '$fromdate' AND '$todate'");
        $this->db->where('ta.attendance_date NOT IN (SELECT `date` FROM `time_prediction` WHERE talent="' . $talent . '"'
                . ' AND is_approved!=3 AND  date BETWEEN "' . $fromdate . '" AND "' . $todate . '")', NULL, FALSE);
        $query = $this->db->get();
//        echo $this->db->last_query();
        return $query->num_rows();
    }

    function getAllProjectbyDate($talent, $date) {
        $query = $this->db->query('SELECT pm.project_id, p.project_code, p.id FROM project_mapping pm '
                . 'INNER JOIN project p ON pm.project_id = p.id '
                . ' WHERE pm.talent_id = "' . $talent . '" AND pm.date = "' . $date . '" '
                . 'UNION '
                . 'SELECT id as project_id ,project_code,id FROM project '
                . 'WHERE stable_talents LIKE "%' . $talent . ',%"');

        return $query->result_array();
    }

    function getAllProjectbyDateAndToDate($talent, $date) {
        $query = $this->db->query('SELECT pm.project_id, p.project_code, p.id FROM project_mapping pm '
                . 'INNER JOIN project p ON pm.project_id = p.id '
                . ' WHERE pm.talent_id = "' . $talent . '" AND pm.date = "' . $date . '" AND p.to_date>="' . $date . '" '
                . 'UNION '
                . 'SELECT id as project_id ,project_code,id FROM project '
                . 'WHERE (stable_talents LIKE "%,' . $talent . ',%" OR stable_talents LIKE "' . $talent . ',%") AND project.to_date>="' . $date . '" ');

        return $query->result_array();
    }

    function getAllProjectbyDateTalent($talent, $fromdate, $todate) {
        $query = $this->db->query('SELECT pm.project_id, p.project_code, p.id FROM project_mapping pm '
                . 'INNER JOIN project p ON pm.project_id = p.id '
                . ' WHERE pm.talent_id = "' . $talent . '" '
                . 'AND pm.date between "' . $fromdate . '" AND "' . $todate . '" '
                . 'UNION '
                . 'SELECT id as project_id ,project_code,id FROM project '
                . 'WHERE stable_talents LIKE "%' . $talent . ',%"');

        return $query->result_array();
    }

    function getAllTalentbyDate($talent, $date) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,pm.id,pm.rating');
        $this->db->from('project_mapping pm');
        $this->db->join('talents t', 'pm.talent_id = t.id', 'inner');
        $this->db->where('pm.talent_id', $talent);
        $this->db->where('pm.date', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProductivityLogByTalent($talent) {
        $this->db->select('date,productivity');
        $this->db->from('productivity');
        $this->db->where('talent', $talent);
        $this->db->order_by('date desc, id desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllTimeByDate($date, $talent) {
        $this->db->select('tp.id,tp.is_approved,tp.send_approval,tp.task,tp.time,tp.description,tp.date,p.project_code,tp.reason,tp.is_outside_time,t.task_code,t.task_desc,p.allowance');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'left');
        $this->db->join('task_code t', 'tp.task = t.id', 'left');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.replaced!=', 1);
        $this->db->order_by('tp.project DESC,tp.task DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTimeAppliedByDate($date, $talent) {
        $this->db->select('tp.id,tp.is_approved,tp.send_approval,tp.task,tp.time,tp.description,tp.date,p.project_code');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.replaced!=', 1);
        $query = $this->db->get();
        return $query->row();
    }

    function getSumOfTimeTravel($date, $talent) {
        $this->db->select('SUM( TIME_TO_SEC( tp.time ) ) AS time');
        $this->db->from('time_prediction tp');
        $this->db->join('task_code tc', 'tc.id= tp.task', 'inner');
        $this->db->where('tc.is_travel', 1);
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.replaced!=', 1);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret) {
            return $ret->time;
        } else {
            return 0;
        }
    }

    function getSumOfTimeByDate($date, $talent) {
        $this->db->select('SUM( TIME_TO_SEC( tp.time ) ) AS time');
        $this->db->from('time_prediction tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.replaced!=', 1);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret) {
            return $ret->time;
        } else {
            return 0;
        }
    }

    function getAllTimeBeforeApproval($date, $talent) {
        $this->db->select('tp.id,tp.is_approved,tp.send_approval,tp.task,tp.time,tp.description,tp.date,p.project_code');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllTimeByDateTalent($year, $month, $talent) {
        $this->db->select('tp.id,tp.is_approved,tp.task,tp.percentage,tp.description,'
                . 'p.project_code,tp.added_by,tp.project');
        $this->db->from('talent_time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.year', $year);
        $this->db->where('tp.month', $month);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllDataById($id) {
        $this->db->select('*');
        $this->db->from('time_prediction');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllDataByIdTalent($id) {
        $this->db->select('*');
        $this->db->from('talent_time_prediction');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function findActualHourAndOD($talent, $date) {
        $this->db->select('actual_hours_spend,od_hours,in_out_log');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    function CheckActualHourAndOD($talent, $date) {
        $this->db->select('id');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $date);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function sum_time($array) {
        if (count($array) == 1) {
            return $array[0];
        } else {
            $i = 0;
            foreach ($array as $time) {
                sscanf($time, '%d:%d:%d', $hour, $min, $sec);
                $i += ($hour * 60 + $min) * 60 + $sec;
            }
            if ($i == 0) {
                $h = 0;
                $m = 0;
            } else {
                $h = floor($i / 3600);
                $m = floor(($i % 3600) / 60);
                $i = floor(($i % 3600) % 60);
            }

            return sprintf('%02d:%02d:%02d', $h, $m, $i);
        }
    }

    function changeToSecond($time) {
        $time_exp = explode(":", $time);
        return $seconds = ((($time_exp[0] * 60) + $time_exp[1]) * 60) + $time_exp[2];
    }

    function ProjectsOfDate($date, $talent, $is_approved) {
        $this->db->select('p.project_code');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.is_approved', $is_approved);
        $this->db->group_by('tp.project');
        $query = $this->db->get();
        return $query->result_array();
    }

    function TimeOfDate($date, $talent, $is_approved) {
        $this->db->select('time');
        $this->db->from('time_prediction');
        $this->db->where('talent', $talent);
        $this->db->where('date', $date);
        $this->db->where('is_approved', $is_approved);
        $query = $this->db->get();
        return $query->result_array();
    }

    function ReportData($talent, $project, $task, $fromdate, $todate, $track = '') {
        $this->db->select('tp.date,tp.time,tp.description,t.track,'
                . 'p.project_code as project,concat(t.first_name," ",t.middle_name," ",t.last_name) as talent,tc.task_code as task');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'inner');
//        $this->db->where('tp.is_approved', 1);
        if ($talent != "") {
            $this->db->where('tp.talent', $talent);
        }
        if ($track != "") {
            $this->db->where('t.track', $track);
        }
        if ($project != "") {
            $this->db->where('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        if (($fromdate != "") && ($todate != "")) {
            $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectReportDataCollapse($talent, $project, $task, $fromdate, $todate) {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS time,p.project_code as project,tp.project as project_id');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'inner');
        $this->db->where('tp.is_approved', 1);
        if ($talent != "") {
            $this->db->where_in('tp.talent', $talent);
        }
        if ($project != "") {
            $this->db->where('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        if (($fromdate != "") && ($todate != "")) {
            $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        }
        $this->db->group_by('tp.project');
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectsReportDataCollapse($talent, $project, $task, $fromdate, $todate, $track = '', $advanceinvoicefromdate = '', $advanceinvoicetodate = '', $completedinvoicefromdate = '', $completedinvoicetodate = '', $invoicestatus = '') {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS time,p.project_code as project,p.estimated_hours,tp.project as project_id');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'inner');
//        $this->db->where('tp.is_approved', 1);
        if ($talent != "" && $talent != "null") {
            $this->db->where_in('tp.talent', $talent);
        }
        if ($project != "") {
            $this->db->where_in('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where_in('tp.task', $task);
        }
        if ($track != "") {
            $this->db->where('t.track', $track);
        }
        if ($invoicestatus != "") {
            $this->db->where('tc.invoice_status', $invoicestatus);
        }
        if (($fromdate != "") && ($todate != "")) {
            $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        }
        if (($advanceinvoicefromdate != "") && ($advanceinvoicetodate != "")) {
            $this->db->where("tc.advance_invoice_date BETWEEN '$advanceinvoicefromdate' AND '$advanceinvoicetodate'");
        }
        if (($completedinvoicefromdate != "") && ($completedinvoicetodate != "")) {
            $this->db->where("tc.invoice_date BETWEEN '$completedinvoicefromdate' AND '$completedinvoicetodate'");
        }
        $this->db->group_by('tp.project');
        $query = $this->db->get();
        $result = $query->result_array();
//        echo '<pre>';
//        var_dump($result);
        return $result;
    }

    function ProjectReportDataExplode($talent, $project, $task, $fromdate, $todate) {
        $this->db->select('tp.date,tp.time,tp.description,p.estimated_hours,'
                . 'p.project_code as project,concat(t.first_name," ",t.middle_name," ",t.last_name) as talent,tc.task_code as task');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'inner');
        $this->db->where('tp.is_approved', 1);
        if ($talent != "") {
            $this->db->where('tp.talent', $talent);
        }
        if ($project != "") {
            $this->db->where('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        if (($fromdate != "") && ($todate != "")) {
            $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectsReportDataExplode($talent, $project, $task, $fromdate, $todate, $track = '', $advanceinvoicefromdate = '', $advanceinvoicetodate = '', $completedinvoicefromdate = '', $completedinvoicetodate = '', $invoicestatus = '') {
        $this->db->select('tp.date,tp.time,tp.description,'
                . 'p.project_code as project,concat(t.first_name," ",t.middle_name," ",t.last_name) as talent,t.track,'
                . 'tc.task_code as task,tc.task_desc,tc.invoice_status,tc.advance_invoice_date,tc.advance_invoice_number,tc.invoice_date,tc.invoice_number');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'inner');
//        $this->db->where('tp.is_approved', 1);
        if ($talent != "") {
            $this->db->where_in('tp.talent', $talent);
        }
        if ($project != "") {
            $this->db->where_in('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where_in('tp.task', $task);
        }
        if ($track != "") {
            $this->db->where('t.track', $track);
        }
        if (($fromdate != "") && ($todate != "")) {
            $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        }
        if ($invoicestatus != "") {
            $this->db->where('tc.invoice_status', $invoicestatus);
        }
        if (($advanceinvoicefromdate != "") && ($advanceinvoicetodate != "")) {
            $this->db->where("tc.advance_invoice_date BETWEEN '$advanceinvoicefromdate' AND '$advanceinvoicetodate'");
        }
        if (($completedinvoicefromdate != "") && ($completedinvoicetodate != "")) {
            $this->db->where("tc.invoice_date BETWEEN '$completedinvoicefromdate' AND '$completedinvoicetodate'");
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectAmountTalents($project, $fromdate, $todate, $task = "") {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,tp.talent');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->where('tp.project', $project);
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
//        $this->db->where("(tp.is_approved='1' OR tp.send_approval='1')", NULL, FALSE);
        $this->db->group_by('tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectsAmountTalents($project, $fromdate, $todate, $task, $talent, $track = '') {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,tp.talent,t.track');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->where_in('tp.project', $project);
        if ($task != "") {
            $this->db->where_in('tp.task', $task);
        }
        if ($talent != "") {
            $this->db->where_in('t.id', $talent);
        }
        if ($track != "") {
            $this->db->where_in('t.track', $track);
        }
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
//        $this->db->where("(tp.is_approved='1' OR tp.send_approval='1')", NULL, FALSE);
        $this->db->group_by('tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectAmountCal($project, $talent, $fromdate, $todate, $approved, $task = "") {
        $this->db->select('SEC_TO_TIME( SUM( TIME_TO_SEC( `time` ) ) ) AS timeSum ');
        $this->db->from('time_prediction');
        if ($approved == 1) {
            $this->db->where('is_approved', 1);
        } else {
            $this->db->where('send_approval', 1);
        }
        $this->db->where('project', $project);
        if ($task != "") {
            $this->db->where('task', $task);
        }
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectAmountCalProjects($project, $talent, $fromdate, $todate, $approved, $task) {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS timeSum ');
        $this->db->from('time_prediction');
        if ($approved == 1) {
            $this->db->where('is_approved', 1);
        } else {
            $this->db->where_in('is_approved', [0, 3]);
            $this->db->where_in('send_approval', [1, 0]);
        }
        $this->db->where_in('project', $project);
        if ($task != "") {
            $this->db->where_in('task', $task);
        }
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectTimeSpendDay($project, $talent, $fromdate, $todate, $task = "") {
        $this->db->select('`time`,`date`,id,is_approved');
        $this->db->from('time_prediction');
        $this->db->where('(send_approval = 1 OR is_approved=1)');
        $this->db->where('project', $project);
        if ($task != "") {
            $this->db->where('task', $task);
        }
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        return $query->result_array();
    }

    function ProjectTimeSpendDayProjects($project, $talent, $fromdate, $todate, $task) {
        $this->db->select('t.`time`,`date`,t.id,t.is_approved,p.project_code,tc.task_code');
        $this->db->from('time_prediction t');
        $this->db->join('project p', 't.project=p.id', 'left');
//        $this->db->where('(t.send_approval = 1 OR t.is_approved=1)');
        $this->db->join('task_code tc', 't.task = tc.id', 'inner');
        if ($project)
            $this->db->where_in('t.project', $project);
        if ($task != "") {
            $this->db->where_in('task', $task);
        }
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * view:TimePrediction/PredictopmAjax
     */

    function TalentTimeSpend($talent, $fromdate, $todate) {
        $this->db->select('p.project_code,t.`project`,p.sub_project,p.billable');
        $this->db->from('time_prediction t');
        $this->db->join('project p', 't.project=p.id', 'left');
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->group_by("t.project");
        $query = $this->db->get();
        $projects = $query->result_array();
        //find the time spend for each day and its cost for each product
        $projectAndProductivity = array();
        foreach ($projects as $project) {
            $loopDate = $fromdate;
            $productivityProject = 0;
            $billableTime = 0;
            $projectTimeSpend = array();
            $projectBillableTimeSpend = array();
            $projectNonBillableTimeSpend = array();
            while (strtotime($loopDate) <= strtotime($todate)) {
                //find the productivity of the date
                $productivty = $this->TimePredictionModel->productivity($talent, $loopDate);
                $timeSpend = $this->TimePredictionModel->ProjectTimeSpendOnDay($project['project'], $talent, $loopDate);
                //finds the billable time spend

                /*
                 *  if a project have sub project then consider it's task for checking billable or not
                 *  if no sub project then check the project is billable or not
                 */

                if ($project['sub_project'] == 0) {
                    if ($project['billable'] == 1) {
                        $billableTimeSpend = $timeSpend;
                        $nonBillableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    } else {
                        $nonBillableTimeSpend = $timeSpend;
                        $billableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    }
                } else {
                    $billableTimeSpend = $this->TimePredictionModel->ProjectBillableTimeSpendOnDay($project['project'], $talent, $loopDate);
                    $nonBillableTimeSpend = $this->TimePredictionModel->ProjectNonBillableTimeSpendOnDay($project['project'], $talent, $loopDate);
                }
                $projectTimeSpend[] = $timeSpend;
                $timeSpend = $this->TimePredictionModel->decimalHours($timeSpend);
                $productivityProject = $productivityProject + ($productivty * $timeSpend);
                $projectBillableTimeSpend[] = $billableTimeSpend;
                $projectNonBillableTimeSpend[] = $nonBillableTimeSpend;
                $loopDate = date("Y-m-d", strtotime("+1 day", strtotime($loopDate)));
            }
            $projectAndProductivity[] = array(
                'project_code' => $project['project_code'],
                'productivity' => $productivityProject,
                'time' => $this->TimePredictionModel->sum_time($projectTimeSpend),
                'billable_time' => $this->TimePredictionModel->sum_time($projectBillableTimeSpend),
                'non_billable_time' => $this->TimePredictionModel->sum_time($projectNonBillableTimeSpend),
            );
        }
        $price = array();
        foreach ($projectAndProductivity as $key => $row) {
            $price[$key] = $row['productivity'];
        }
        array_multisort($price, SORT_DESC, $projectAndProductivity);
        return $projectAndProductivity;
    }

    /*
     * view:TimePrediction/PredictopmAjax
     */

    function talentTimeSpendGroupByTask($talent, $fromdate, $todate, $project) {
        $this->db->select('t.task,tc.task_code');
        $this->db->join('task_code tc', 'tc.id=t.task', 'left');
        $this->db->from('time_prediction t');
        $this->db->where_in('project', $project);
        $this->db->where('talent', $talent);
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->group_by("t.task");
        $query = $this->db->get();
        $tasks = $query->result_array();
        //find the time spend for each day and its cost for each product
        $projectAndProductivity = array();
        foreach ($tasks as $task) {
            $loopDate = $fromdate;
            $productivityProject = 0;
            $taskTimeSpend = array();
            while (strtotime($loopDate) <= strtotime($todate)) {
                //find the productivity of the date
                $productivty = $this->TimePredictionModel->productivity($talent, $loopDate);
                //find the time spend for the task in this date
                $timeSpend = $this->TimePredictionModel->taskTimeSpendOnDay($task['task'], $talent, $loopDate, $project);
                $taskTimeSpend[] = $timeSpend;
                $timeSpend = $this->TimePredictionModel->decimalHours($timeSpend);
                $productivityProject = $productivityProject + ($productivty * $timeSpend);
                $loopDate = date("Y-m-d", strtotime("+1 day", strtotime($loopDate)));
            }
            $taskAndProductivity[] = array(
                'task_code' => $task['task_code'],
                'productivity' => $productivityProject,
                'time' => $this->TimePredictionModel->sum_time($taskTimeSpend),
            );
        }
        $price = array();
        foreach ($taskAndProductivity as $key => $row) {
            $price[$key] = $row['productivity'];
        }
        array_multisort($price, SORT_DESC, $taskAndProductivity);
        return $taskAndProductivity;
    }

    /*
     * model: TimePredictionModel
     */

    function taskTimeSpendOnDay($task, $talent, $date, $project) {
        $this->db->select('t.`time`');
        $this->db->from('time_prediction t');
        $this->db->where('talent', $talent);
        $this->db->where('task', $task);
        $this->db->where_in('project', $project);
        $this->db->where('date', $date);
        $query = $this->db->get();
        $time = $query->result_array();
        $timeSpendDecimal = 0;
        $time_day = array();
        foreach ($time as $timeSpend) {
            $time_day[] = $timeSpend['time'];
        }
        return $this->TimePredictionModel->sum_time($time_day);
    }

    /*
     * model: TimePredictionModel
     */

    function ProjectTimeSpendOnDay($project, $talent, $date) {
        $this->db->select('t.`time`');
        $this->db->from('time_prediction t');
        $this->db->where('talent', $talent);
        $this->db->where('project', $project);
        $this->db->where('date', $date);
        $query = $this->db->get();
        $time = $query->result_array();
        $timeSpendDecimal = 0;
        $time_day = array();
        foreach ($time as $timeSpend) {
            $time_day[] = $timeSpend['time'];
        }
        return $this->TimePredictionModel->sum_time($time_day);
    }

    /*
     * model:TimePredictionModel
     */

    function ProjectBillableTimeSpendOnDay($project, $talent, $date) {
        $this->db->select('t.`time`');
        $this->db->from('time_prediction t');
        $this->db->join('task_code tc', 'tc.id=t.task', 'left');
        $this->db->where('talent', $talent);
        $this->db->where('project', $project);
        $this->db->where('tc.billable', 1);
        $this->db->where('date', $date);
        $query = $this->db->get();
        $time = $query->result_array();
        $timeSpendDecimal = 0;
        $time_day = array();
        foreach ($time as $timeSpend) {
            $time_day[] = $timeSpend['time'];
        }
        return $this->TimePredictionModel->sum_time($time_day);
    }

    /*
     * model:TimePredictionModel
     */

    function ProjectNonBillableTimeSpendOnDay($project, $talent, $date) {
        $this->db->select('t.`time`');
        $this->db->from('time_prediction t');
        $this->db->join('task_code tc', 'tc.id=t.task', 'left');
        $this->db->where('talent', $talent);
        $this->db->where('project', $project);
        $this->db->where('tc.billable', 0);
        $this->db->where('date', $date);
        $query = $this->db->get();
        $time = $query->result_array();
        $timeSpendDecimal = 0;
        $time_day = array();
        foreach ($time as $timeSpend) {
            $time_day[] = $timeSpend['time'];
        }
        return $this->TimePredictionModel->sum_time($time_day);
    }

    function CheckApprovedOrNot($date, $talent) {
        $this->db->select('*');
        $this->db->from('time_prediction tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        //$this->db->where('tp.is_approved', 1);
        $this->db->where("(tp.is_approved='1' OR tp.send_approval='1')", NULL, FALSE);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function CheckApprovedOrNotTalent($talent, $year, $month) {
        $this->db->select('*');
        $this->db->from('talent_time_prediction tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.year', $year);
        $this->db->where('tp.month', $month);
        $this->db->where('tp.is_approved', 1);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->num_rows();
    }

    function getProjectTask($project) {
        $this->db->select('*');
        $this->db->from('task_code');
        $this->db->where_in('id', $project);
        $query = $this->db->get();
        return $query->result_array();
    }

    function TalentCheckauto($talent) {
        $this->db->select('*');
        $this->db->from('talents');
        $this->db->where('id', $talent);
        $this->db->where('autoTimeApproval', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function ApprovalStatus($talent, $date) {
        $this->db->select('send_approval,is_approved');
        $this->db->from('time_prediction tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.replaced!=', 1);
        $this->db->where("(tp.is_approved='1' OR tp.send_approval='1')", NULL, FALSE);
        $query = $this->db->get();
        //echo $this->db->last_query()."<br>";
        return $query->result_array();
    }

    function decimalHours($time) {
        $hms = explode(":", $time);
        $time = ($hms[0] + ($hms[1] / 60) + ($hms[2] / 3600));
        return number_format((float) $time, 2, '.', '');
    }

    function HoursDecimal($time) {
        $hms = explode(".", $time);
        $min = $hms[1] * 60;
        $time = $hms[0] . "." . $min;
        $time = number_format((float) $time, 2, '.', '');
        return str_replace(".", ":", $time);
    }

    function Productivity($talent, $date) {
        $this->db->select('productivity');
        $this->db->from('productivity');
        $this->db->where('talent', $talent);
        $this->db->where('date<=', $date);
        $this->db->order_by('date DESC,id DESC');
        $this->db->limit(1);
        $query = $this->db->get();
//        $this->db->last_query();
        $ret = $query->row();
        if ($ret) {
            return $ret->productivity;
        } else {
            return;
        }
    }

    function ClaimAmount($project, $status, $task = "") {
        $this->db->select('sum(amount) as amount');
        $this->db->from('claims');
        $this->db->where('project', $project);
        if ($task != "") {
            $this->db->where('task_code', $task);
        }
        $this->db->where('status', $status);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->row();
        if ($ret == "") {
            return 0;
        } else {
            return $ret->amount;
        }
    }

    function ClaimAmountProjects($project, $status, $task, $fromdate, $todate, $talent) {
        $this->db->select('sum(amount) as amount');
        $this->db->from('claims');
        $this->db->where_in('project', $project);
        if ($task != "") {
            $this->db->where_in('task_code', $task);
        }
        if (!empty($talent)) {
            $this->db->where_in('talent', $talent);
        }
        if ($fromdate != "") {
            $this->db->where('date_of_approval>=', $fromdate);
        }
        if ($todate != "") {
            $this->db->where('date_of_approval<=', $todate);
        }
        $this->db->where('status', $status);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->row();
        if ($ret == "") {
            return 0;
        } else {
            return $ret->amount;
        }
    }

    function ClaimsUnderProjects($project, $status, $task, $fromdate, $todate, $talent) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'c.settlement,concat(p.project_code,"-",p.name) as project,c.category,'
                . 'c.amount,c.date_of_approval,mc.value,'
                . 'concat(a.first_name," ",a.middle_name," ",a.last_name) as approved_by,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'c.status,c.id,c.note');
        $this->db->from('claims c');
        $this->db->join('talents t', 'c.talent=t.id', 'left');
        $this->db->join('master_claim_category mc', 'c.category=mc.id', 'left');
        $this->db->join('talents a', 'c.approved_by=a.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->where_in('p.id', $project);
        if ($fromdate != "") {
            $this->db->where('date_of_approval>=', $fromdate);
        }
        if (!empty($talent)) {
            $this->db->where_in('c.talent', $talent);
        }
        if ($todate != "") {
            $this->db->where('date_of_approval<=', $todate);
        }
        if ($task != "") {
            $this->db->where_in('c.task_code', $task);
        }
        $this->db->where('c.status', $status);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->result_array();
        return $ret;
    }

    function TransUnderProjects($project, $status, $task, $fromdate, $todate) {
        $this->db->select('company_name,concat(p.project_code,"-",p.name) as project,'
                . 'c.debit_amount as amount,c.date_of_approval,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'c.id,c.note');
        $this->db->from('transaction_tracking c');
        $this->db->join('company cy', 'c.company=cy.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->where_in('p.id', $project);
        if ($task != "") {
            $this->db->where_in('c.task_code', $task);
        }
        if ($fromdate != "") {
            $this->db->where('date_of_approval>=', $fromdate);
        }
        if ($todate != "") {
            $this->db->where('date_of_approval<=', $todate);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->result_array();
        return $ret;
    }

    function transactionAmount($project, $status, $task = "") {
        $this->db->select('sum(amount) as amount');
        $this->db->from('transaction_tracking');
        $this->db->where('project', $project);
        if ($task != "") {
            $this->db->where('task_code', $task);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->row();
        if ($ret == "") {
            return 0;
        } else {
            return $ret->amount;
        }
    }

    function transactionAmountProjects($project, $status, $task, $fromdate, $todate) {
        $this->db->select('sum(debit_amount) as amount');
        $this->db->from('transaction_tracking');
        $this->db->where_in('project', $project);
        if ($task != "") {
            $this->db->where_in('task_code', $task);
        }
        if ($fromdate != "") {
            $this->db->where('date_of_approval>=', $fromdate);
        }
        if ($todate != "") {
            $this->db->where('date_of_approval<=', $todate);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        $ret = $query->row();
        if ($ret == "") {
            return 0;
        } else {
            return $ret->amount;
        }
    }

    /*
     * controller:TimePredictionModel
     */

    function getProjectTaskTimeByTalentDate($talent, $date) {
        $this->db->select('t.`time`,t.task,t.project,p.sub_project,p.billable as project_billable,tc.billable as task_billable');
        $this->db->from('time_prediction t');
        $this->db->join('project p', 'p.id=t.project', 'left');
        $this->db->join('task_code tc', 'tc.id=t.task', 'left');
        $this->db->where('talent', $talent);
        $this->db->where('date', $date);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    /*
     * controller:TimePredictionCntrl
     */

    function talentBillabilityBetweenDates($talent, $fromDate, $toDate) {

        $loopDate = $fromDate;
        /*
         * for each date
         */
        $dateAndProductivity = array();
        while (strtotime($loopDate) <= strtotime($toDate)) {
            /*
             * finds Productivity of the date
             */
            $productivty = $this->TimePredictionModel->productivity($talent, $loopDate);
            /*
             * finds the project and tasks of the date
             */
            $productivityDate = 0;
            $billableProductivityDate = 0;
            $nonBillableProductivityDate = 0;
            $dateTimeSpend = array();
            $dateBillableTimeSpend = array();
            $dateNonBillableTimeSpend = array();
            $projectTaskTimes = $this->TimePredictionModel->getProjectTaskTimeByTalentDate($talent, $loopDate);
            foreach ($projectTaskTimes as $projectTaskTimesSpecific) {
                $timeSpend = $projectTaskTimesSpecific['time'];
                if ($projectTaskTimesSpecific['sub_project'] == 0) {
                    if ($projectTaskTimesSpecific['project_billable'] == 1) {
                        $billableTimeSpend = $timeSpend;
                        $nonBillableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    } else {
                        $nonBillableTimeSpend = $timeSpend;
                        $billableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    }
                } else {
                    if ($projectTaskTimesSpecific['task_billable'] == 1) {
                        $billableTimeSpend = $timeSpend;
                        $nonBillableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    } else {
                        $nonBillableTimeSpend = $timeSpend;
                        $billableTimeSpend = date('H:i:s', strtotime('00:00:00'));
                    }
                }
                $dateTimeSpend[] = $timeSpend;
                $timeSpend = $this->TimePredictionModel->decimalHours($timeSpend);
                $productivityDate = $productivityDate + ($productivty * $timeSpend);
                $dateBillableTimeSpend[] = $billableTimeSpend;
                $billableTimeSpend = $this->TimePredictionModel->decimalHours($billableTimeSpend);
                $billableProductivityDate = $billableProductivityDate + ($productivty * $billableTimeSpend);
                $dateNonBillableTimeSpend[] = $nonBillableTimeSpend;
                $nonBillableTimeSpend = $this->TimePredictionModel->decimalHours($nonBillableTimeSpend);
                $nonBillableProductivityDate = $nonBillableProductivityDate + ($productivty * $nonBillableTimeSpend);
            }
            $dateAndProductivity[] = array(
                'date' => date('d/m/Y', strtotime($loopDate)),
                'productivity' => $productivty,
                'productivity_date' => $productivityDate,
                'time' => $this->TimePredictionModel->sum_time($dateTimeSpend),
                'billable_time' => $this->TimePredictionModel->sum_time($dateBillableTimeSpend),
                'billable_productivity' => $billableProductivityDate,
                'non_billable_time' => $this->TimePredictionModel->sum_time($dateNonBillableTimeSpend),
                'non_billable_productivity' => $nonBillableProductivityDate,
            );
            /*
             * finds the next date
             */
            $loopDate = date("Y-m-d", strtotime("+1 day", strtotime($loopDate)));
        }

        return $dateAndProductivity;
    }

    function projectsAmountTasks($project, $fromdate, $todate, $task, $talent) {

        $this->db->select('tp.task as task, tc.task_code as task_name,tcp.task_code as parent_task,'
                . 'pp.project_code as parent_project, tc.task_desc as task_desc');
        $this->db->from('time_prediction tp');
        $this->db->join('task_code tc', 'tp.task = tc.id', 'left');
        $this->db->join('task_code tcp', 'tc.parent_task = tcp.id', 'left');
        $this->db->join('project pp', 'tc.parent_project = pp.id', 'left');
        $this->db->join('talents t', 'tp.talent = t.id', 'left');
        $this->db->where_in('tp.project', $project);
        if ($task != "") {
            $this->db->where_in('tp.task', $task);
        }
        if ($talent != "") {
            $this->db->where_in('t.id', $talent);
        }
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
//        $this->db->where("(tp.is_approved='1' OR tp.send_approval='1')", NULL, FALSE);
        $this->db->group_by('tp.task');
        $query = $this->db->get();
        return $query->result_array();
    }

    function projectTaskAmountCalProjects($project, $task, $talent, $fromdate, $todate) {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS timeSum ');
        $this->db->from('time_prediction');
        $this->db->where_in('project', $project);
        $this->db->where_in('task', $task);
        if ($talent != "") {
            $this->db->where_in('talent', $talent);
        }
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsByTask($task, $fromdate, $todate, $talent) {
        $this->db->select('tp.talent as talent, CONCAT(t.first_name, " ", t.middle_name, " ", t.last_name) as talent_name');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'inner');
        $this->db->where('tp.task', $task);
        if ($talent != "") {
            $this->db->where_in('talent', $talent);
        }
        $this->db->where("date BETWEEN '$fromdate' AND '$todate'");
        $this->db->group_by('tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getDeployDate($task_id) {
        $this->db->select('*');
        $this->db->from('task_code');
        $this->db->where('id', $task_id);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row()->deploy_date;
        } else {
            return false;
        }
    }

    /*
     * get the task total time 
     */

    function projectTaskTotalTime($task) {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS timeSum ');
        $this->db->from('time_prediction');
        $this->db->where_in('task', $task);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get talent Allowance list  count lost
     */

    function gettalentAllowance($year, $month) {
        $this->db->select('t.id,concat(t.first_name," ",t.middle_name," ",t.last_name) as talent,SUM(food_allowance = 1) as count_foodallowance, SUM(food_andtravel_allowance = 1) as count_food_andtravel_allowance ');
        $this->db->from('talent_allowance tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'left');
        $this->db->where('MONTH(tp.date)', $month);
        $this->db->where('YEAR(tp.date)', $year);
        $this->db->where('tp.approved', 1);
        $this->db->group_by('tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get talent detailed Allowance list  view details click
     */

    function gettalentDetailedAllowance($year, $month, $talent) {
        $this->db->select('t.id,concat(t.first_name," ",t.middle_name," ",t.last_name) as talent,tp.food_allowance,tp.food_andtravel_allowance,date');
        $this->db->from('talent_allowance tp');
        $this->db->join('talents t', 'tp.talent = t.id', 'left');
        $this->db->where('MONTH(tp.date)', $month);
        $this->db->where('YEAR(tp.date)', $year);
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.approved', 1);
        $this->db->order_by('tp.date', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get allowance details selected  date and talent
     */

    function getAllowanceByDate($date, $talent) {
        $this->db->select('*');
        $this->db->from('talent_allowance tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.send_approval', 1);
        $query = $this->db->get();
        return $query->row();
    }

    /*
     * get approved allowance details by date and talent
     */

    function getApprovedAllowanceByDate($date, $talent) {
        $this->db->select('*');
        $this->db->from('talent_allowance tp');
        $this->db->where('tp.talent', $talent);
        $this->db->where('tp.date', $date);
        $this->db->where('tp.approved', 1);
        $query = $this->db->get();
        return $query->row();
    }

    /*
     * get invoice Status
     */

    function getSelectedInvoiceStatuses($id) {
        $this->db->select('status');
        $this->db->from('master_invoice_status');
        $this->db->where('status_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllProjects($project) {
        $this->db->select('id, name, project_code');
        $this->db->from('project');
        $this->db->where('is_head', 0);
        $this->db->where("name LIKE '%$project%' OR project_code LIKE '%$project%'");
        $query = $this->db->get();
        return $query->result_array();
    }

}
