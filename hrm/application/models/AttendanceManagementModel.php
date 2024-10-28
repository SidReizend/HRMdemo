<?php

Class AttendanceManagementModel extends CI_Model {

    public function doResignProcess($talentId, $startDate, $dateOfResignation) {
        /* resign process start date and end date */
        $endDate = $dateOfResignation;
        /* Talent details by talent id */
        $talentDeatails = $this->AttendanceManagementModel->getTalentWithRoleStreamDetails($talentId);


        /* Initializing variables */

        $result['encashment'] = 0;
        $result['compOffDeductedProcess'] = 0;
        $result['elDeductedProcess'] = 0;
        $result['lopByProcess'] = 0;
        $result['presentDay'] = 0;
        $result['totalProductiveSeconds'] = 0;
        $result['extraOdSeconds'] = 0;
        $result['extraAttendanceSeconds'] = 0;
        $result['firstTime'] = TRUE;
        $result['isCurrentDayLeave'] = false;

        $result['finalDayLop'] = $this->AttendanceManagementModel->checkFinalDayIsLop();
        $this->AttendanceManagementModel->calculateReportOfTalet($talentId, $startDate, $endDate, $talentDeatails, $result);
    }

    public function calculateReportOfTalet($talentId, $startDate, $endDate, $talentDeatails, $result) {
        $dateCount = $startDate;
        //for every date calculate the report
        while (strtotime($dateTo) >= strtotime($dateCount)) {

            $attendanceDetailsOfTheDate = $this->talentAttendance->talentAttendanceForDateTalent($talentId, $dateCount);

            $result = $this->AttendanceManagementModel->processTheDateStatus($talentId, $dateCount, $talentDeatails, $result, $attendanceDetailsOfTheDate);

            $dateCount = strtotime("+1 day", strtotime($dateCount));
            $dateCount = date("Y-m-d", $dateCount);
            if (1 == $talentDeatails->monthly_process) {
                $this->AttendanceManagementModel->processTheLopDueToLop($talentId, $dateCount, $endDate, $talentDeatails, $result, $attendanceDetailsOfTheDate);
            }
        }
    }

    public function processTheLopDueToLop($talentId, $dateCount, $endDate, $talentDeatails, $result, $attendanceDetailsOfTheDate) {

        $isCurrentDayAlreadyLeave = $this->AttendanceManagementModel->findCurrentDayAlreadyLeave($dateCount, $talentId);

        $nextWorkingDayWorked = $this->AttendanceManagementModel->findNextWorkingDayWorked($dateCount, $talentId);
        $previousWorkingDayWorked = $this->AttendanceManagementModel->findPreviousWorkingDayWorked($dateCount, $talentId);

        
        if (TRUE === $result['firstTime']) {
            if (false === $isCurrentDayAlreadyLeave && FALSE === $next_working_day_worked && 0 == $attendanceDetailsOfTheDate->avg_consider) {
                
            }
        }
        //check current date is leave
    }

    public function findCurrentDayAlreadyLeave($dateCount, $talentId) {
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

    public function findNextWorkingDayWorked($dateCount, $talentId) {
        $dateAfterAPeriod = strtotime("+5 day", strtotime($dateCount));
        while (strtotime($dateAfterAPeriod) >= strtotime($dateCount)) {
            $dateCount = strtotime("+1 day", strtotime($dateCount));
            $attendanceDetailsOfTheDate = $this->talentAttendance->talentAttendanceForDateTalent($talentId, $dateCount);
            if (1 == $attendanceDetailsOfTheDate->avg_consider) {
                return TRUE;
            } else if (0 == $attendanceDetailsOfTheDate->is_holiday) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function findPreviousWorkingDayWorked($dateCount, $talentId) {
        $dateAfterAPeriod = strtotime("-5 day", strtotime($dateCount));
        while (strtotime($dateAfterAPeriod) <= strtotime($dateCount)) {
            $dateCount = strtotime("-1 day", strtotime($dateCount));
            $attendanceDetailsOfTheDate = $this->talentAttendance->talentAttendanceForDateTalent($talentId, $dateCount);
            if (1 == $attendanceDetailsOfTheDate->avg_consider) {
                return TRUE;
            } else if (0 == $attendanceDetailsOfTheDate->is_holiday) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function processTheDateStatus($talentId, $dateCount, $talentDeatails, $result, $attendanceDetailsOfTheDate) {
        /* Attendance details of a date */
        if ($attendanceDetailsOfTheDate) {
            //if approved encashment exist then increase it's countt
            if (1 == $attendanceDetailsOfTheDate->encashment) {
                $result['encashment'] ++;
            }
            //if a day is working day and not worked then
            if (0 == $attendanceDetailsOfTheDate->avg_consider && 0 == $attendanceDetailsOfTheDate->is_holiday && 0 == $attendanceDetailsOfTheDate->is_leave) {
                //then set the date as leave if the talent need to undergo monthly process
                if (1 == $talentDeatails->monthly_process) {
                    $this->AttendanceManagementModel->deductAvailableLeave($talentId, $dateCount, $result);
                }
            } else if (1 == $attendanceDetailsOfTheDate->avg_consider) {
                $result['presentDay'] ++;
                $result['totalProductiveSeconds'] += $this->TalentAttendenceSheet->hourToSecondsConvertion($attendanceDetailsOfTheDate->productive_hrs);
            }
            if (1 == $attendanceDetailsOfTheDate->is_holiday || 1 == $attendanceDetailsOfTheDate->is_leave) {
                $result['extraOdSeconds'] += $this->TalentAttendenceSheet->hourToSecondsConvertion($attendanceDetailsOfTheDate->extra_od_hrs);
                $result['extraAttendanceSeconds'] += $this->TalentAttendenceSheet->hourToSecondsConvertion($attendanceDetailsOfTheDate->extra_attendance_hrs);
            }
        }
        return $result;
    }

    public function deductAvailableLeave($talentId, $dateCount, $result) {
        //message for leave deduction
        $message = 'System applied due to absent';
        //find compoff available
        $compOffAvailable = $this->leaves->checkCompoffPendingBefore($talentId, $dateCount);
        $elAvailable = $this->AttendanceManagementModel->getElAvailable($talentId);
        if ($compOffAvailable != 0) {
            //deduct compoff
            $result['compOffDeductedProcess'] ++;
            $this->AttendanceManagementModel->deductLeave($talentId, 2, $dateCount, $message, 0, 0);
        } else if ($elAvailable->leave_left) {
            $result['elDeductedProcess'] ++;
        } else {
            $this->AttendanceManagementModel->deductLeave($talentId, 0, $dateCount, $message, 1, 0);
            $result['lopByProcess'] ++;
        }
    }

    public function deductLeave($talentId, $leaveType, $dateCount, $message, $lop, $compOffId) {
        //reduce leave count
        $this->db->set('leave_left', 'leave_left-1', FALSE);
        $this->db->where('talent', $talentId);
        $this->db->where('leave_type', $leaveType);
        $this->db->update('talent_leave_left');

        //update attendance table as leave
        $this->db->set('is_leave', 1);
        $this->db->where('talent_id', $talentId);
        $this->db->where('attendance_date', $dateCount);
        $this->db->update('talent_attendance');

        //insert a leave
        $insertData = array(
            `talent` => $talentId,
            `from_date` => $dateCount,
            `to_date` => $dateCount,
            `considered` => 1,
            `request` => $message,
            `lop` => $lop,
            `is_approved` => 1,
        );
        $this->db->insert('talent_leave', $insertData);

        $insert_id = $this->db->insert_id();

        //insert talent leave date 
        $insertData = array(
            `talent_leave` => $insert_id,
            `date_of_leave` => $dateCount,
            `leave_type` => $leaveType,
            `is_approved` => 1,
            `compoff_id` => $compOffId,
        );
        $this->db->insert('talent_leave_date', $insertData);
    }

    public function getTalentWithRoleStreamDetails($talentId) {
        $this->db->select('talents.`id`,`average_working_hour_per_day`,`average_hour_calculation`,`r`.`role_stream`,rs.monthly_process');
        $this->db->from('talents t');
        $this->db->join('role r', 'r.id=t.role', 'left');
        $this->db->join('role_stream rs', 'rs.id=r.role_stream', 'left');
        $this->db->where('t.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getElAvailable($talentId) {
        $this->db->select('leave_left');
        $this->db->from('talent_leave_left');
        $this->db->where('talent', $talentId);
        $this->db->where('leave_type', 2);
        $query = $this->db->get();
        return $query->row();
    }

}
