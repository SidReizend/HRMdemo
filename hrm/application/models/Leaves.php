<?php

Class Leaves extends CI_Model {

    function getMyLeaves($talent_id) {
        $this->db->select('tl.*,count(tld.id) as number');
        $this->db->from('talent_leave tl');
        $this->db->join('talent_leave_date tld', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('talent', $talent_id);
        $this->db->group_by('tl.`id`');
        $this->db->order_by('tl.id', 'desc');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    /*
     * controler : TalentLeaveLeft
     */

    function getMyLeavesLeft($talent_id) {
        $this->db->select('m.type,t.leave_left,m.id');
        $this->db->from('talent_leave_left t');
        $this->db->join('master_leaves m', 't.leave_type=m.id', 'inner');
        $this->db->where('t.talent', $talent_id);
        $this->db->order_by('m.id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMyLopsLeft($talent_id) {
        $this->db->select('lop');
        $this->db->from('talent_lop');
        $this->db->where('talent', $talent_id);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret == "") {
            $res = 0;
        } else {
            $res = $ret->lop;
        }
        return $res;
    }

    function CheckMyLopsLeft($talent_id) {
        $this->db->select('lop');
        $this->db->from('talent_lop');
        $this->db->where('talent', $talent_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function CheckConsidered($id) {
        $this->db->select('considered');
        $this->db->from('talent_leave');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        return $res = $ret->considered;
    }

    function Checktalent($id) {
        $this->db->select('talent');
        $this->db->from('talent_leave');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        return $res = $ret->talent;
    }

    function LopApplied($id) {
        $this->db->select('lop');
        $this->db->from('talent_leave');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        return $res = $ret->lop;
    }

    function splitDateBetweenQuarter($date) {
        $this->db->select('id');
        $this->db->from('master_quarters');
        $this->db->where("'$date' BETWEEN start_month AND end_month");
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret == "") {
            $res = 5;
        } else {
            $res = $ret->id;
        }
        return $res;
    }

    function findEndDateQuarter($id) {
        $this->db->select('end_month');
        $this->db->from('master_quarters');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        return $ret->end_month;
    }

    function findStartDateQuarter($id) {
        $this->db->select('start_month');
        $this->db->from('master_quarters');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        return $ret->start_month;
    }

    /*
     * used in
     * Controller:LeaveApproval
     * 
     */

    function getMyLeavesLeft_byone($talent_id, $leave_type) {
        $this->db->select('leave_left');
        $this->db->from('talent_leave_left');
        $this->db->where('talent', $talent_id);
        $this->db->where('leave_type', $leave_type);
        $query = $this->db->get();
        $ret = $query->row();
        return $ret->leave_left;
    }

    function getcountLeavetype($talent_leave_id) {

        $this->db->select('count(id) as leftt,leave_type');
        $this->db->from('talent_leave_date');
        $this->db->where('talent_leave', $talent_leave_id);
        $this->db->group_by('leave_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler:talentMonthlyReport
     */

    function getLeaveDayStatus($talent_id, $fromDate, $toDate) {
        $categories = array('0', '1');
        $this->db->select('t.date_of_leave,t.leave_type,t.is_approved');
        $this->db->from('talent_leave_date t');
        $this->db->join('talent_leave tl', 'tl.id=t.talent_leave', 'inner');
        $this->db->where('tl.talent', $talent_id);
        $this->db->where_in('t.is_approved', $categories);
        $this->db->where('t.date_of_leave BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $this->db->order_by('t.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler:talentMonthlyReport
     */

    function getMyPlannedLeaves($talent_id, $fromDate,$leaveTye) {
        $categories = array('0', '1');
        $this->db->select('count(*) as number_of_leaves, t.leave_type');
        $this->db->from('talent_leave_date t');
        $this->db->join('talent_leave tl', 'tl.id=t.talent_leave', 'left');
        $this->db->where('tl.talent', $talent_id);
        $this->db->where_in('t.is_approved', $categories);
        $this->db->where('t.date_of_leave >= "' . date('Y-m-d', strtotime($fromDate)) . '"');
        $this->db->where('t.leave_type', $leaveTye);
        $this->db->group_by('t.leave_type');
        $this->db->order_by('t.id', 'desc');
        return $this->db->get()->row();;
    }

    function getWorkingDayStatus($talent_id, $fromDate, $toDate) {
        $this->db->select('attendance_date');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent_id);
        $this->db->where('avg_consider', 1);
        $this->db->where('attendance_date BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMyLeaveById($Id) {
        $this->db->select('tl.id,tl.from_date,tl.to_date,tl.is_approved,tl.request,tl.talent');
        $this->db->from('talent_leave tl');
        $this->db->where('tl.id', $Id);
        $this->db->order_by('tl.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkLeaveCancelled($Id) {
        $this->db->select('is_approved');
        $this->db->from('talent_leave tl');
        $this->db->where('tl.id', $Id);
        $this->db->where('is_approved', 0);
        $query = $this->db->get();
        return count($query->result_array());
    }

    function checkPlannedDate($talent) {
        $categories = array('0', '1');
        $this->db->select('from_date,to_date,is_approved');
        $this->db->from('talent_leave');
        $this->db->where('considered', 0);
        $this->db->where('talent', $talent);
        $this->db->where_in('is_approved', $categories);
        $query = $this->db->get();
        return $query->result_array();
    }

    function createDateRangeArray($strDateFrom, $strDateTo) {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    function getMyLeaveSpecificByLeaveId($leaveId) {
        $this->db->select('id,talent_leave,date_of_leave');
        $this->db->from('talent_leave_date');
        $this->db->where('talent_leave', $leaveId);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getDayStatus($talent_id, $fromDate, $toDate) {
        $this->db->select('tld.date_of_leave');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('tl.talent', $talent_id);
        $this->db->where('tld.date_of_leave BETWEEN "' . date('Y-m-d', strtotime($fromDate)) . '" and "' . date('Y-m-d', strtotime($toDate)) . '"');
        $this->db->order_by('tl.id', 'desc');
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

    public function checkCompExist($talent, $date) {
        $this->db->select('date_of_compoff');
        $this->db->from('talent_compoff');
        $this->db->where('date_of_compoff= ', $date);
        $this->db->where('talent ', $talent);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getDayleaveStatus($dateOfAttendence, $talentId) {
        $this->db->select('tld.date_of_leave');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('tl.talent', $talentId);
        $this->db->where('tld.date_of_leave', $dateOfAttendence);
        $this->db->where('tld.is_approved', 1);
        $query = $this->db->get();

        $leaveDayStatus = $query->num_rows();
        if ($leaveDayStatus >= 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getTalentLeaveKit($talentId) {
        $query = $this->db->query("SELECT ml.type,l.leave_left,ml.id FROM talent_leave_left l LEFT JOIN  master_leaves ml on  (l.leave_type=ml.id) WHERE l.talent='" . $talentId . "' order by ml.type");

        return $query->result_array();
    }

    function getCountLeaveWaiting($talentId) {
        $this->db->select('t.first_name');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'inner');
        $this->db->where('t.reporting_manager', $talentId);
        $this->db->where('tl.is_approved =', 0);
        $this->db->order_by('tl.is_approved', 'desc');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getCountLeaveWaitingTalent($talentId) {
        $this->db->select('t.first_name');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'inner');
        $this->db->where('t.id', $talentId);
        $this->db->where('tl.is_approved =', 0);
        $this->db->order_by('tl.is_approved', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }

    function talentsUnderReportManager($talent) {
        $this->db->select('id,first_name');
        $this->db->from('talents');
        $this->db->where('reporting_manager', $talent);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getPendingLeaveApprovals() {
        $this->db->select('t.first_name,t.middle_name,t.last_name,tr.first_name as tr_first_name,tr.middle_name as tr_middle_name ,tr.last_name as tr_last_name,date_format(tl.from_date,"%d/%m/%Y") as from_date_f,date_format(tl.to_date,"%d/%m/%Y") as to_date_f,tl.request');
        $this->db->from('talent_leave tl ');
        $this->db->join('talents t ', 't.id=tl.talent', 'left');
        $this->db->join('talents tr ', 'tr.id=t.reporting_manager', 'left');
        $this->db->where('tl.is_approved =', 0);
        $this->db->order_by('tl.is_approved', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    /*
     * controler : monthlyCronJob
     */

    function checkCompoffAgainstDate($talent, $date) {
        $this->db->select('date_of_compoff');
        $this->db->from('talent_compoff');
        $this->db->where('date_of_compoff <= ', $date);
        $this->db->where('used ', 0);
        $this->db->where('talent ', $talent);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * controler :  monthlyCronJob
     */

    function leaveDeduction($talentId, $leaveType, $dateCount, $message, $lopCount, $compOffId) {
        $query = $this->db->query("UPDATE `talent_leave_left` SET `leave_left`=`leave_left`-1 "
                . "WHERE `talent`='$talentId' AND `leave_type`='$leaveType' ");
        $query = $this->db->query("UPDATE `talent_attendance` "
                . "SET `is_leave`='1' WHERE `talent_id`='$talentId' "
                . "AND `attendance_date`='$dateCount'");
        $query = $this->db->query("INSERT INTO `talent_leave`(`talent`, `from_date`, `to_date`,"
                . "`considered`,`request`,`lop`,`is_approved`) "
                . "VALUES ('$talentId','$dateCount','$dateCount','1','$message','$lopCount','1')");
        $query = $this->db->query("INSERT INTO `talent_leave_date`(`talent_leave`, `date_of_leave`, "
                . "`leave_type`, `is_approved`, `compoff_id`) VALUES ('$id','$dateCount',"
                . "'$leaveType','1','$compOffId')");
        return;
    }

    /*
     * controler : monthlyCronJob
     */

    function findDateOfCompOffBeforeTheDate($talentId, $date) {
        $query = $this->db->query("SELECT id from  talent_compoff "
                . "WHERE used=0 AND talent=$talentId "
                . "AND date_of_compoff <= '$date' "
                . "ORDER BY date_of_compoff ASC LIMIT 1");
        $result = $query->row();
        if ($result) {
            return $result->id;
        } else {
            return;
        }
    }

    function checkCompoffBetweenDate($fromdate, $todate, $talent) {
        $this->db->select('*');
        $this->db->from('talent_compoff');
        $this->db->where("`date_of_compoff` BETWEEN '$fromdate' AND '$todate'");
        $this->db->where('talent ', $talent);
        $this->db->where('used ', 0);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function minIdInCompoff($talent) {
        $this->db->select('min(id) as id');
        $this->db->from('talent_compoff');
        $this->db->where('used ', 0);
        $this->db->where('talent ', $talent);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret == "") {
            $res = 0;
        } else {
            $res = $ret->id;
        }
        return $res;
    }

    function findCompoffId($talent_leave) {
        $this->db->select('compoff_id');
        $this->db->from('talent_leave_date');
        $this->db->where('talent_leave', $talent_leave);
        $this->db->where('compoff_id!=', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getLeaveTypeName($id) {
        $this->db->select('m.type');
        $this->db->from('master_leaves m');
        $this->db->where('m.id', $id);
        $query = $this->db->get();
        $ret = $query->row();
        if ($ret == "") {
            $res = "LOP";
        } else {
            $res = $ret->type;
        }
        return $res;
    }

    function getDayleaveType($dateOfAttendence, $talentId) {
        $this->db->select('ml.type');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl', 'tl.id=tld.talent_leave', 'left');
        $this->db->join('master_leaves ml', 'ml.id=tld.leave_type', 'left');
        $this->db->where('tl.talent', $talentId);
        $this->db->where('tld.date_of_leave', $dateOfAttendence);
        $query = $this->db->get();
        $ret = $query->row();
        $res = "";
        if ($ret) {
            if ($ret->type == "") {
                $res = "LOP";
            } else {
                $res = $ret->type;
            }
        }
        return $res;
    }

    /*
     * controler: talentAttendanceDetails
     */

    function futureLeaves($from, $to, $talentId) {
        $query = $this->db->query("SELECT ml.type,tld.date_of_leave FROM talent_leave_date tld "
                . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
                . "LEFT JOIN master_leaves ml ON ml.id=tld.leave_type "
                . "WHERE tl.talent='$talentId' AND tld.date_of_leave NOT IN "
                . "(SELECT attendance_date FROM talent_attendance "
                . "WHERE talent_id='$talentId') AND tld.is_approved='1' AND from_date>='$from' AND from_date<='$to' ");
        return $query->result_array();
    }

    public function elAvailableRole($talentId) {
        $query = $this->db->query("SELECT `quarterly_earned_leaves` "
                . "FROM `talents` t "
                . "LEFT JOIN role r on t.role=r.id "
                . "WHERE t.`id`='$talentId' ");
        $result = $query->row();
        return $result ? $result->quarterly_earned_leaves : 0;
    }

    function getExpectedEl($talentId, $resignDate, $dateOfJoining) {
        $quarterEl = $this->elAvailableRole($talentId);
        $month_of_resiginig = date('m', strtotime($resignDate));
        $year_of_resiginig = date('Y', strtotime($resignDate));
        if (1 <= $month_of_resiginig && $month_of_resiginig <= 3) {
            $date1 = date_create($resignDate);
            //finds the start date of the talent in the current quarter
            $date1 = date_create($resignDate);
            $date2 = $this->findStart($resignDate, $dateOfJoining, 1, 3);
            $difference = date_diff($date2, $date1);
            $dateDifference = $difference->format("%a");
            $EL = round(($dateDifference / 90) * $quarterEl);
            $dateQuarterEnd = date_create("$year_of_resiginig-03-31");
            $dateQuarterStart = $this->findStart($resignDate, $dateOfJoining, 1, 3);
            $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
            $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
            $elExpected = round(($dateDifferenceToQuarterEnd / 90) * $quarterEl);
        } else if (4 <= $month_of_resiginig && $month_of_resiginig <= 6) {
            $date1 = date_create($resignDate);
            $date2 = $this->findStart($resignDate, $dateOfJoining, 4, 6);
            $difference = date_diff($date2, $date1);
            $dateDifference = $difference->format("%a");
            $EL = round(($dateDifference / 91) * $quarterEl);
            $dateQuarterEnd = date_create("$year_of_resiginig-06-30");
            $dateQuarterStart = $this->findStart($resignDate, $dateOfJoining, 4, 6);
            $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
            $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
            $elExpected = round(($dateDifferenceToQuarterEnd / 91) * $quarterEl);
        } else if (7 <= $month_of_resiginig && $month_of_resiginig <= 9) {
            $date1 = date_create($resignDate);
            $date2 = $this->findStart($resignDate, $dateOfJoining, 7, 9);
            $difference = date_diff($date2, $date1);
            $dateDifference = $difference->format("%a");
            $EL = round(($dateDifference / 92) * $quarterEl);
            $dateQuarterEnd = date_create("$year_of_resiginig-09-30");
            $dateQuarterStart = $this->findStart($resignDate, $dateOfJoining, 7, 9);
            $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
            $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
            $elExpected = round(($dateDifferenceToQuarterEnd / 92) * $quarterEl);
        } else if (10 <= $month_of_resiginig) {
            $date1 = date_create($resignDate);
            $date2 = $this->findStart($resignDate, $dateOfJoining, 10, 12);
            $difference = date_diff($date2, $date1);
            $dateDifference = $difference->format("%a");
            $EL = round(($dateDifference / 92) * $quarterEl);
            $dateQuarterEnd = date_create("$year_of_resiginig-12-31");
            $dateQuarterStart = $this->findStart($resignDate, $dateOfJoining, 10, 12);
            $differenceToQuarterEnd = date_diff($dateQuarterStart, $dateQuarterEnd);
            $dateDifferenceToQuarterEnd = $differenceToQuarterEnd->format("%a");
            $elExpected = round(($dateDifferenceToQuarterEnd / 92) * $quarterEl);
        }
        return ['el_expected' => $elExpected, 'el_actual' => $EL];
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

    /*
     * controler: talentAttendanceDetails
     */

    function checkPlannedDateFrom($from, $to, $talent) {
        $categories = array('0', '1');
        $this->db->select('from_date,to_date,is_approved');
        $this->db->from('talent_leave');
        $this->db->where('considered', 0);
        $this->db->where('talent', $talent);
        $this->db->where('from_date>=', $from);
        $this->db->where('from_date<=', $to);
        $this->db->where_in('is_approved', $categories);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * view:myleaves
     */

    function leaveCountEachForLeaveId($id) {

        $this->db->select('leave_type');
        $this->db->from('talent_leave_date');
        $this->db->where('talent_leave', $id);
        $query = $this->db->get();
        $result = $query->result_array();
        $countLop = 0;
        $countComp = 0;
        $countEL = 0;
        $countEmergency = 0;
        $countPaternity = 0;
        $countMaternity = 0;
        foreach ($result as $row) {
            if ($row['leave_type'] == '0') {
                $countLop++;
            } else if ($row['leave_type'] == '1') {
                $countComp++;
            } else if ($row['leave_type'] == '2') {
                $countEL++;
            } else if ($row['leave_type'] == '3') {
                $countEmergency++;
            } else if ($row['leave_type'] == '4') {
                $countMaternity++;
            } else if ($row['leave_type'] == '5') {
                $countPaternity++;
            }
        }
        $leves = "";
        if ($countLop > 0) {
            $leves .= " LOP-" . $countLop;
        }
        if ($countComp > 0) {
            $leves .= " Comp-" . $countComp;
        }
        if ($countEL > 0) {
            $leves .= " EL-" . $countEL;
        }
        if ($countEmergency > 0) {
            $leves .= " Emergency-" . $countEmergency;
        }
        if ($countMaternity > 0) {
            $leves .= " Maternity-" . $countMaternity;
        }
        if ($countPaternity > 0) {
            $leves .= " Paternity-" . $countPaternity;
        }
        return $leves;
    }

    /*
     * controller :leaveCreditDetails
     */

    function getLeaveCreditDetailsEL($talent_id) {
        $this->db->select('*');
        $this->db->from('talent_el te');
        $this->db->where('talent', $talent_id);
        $this->db->order_by('te.credicted_on', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getLeaveCreditDetailsComp($talent_id) {
        $this->db->select('*');
        $this->db->from('talent_compoff tc');
        $this->db->where('talent', $talent_id);
        $this->db->order_by('tc.date_of_compOff', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controler :monthlyCronJob
     */

    function lopFinding($talentId, $dateCount) {
        $query = $this->db->query("SELECT tld.`id` "
                . "FROM `talent_leave_date` tld "
                . "JOIN `talent_leave` tl ON tld.talent_leave=tl.id "
                . "WHERE `tld`.`date_of_leave`='$dateCount' "
                . "AND `tl`.`talent`='$talentId' "
                . "AND `tld`.`leave_type`='0' AND tld.`is_approved`='1'");
        return $query->num_rows();
    }

    /*
     * controler :monthlyCronJob
     */

    function lastDayLop($talentId) {
        $query = $this->db->query("SELECT `last_day_lop` "
                . "FROM `talent_monthly_report` "
                . "WHERE `talent_id`='$talentId' ORDER BY `id` DESC");
        if ($query->num_rows()) {
            $result = $query->row();
            return $result->last_day_lop;
        } else {
            return FALSE;
        }
    }

    function getStatusLeaveWaitingForApprovalBetweenDates($startDate, $endDate) {
        $query = $this->db->query("SELECT t.id "
                . "FROM talent_leave_date tld "
                . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
                . "LEFT JOIN talents t ON t.id=tl.talent "
                . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
                . "WHERE tl.is_approved ='0' AND "
                . " `tld`.`date_of_leave` BETWEEN '$startDate' AND '$endDate' ");
        if ($query->num_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getReportingLeaveWaitingForApprovalBetweenDates($startDate, $endDate) {
        $query = $this->db->query("SELECT tr.email "
                . "FROM talent_leave_date tld "
                . "LEFT JOIN talent_leave tl ON tl.id=tld.talent_leave "
                . "LEFT JOIN talents t ON t.id=tl.talent "
                . "LEFT JOIN talents tr ON tr.id=t.reporting_manager "
                . "WHERE tl.is_approved ='0' AND "
                . " `tld`.`date_of_leave` BETWEEN '$startDate' AND '$endDate' "
                . "GROUP BY tr.id");

        return $query->result_array();
    }

    /*
     * controler : monthlyCronJob
     */

    function updateCompOffAsUsed($getMinIdOfCompOffAvailable) {
        $query = $this->db->query("UPDATE talent_compoff set used=1 where id='$getMinIdOfCompOffAvailable'");
        if ($query)
            return TRUE;
    }

    /*
     * controler : monthlyCronJob
     */

    function findLopCount($talentId) {
        $query = $this->db->query("SELECT `lop` "
                . "FROM `talent_lop` "
                . "WHERE `talent`='$talentId'");
        if ($query->num_rows()) {
            $result = $query->row();
            return $result->lop;
        } else {
            return 0;
        }
    }

    /*
     * controler : LeaveApproval
     */

    function checkCompoffPendingBefore($talentId, $date) {
        $query = $this->db->query("SELECT leave_left from  talent_leave_left WHERE talent='$talentId' AND leave_type='1'");
        if ($query->num_rows()) {
            $result = $query->row();
            if ($result->leave_left > 0) {
                $query = $this->db->query("SELECT id from  talent_compoff "
                        . "WHERE used=0 AND talent=$talentId "
                        . "AND date_of_compoff <= '$date' "
                        . "ORDER BY date_of_compoff ASC LIMIT 1");
                if ($query->num_rows()) {
                    $result = $query->row();
                    return $result->id;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }

    /*
     * 
     * Controller : LeaveApproval
     */

    function getAttendanceDetailsById($attendance) {
        $query = $this->db->query("SELECT * FROM talent_attendance WHERE id='$attendance'");
        $result = $query->row();
        return $result;
    }

    /*
     * used in: 
     * Controller: TalentLeaveLeft
     * 
     */

    function getCompOffDetailsById($id) {
        $this->db->select("*");
        $this->db->from("talent_compoff");
        $this->db->where("id", $id);
        $query = $this->db->get();
        return $query->row();
    }

}
